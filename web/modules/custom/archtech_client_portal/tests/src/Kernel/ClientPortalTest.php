<?php

declare(strict_types=1);

namespace Drupal\Tests\archtech_client_portal\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * @coversDefaultClass \Drupal\archtech_client_portal\Controller\UpdateController
 * @group archtech_client_portal
 */
final class ClientPortalTest extends KernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'system',
    'text',
    'options',
    'field',
    'filter',
    'file',
    'jsonapi',
    'rest',
    'serialization',
    'archtech_client_portal',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installConfig(['node', 'filter', 'archtech_client_portal']);
  }

  public function testModuleInstalled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('archtech_client_portal'),
    );
  }

  public function testProjectUpdateNodeTypeExists(): void {
    $nodeType = NodeType::load('project_update');
    $this->assertNotNull($nodeType, 'Project Update node type should exist.');
    $this->assertSame('Project Update', $nodeType->label());
  }

  public function testProjectUpdateCanBeCreated(): void {
    $node = Node::create([
      'type' => 'project_update',
      'title' => 'Construction Progress',
      'field_update_body' => ['value' => 'Foundation work completed.', 'format' => 'plain_text'],
      'field_update_visibility' => 'client_only',
    ]);
    $node->save();

    $this->assertNotEmpty($node->id());
    $loaded = Node::load($node->id());
    $this->assertSame('client_only', $loaded->get('field_update_visibility')->value);
  }

  public function testVisibilityValues(): void {
    $node = Node::create([
      'type' => 'project_update',
      'title' => 'Internal Memo',
      'field_update_body' => ['value' => 'Confidential.', 'format' => 'plain_text'],
      'field_update_visibility' => 'internal',
    ]);
    $node->save();

    $loaded = Node::load($node->id());
    $this->assertSame('internal', $loaded->get('field_update_visibility')->value);
  }

  public function testEventSubscriberRegistered(): void {
    $subscriber = $this->container->get('archtech_client_portal.update_event_subscriber');
    $this->assertInstanceOf(
      \Drupal\archtech_client_portal\EventSubscriber\UpdateEventSubscriber::class,
      $subscriber,
    );
  }

}
