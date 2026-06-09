<?php

declare(strict_types=1);

namespace Drupal\Tests\archtech_permits\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * @coversDefaultClass \Drupal\archtech_permits\Controller\PermitController
 * @group archtech_permits
 */
final class PermitsTest extends KernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'system',
    'text',
    'options',
    'field',
    'filter',
    'file',
    'datetime',
    'jsonapi',
    'rest',
    'serialization',
    'archtech_permits',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installConfig(['node', 'filter', 'archtech_permits']);
  }

  public function testModuleInstalled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('archtech_permits'),
    );
  }

  public function testPermitApplicationNodeTypeExists(): void {
    $nodeType = NodeType::load('permit_application');
    $this->assertNotNull($nodeType, 'Permit Application node type should exist.');
    $this->assertSame('Permit Application', $nodeType->label());
  }

  public function testPermitCanBeCreated(): void {
    $node = Node::create([
      'type' => 'permit_application',
      'title' => 'Building Permit - Tower A',
      'field_permit_type' => 'construction',
      'field_permit_status' => 'draft',
    ]);
    $node->save();

    $this->assertNotEmpty($node->id());
    $loaded = Node::load($node->id());
    $this->assertSame('construction', $loaded->get('field_permit_type')->value);
    $this->assertSame('draft', $loaded->get('field_permit_status')->value);
  }

  public function testPermitStatusWorkflow(): void {
    $node = Node::create([
      'type' => 'permit_application',
      'title' => 'Zoning Permit',
      'field_permit_type' => 'zoning',
      'field_permit_status' => 'draft',
    ]);
    $node->save();

    $node->set('field_permit_status', 'submitted');
    $node->set('field_permit_submitted_date', '2024-05-01');
    $node->save();
    $loaded = Node::load($node->id());
    $this->assertSame('submitted', $loaded->get('field_permit_status')->value);

    $node->set('field_permit_status', 'approved');
    $node->set('field_permit_approved_date', '2024-06-15');
    $node->save();
    $loaded = Node::load($node->id());
    $this->assertSame('approved', $loaded->get('field_permit_status')->value);
    $this->assertSame('2024-06-15', $loaded->get('field_permit_approved_date')->value);
  }

  public function testEventSubscriberRegistered(): void {
    $subscriber = $this->container->get('archtech_permits.permit_event_subscriber');
    $this->assertInstanceOf(
      \Drupal\archtech_permits\EventSubscriber\PermitEventSubscriber::class,
      $subscriber,
    );
  }

}
