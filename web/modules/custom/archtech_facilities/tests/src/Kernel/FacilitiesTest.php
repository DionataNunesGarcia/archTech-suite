<?php

declare(strict_types=1);

namespace Drupal\Tests\archtech_facilities\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * @coversDefaultClass \Drupal\archtech_facilities\Controller\MaintenanceController
 * @group archtech_facilities
 */
final class FacilitiesTest extends KernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'system',
    'text',
    'options',
    'field',
    'filter',
    'datetime',
    'jsonapi',
    'rest',
    'serialization',
    'archtech_facilities',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'filter', 'archtech_facilities']);
  }

  public function testModuleInstalled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('archtech_facilities'),
    );
  }

  public function testMaintenanceScheduleNodeTypeExists(): void {
    $nodeType = NodeType::load('maintenance_schedule');
    $this->assertNotNull($nodeType, 'Maintenance Schedule node type should exist.');
    $this->assertSame('Maintenance Schedule', $nodeType->label());
  }

  public function testMaintenanceCanBeCreated(): void {
    $node = Node::create([
      'type' => 'maintenance_schedule',
      'title' => 'HVAC Filter Replacement',
      'field_maintenance_facility' => 'Tower A - Floor 3',
      'field_maintenance_task' => 'Replace HVAC air filters',
      'field_maintenance_scheduled_date' => '2024-07-15',
      'field_maintenance_status' => 'scheduled',
    ]);
    $node->save();

    $this->assertNotEmpty($node->id());
    $loaded = Node::load($node->id());
    $this->assertSame('HVAC Filter Replacement', $loaded->label());
    $this->assertSame('Tower A - Floor 3', $loaded->get('field_maintenance_facility')->value);
    $this->assertSame('scheduled', $loaded->get('field_maintenance_status')->value);
  }

  public function testMaintenanceStatusWorkflow(): void {
    $node = Node::create([
      'type' => 'maintenance_schedule',
      'title' => 'Elevator Inspection',
      'field_maintenance_facility' => 'Main Building',
      'field_maintenance_task' => 'Annual elevator safety inspection',
      'field_maintenance_scheduled_date' => '2024-08-01',
      'field_maintenance_status' => 'scheduled',
    ]);
    $node->save();

    $node->set('field_maintenance_status', 'in_progress');
    $node->save();
    $this->assertSame('in_progress', Node::load($node->id())->get('field_maintenance_status')->value);

    $node->set('field_maintenance_status', 'completed');
    $node->save();
    $this->assertSame('completed', Node::load($node->id())->get('field_maintenance_status')->value);
  }

  public function testEventSubscriberRegistered(): void {
    $subscriber = $this->container->get('archtech_facilities.maintenance_event_subscriber');
    $this->assertInstanceOf(
      \Drupal\archtech_facilities\EventSubscriber\MaintenanceEventSubscriber::class,
      $subscriber,
    );
  }

}
