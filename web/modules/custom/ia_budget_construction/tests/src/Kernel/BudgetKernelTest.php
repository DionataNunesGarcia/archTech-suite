<?php

declare(strict_types=1);

namespace Drupal\Tests\ia_budget_construction\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * @coversDefaultClass \Drupal\ia_budget_construction\Controller\BudgetController
 * @group ia_budget_construction
 */
final class BudgetKernelTest extends EntityKernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'field',
    'text',
    'datetime',
    'options',
    'system',
    'paragraphs',
    'entity_reference_revisions',
    'ia_budget_construction',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('paragraph');
    $this->installConfig(['node', 'paragraphs', 'ia_budget_construction']);
  }

  public function testModuleEnabled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('ia_budget_construction'),
      'ia_budget_construction module should be installed.'
    );
  }

  public function testNodeTypesExist(): void {
    $storage = $this->container->get('entity_type.manager')->getStorage('node_type');

    $this->assertNotNull($storage->load('budget'), 'budget type should exist.');
    $this->assertNotNull($storage->load('measurement'), 'measurement type should exist.');
  }

  public function testBudgetCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'budget',
        'title' => 'Budget Q1 2026',
        'field_budget_total' => '250000.00',
        'field_budget_status' => 'draft',
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('budget', $node->bundle());
    $this->assertSame('250000.000000', $node->get('field_budget_total')->value);
    $this->assertSame('draft', $node->get('field_budget_status')->value);
  }

  public function testMeasurementCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'measurement',
        'title' => 'Concrete Volume Measurement',
        'field_measurement_item' => 'Concrete C25',
        'field_measurement_quantity' => '45.5000',
        'field_measurement_unit' => 'm3',
        'field_measurement_unit_price' => '350.0000',
        'field_measurement_total' => '15925.00',
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('measurement', $node->bundle());
    $this->assertSame('Concrete C25', $node->get('field_measurement_item')->value);
  }
}
