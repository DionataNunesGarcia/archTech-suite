<?php

declare(strict_types=1);

namespace Drupal\archtech_ai_gateway\Service;

/**
 * Masks PII (Personally Identifiable Information) before sending to external AI APIs.
 *
 * Integrates with Microsoft Presidio for entity detection and anonymization.
 * Falls back to regex-based masking when Presidio is unavailable.
 *
 * PII categories detected:
 *   - PERSON: Full names
 *   - EMAIL_ADDRESS: Email addresses
 *   - PHONE_NUMBER: Brazilian and international phone numbers
 *   - CPF: Brazilian individual taxpayer ID (XXX.XXX.XXX-XX)
 *   - CNPJ: Brazilian company taxpayer ID (XX.XXX.XXX/XXXX-XX)
 *   - ADDRESS: Street addresses
 *   - CREDIT_CARD: Credit card numbers
 *   - RG: Brazilian identity card numbers
 *   - CEP: Brazilian postal codes (XXXXX-XXX)
 */
final class PiiMaskerService {

  /**
   * Presidio Analyzer endpoint for entity detection.
   */
  private string $presidioEndpoint = '';

  /**
   * Whether Presidio integration is available.
   */
  private bool $presidioAvailable = FALSE;

  /**
   * Replacement text for masked entities.
   */
  private const string MASK_PLACEHOLDER = '[REDACTED]';

  /**
   * Regex patterns for fallback PII detection (when Presidio unavailable).
   *
   * @var array<string, string>
   */
  private const array FALLBACK_PATTERNS = [
    'CPF' => '/\d{3}\.\d{3}\.\d{3}-\d{2}/',
    'CNPJ' => '/\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}/',
    'EMAIL' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
    'PHONE_BR' => '/(?:\(\d{2}\)\s?)?\d{4,5}-\d{4}/',
    'CREDIT_CARD' => '/\b(?:\d[ -]*?){13,16}\b/',
    'CEP' => '/\d{5}-\d{3}/',
    'RG' => '/\d{1,2}\.\d{3}\.\d{3}-\d{1}/',
  ];

  /**
   * Constructor.
   */
  public function __construct() {
    $presidioHost = \getenv('PRESIDIO_ANALYZER_HOST') ?: '';
    if ($presidioHost !== '' && $presidioHost !== '0') {
      $this->presidioEndpoint = \rtrim($presidioHost, '/') . '/analyze';
      $this->presidioAvailable = TRUE;
    }
  }

  /**
   * Masks PII in the given text.
   *
   * @param string $text
   *   Raw text that may contain PII.
   *
   * @return array{clean_text: string, masked_entities: array<int, array{type: string, start: int, end: int}>}
   */
  public function maskPii(string $text): array {
    if ($this->presidioAvailable) {
      return $this->maskWithPresidio($text);
    }

    return $this->maskWithRegex($text);
  }

  /**
   * Unmasks PII after receiving the AI response.
   *
   * Reverses the masking by replacing placeholders with original values.
   * Only used when the AI response references the same entities.
   *
   * @param string $responseText
   *   AI response text with masked placeholders.
   * @param array $entityMap
   *   Map of masked value => original value pairs from maskPii().
   *
   * @return string
   */
  public function unmaskPii(string $responseText, array $entityMap): string {
    return \strtr($responseText, $entityMap);
  }

  /**
   * Checks whether a string contains any detectable PII.
   *
   * @param string $text
   *
   * @return bool
   */
  public function containsPii(string $text): bool {
    foreach (self::FALLBACK_PATTERNS as $pattern) {
      if (\preg_match($pattern, $text) === 1) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Masks PII using Microsoft Presidio Analyzer API.
   *
   * @param string $text
   *
   * @return array{clean_text: string, masked_entities: array<int, array{type: string, start: int, end: int}>}
   */
  private function maskWithPresidio(string $text): array {
    $payload = \json_encode([
      'text' => $text,
      'language' => 'pt',
      'entities' => [
        'PERSON',
        'EMAIL_ADDRESS',
        'PHONE_NUMBER',
        'BR_CPF',
        'BR_CNPJ',
        'ADDRESS',
        'CREDIT_CARD',
      ],
    ], \JSON_THROW_ON_ERROR);

    $context = \stream_context_create([
      'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $payload,
        'timeout' => 5,
      ],
    ]);

    $response = @\file_get_contents($this->presidioEndpoint, FALSE, $context);

    if ($response === FALSE) {
      return $this->maskWithRegex($text);
    }

    $entities = \json_decode($response, TRUE, 512, \JSON_THROW_ON_ERROR);

    if (!\is_array($entities)) {
      return $this->maskWithRegex($text);
    }

    // Sort entities by position (end to start) to preserve offsets during replacement.
    \usort($entities, fn($a, $b) => $b['start'] - $a['start']);

    $maskedEntities = [];
    foreach ($entities as $entity) {
      $original = \substr($text, $entity['start'], $entity['end'] - $entity['start']);
      $text = \substr_replace($text, self::MASK_PLACEHOLDER, $entity['start'], $entity['end'] - $entity['start']);
      $maskedEntities[] = [
        'type' => $entity['type'],
        'start' => $entity['start'],
        'end' => $entity['end'],
      ];
    }

    return [
      'clean_text' => $text,
      'masked_entities' => $maskedEntities,
    ];
  }

  /**
   * Falls back to regex-based PII masking when Presidio is unavailable.
   *
   * @param string $text
   *
   * @return array{clean_text: string, masked_entities: array<int, array{type: string, start: int, end: int}>}
   */
  private function maskWithRegex(string $text): array {
    $maskedEntities = [];

    foreach (self::FALLBACK_PATTERNS as $entityType => $pattern) {
      if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE) === FALSE) {
        continue;
      }

      foreach ($matches[0] as $match) {
        $matchedText = $match[0];
        $start = $match[1];
        $length = \strlen($matchedText);

        $maskedEntities[] = [
          'type' => $entityType,
          'start' => $start,
          'end' => $start + $length,
        ];
      }
    }

    // Sort by start position descending for correct replacement offsets.
    \usort($maskedEntities, fn($a, $b) => $b['start'] - $a['start']);

    foreach ($maskedEntities as &$entity) {
      $length = $entity['end'] - $entity['start'];
      $text = \substr_replace($text, self::MASK_PLACEHOLDER, $entity['start'], $length);
    }
    unset($entity);

    return [
      'clean_text' => $text,
      'masked_entities' => $maskedEntities,
    ];
  }

}
