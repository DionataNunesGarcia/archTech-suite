<?php

declare(strict_types=1);

namespace Drupal\archtech_ai_gateway\Service;

/**
 * Moderates AI-generated content using OpenAI Moderation API.
 *
 * Checks outputs for harmful content categories before serving to users.
 * Categories: hate, harassment, self-harm, sexual, violence, illegal activity.
 *
 * Configuration:
 *   - MODERATION_ENABLED: env var, 'true' to enable (default: false)
 *   - MODERATION_THRESHOLD: 0.0–1.0, block content above this score (default: 0.7)
 *   - MODERATION_ACTION: 'block' | 'flag' | 'rewrite' (default: 'block')
 */
final class ContentModerationService {

  /**
   * OpenAI Moderation API endpoint.
   */
  private const string MODERATION_ENDPOINT = 'https://api.openai.com/v1/moderations';

  /**
   * Categories monitored for moderation.
   *
   * @var string[]
   */
  private const array MODERATION_CATEGORIES = [
    'hate',
    'hate/threatening',
    'harassment',
    'harassment/threatening',
    'self-harm',
    'self-harm/intent',
    'self-harm/instructions',
    'sexual',
    'sexual/minors',
    'violence',
    'violence/graphic',
  ];

  /**
   * Whether content moderation is enabled.
   */
  private bool $enabled = FALSE;

  /**
   * Score threshold above which content is flagged/blocked.
   */
  private float $threshold = 0.7;

  /**
   * Action to take on moderated content: block, flag, or rewrite.
   */
  private string $action = 'block';

  /**
   * Constructor.
   */
  public function __construct() {
    $this->enabled = \getenv('MODERATION_ENABLED') === 'true';
    $threshold = \getenv('MODERATION_THRESHOLD');
    if ($threshold !== FALSE && $threshold !== '') {
      $this->threshold = (float) $threshold;
    }
    $action = \getenv('MODERATION_ACTION') ?: 'block';
    if (\in_array($action, ['block', 'flag', 'rewrite'], TRUE)) {
      $this->action = $action;
    }
  }

  /**
   * Moderates AI-generated content.
   *
   * @param string $content
   *   The AI-generated text to check.
   *
   * @return array{approved: bool, action: string, flagged_categories: array<string, float>, moderated_content: string|null}
   *
   * @throws \RuntimeException
   *   When content is blocked by the moderation policy.
   */
  public function moderate(string $content): array {
    if (!$this->enabled) {
      return [
        'approved' => TRUE,
        'action' => 'passthrough',
        'flagged_categories' => [],
        'moderated_content' => NULL,
      ];
    }

    $scores = $this->callModerationApi($content);

    $flaggedCategories = [];
    foreach (self::MODERATION_CATEGORIES as $category) {
      $score = $scores[$category] ?? 0.0;
      if ($score >= $this->threshold) {
        $flaggedCategories[$category] = $score;
      }
    }

    if ($flaggedCategories === []) {
      return [
        'approved' => TRUE,
        'action' => 'passthrough',
        'flagged_categories' => [],
        'moderated_content' => NULL,
      ];
    }

    switch ($this->action) {
      case 'block':
        throw new \RuntimeException(
          \sprintf(
            'Content blocked by moderation policy. Flagged categories: %s',
            \implode(', ', \array_keys($flaggedCategories)),
          ),
        );

      case 'rewrite':
        $safeContent = $this->rewriteContent($content);
        return [
          'approved' => TRUE,
          'action' => 'rewrite',
          'flagged_categories' => $flaggedCategories,
          'moderated_content' => $safeContent,
        ];

      case 'flag':
      default:
        return [
          'approved' => TRUE,
          'action' => 'flag',
          'flagged_categories' => $flaggedCategories,
          'moderated_content' => NULL,
        ];
    }
  }

  /**
   * Checks content and returns moderation scores without blocking.
   *
   * @param string $content
   *
   * @return array<string, float>
   */
  public function check(string $content): array {
    if (!$this->enabled) {
      return [];
    }

    $scores = $this->callModerationApi($content);
    $filtered = [];

    foreach (self::MODERATION_CATEGORIES as $category) {
      $filtered[$category] = $scores[$category] ?? 0.0;
    }

    return $filtered;
  }

  /**
   * Calls the OpenAI Moderation API.
   *
   * @param string $content
   *
   * @return array<string, float>
   */
  private function callModerationApi(string $content): array {
    $apiKey = \getenv('OPENAI_API_KEY') ?: '';

    $payload = \json_encode([
      'input' => \mb_substr($content, 0, 2000),
    ], \JSON_THROW_ON_ERROR);

    $context = \stream_context_create([
      'http' => [
        'method' => 'POST',
        'header' => [
          'Content-Type: application/json',
          'Authorization: Bearer ' . $apiKey,
        ],
        'content' => $payload,
        'timeout' => 10,
      ],
    ]);

    $response = @\file_get_contents(self::MODERATION_ENDPOINT, FALSE, $context);

    if ($response === FALSE) {
      // If moderation API fails, allow content through (fail-open for availability)
      // but log the failure for monitoring.
      \trigger_error('Content moderation API call failed. Allowing content through.', \E_USER_WARNING);
      return [];
    }

    $data = \json_decode($response, TRUE, 512, \JSON_THROW_ON_ERROR);

    $scores = [];
    if (isset($data['results'][0]['category_scores'])) {
      $scores = $data['results'][0]['category_scores'];
    }

    return $scores;
  }

  /**
   * Rewrites content to remove harmful elements.
   *
   * @param string $content
   *
   * @return string
   */
  private function rewriteContent(string $content): string {
    $replacements = [
      '/\b(ódio|ódio racial|xenofob|homofob|transfob)\w*/ui' => '[conteúdo revisado]',
      '/\b(assédio|assediar|perseguição)\w*/ui' => '[conteúdo revisado]',
      '/\b(suicídio|automutilação|auto-mutilação)\w*/ui' => '[conteúdo revisado]',
      '/\b(explícito|pornográf|obsceno)\w*/ui' => '[conteúdo revisado]',
      '/\b(violência|tortura|assassinato|massacre)\w*/ui' => '[conteúdo revisado]',
    ];

    return \preg_replace(\array_keys($replacements), \array_values($replacements), $content) ?? $content;
  }

}
