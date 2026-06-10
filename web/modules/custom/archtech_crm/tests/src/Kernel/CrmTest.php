<?php

declare(strict_types=1);

namespace Drupal\Tests\archtech_crm\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * @coversDefaultClass \Drupal\archtech_crm\Controller\LeadController
 * @group archtech_crm
 */
final class CrmTest extends KernelTestBase {

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
    'archtech_crm',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'filter', 'archtech_crm']);
  }

  public function testModuleInstalled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('archtech_crm'),
    );
  }

  public function testLeadNodeTypeExists(): void {
    $nodeType = NodeType::load('lead');
    $this->assertNotNull($nodeType, 'Lead node type should exist.');
    $this->assertSame('Lead', $nodeType->label());
  }

  public function testLeadCanBeCreated(): void {
    $node = Node::create([
      'type' => 'lead',
      'title' => 'Test Lead',
      'field_lead_name' => 'John Doe',
      'field_lead_email' => 'john@example.com',
      'field_lead_status' => 'new',
    ]);
    $node->save();

    $this->assertNotEmpty($node->id());
    $this->assertSame('new', $node->get('field_lead_status')->value);

    $loaded = Node::load($node->id());
    $this->assertNotNull($loaded);
    $this->assertSame('John Doe', $loaded->get('field_lead_name')->value);
  }

  public function testLeadStatusUpdate(): void {
    $node = Node::create([
      'type' => 'lead',
      'title' => 'Status Test Lead',
      'field_lead_name' => 'Jane Doe',
      'field_lead_email' => 'jane@example.com',
      'field_lead_status' => 'new',
    ]);
    $node->save();

    $node->set('field_lead_status', 'qualified');
    $node->save();

    $reloaded = Node::load($node->id());
    $this->assertSame('qualified', $reloaded->get('field_lead_status')->value);
  }

  public function testEventSubscriberRegistered(): void {
    $subscriber = $this->container->get('archtech_crm.lead_event_subscriber');
    $this->assertInstanceOf(
      \Drupal\archtech_crm\EventSubscriber\LeadEventSubscriber::class,
      $subscriber,
    );
  }

}
