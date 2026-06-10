<?php

declare(strict_types=1);

namespace Drupal\Tests\ia_compliance\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * @coversDefaultClass \Drupal\ia_compliance\Controller\ComplianceController
 * @group ia_compliance
 */
final class ComplianceKernelTest extends EntityKernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'field',
    'text',
    'datetime',
    'options',
    'system',
    'ia_compliance',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'ia_compliance']);
  }

  public function testModuleEnabled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('ia_compliance'),
      'ia_compliance module should be installed.'
    );
  }

  public function testNodeTypeExists(): void {
    $node_type = $this->container->get('entity_type.manager')
      ->getStorage('node_type')
      ->load('compliance_record');

    $this->assertNotNull($node_type, 'compliance_record node type should exist.');
  }

  public function testComplianceRecordCreation(): void {
    $hash = hash('sha256', 'test_data');
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'compliance_record',
        'title' => 'compliance_record node/1/create',
        'field_compliance_entity_type' => 'node',
        'field_compliance_entity_id' => 1,
        'field_compliance_action' => 'create',
        'field_compliance_changes_json' => [
          'value' => json_encode(['action' => 'create', 'type' => 'node']),
          'format' => 'plain_text',
        ],
        'field_compliance_user' => 1,
        'field_compliance_timestamp' => time(),
        'field_compliance_hash' => $hash,
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('compliance_record', $node->bundle());
    $this->assertSame('create', $node->get('field_compliance_action')->value);
    $this->assertSame($hash, $node->get('field_compliance_hash')->value);
  }
}
