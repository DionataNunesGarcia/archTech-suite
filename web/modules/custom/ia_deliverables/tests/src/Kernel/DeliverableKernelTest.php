<?php

declare(strict_types=1);

namespace Drupal\Tests\ia_deliverables\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * @coversDefaultClass \Drupal\ia_deliverables\Controller\DeliverableController
 * @group ia_deliverables
 */
final class DeliverableKernelTest extends EntityKernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'field',
    'text',
    'datetime',
    'file',
    'options',
    'system',
    'ia_deliverables',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'ia_deliverables']);
  }

  public function testModuleEnabled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('ia_deliverables'),
      'ia_deliverables module should be installed.'
    );
  }

  public function testNodeTypeExists(): void {
    $node_type = $this->container->get('entity_type.manager')
      ->getStorage('node_type')
      ->load('deliverable');

    $this->assertNotNull($node_type, 'deliverable node type should exist.');
  }

  public function testDeliverableCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'deliverable',
        'title' => 'Structural Plans',
        'field_deliverable_phase' => 'executive',
        'field_deliverable_title' => 'Structural Plans',
        'field_deliverable_description' => [
          'value' => 'Complete structural engineering drawings',
          'format' => 'plain_text',
        ],
        'field_deliverable_status' => 'pending',
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('deliverable', $node->bundle());
    $this->assertSame('executive', $node->get('field_deliverable_phase')->value);
    $this->assertSame('pending', $node->get('field_deliverable_status')->value);
  }
}
