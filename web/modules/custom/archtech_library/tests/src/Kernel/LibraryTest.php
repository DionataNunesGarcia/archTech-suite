<?php

declare(strict_types=1);

namespace Drupal\Tests\archtech_library\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * @coversDefaultClass \Drupal\archtech_library\Controller\SearchController
 * @group archtech_library
 */
final class LibraryTest extends KernelTestBase {

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
    'archtech_library',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installConfig(['node', 'filter', 'archtech_library']);
  }

  public function testModuleInstalled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('archtech_library'),
    );
  }

  public function testDocumentNodeTypeExists(): void {
    $nodeType = NodeType::load('document');
    $this->assertNotNull($nodeType, 'Document node type should exist.');
    $this->assertSame('Document', $nodeType->label());
  }

  public function testDocumentCanBeCreated(): void {
    $node = Node::create([
      'type' => 'document',
      'title' => 'Structural Analysis Report',
      'field_document_category' => 'technical',
      'field_document_tags' => 'structural,analysis,report',
      'field_document_description' => ['value' => 'Structural analysis of the main building.', 'format' => 'plain_text'],
    ]);
    $node->save();

    $this->assertNotEmpty($node->id());
    $loaded = Node::load($node->id());
    $this->assertSame('technical', $loaded->get('field_document_category')->value);
    $this->assertSame('structural,analysis,report', $loaded->get('field_document_tags')->value);
  }

  public function testEmbeddingFieldIsHidden(): void {
    $node = Node::create([
      'type' => 'document',
      'title' => 'Document with Embedding',
      'field_document_category' => 'other',
      'field_document_embedding' => ['value' => json_encode([0.1, 0.2, 0.3]), 'format' => 'plain_text'],
    ]);
    $node->save();

    $loaded = Node::load($node->id());
    $this->assertNotNull($loaded->get('field_document_embedding')->value);
  }

  public function testEventSubscriberRegistered(): void {
    $subscriber = $this->container->get('archtech_library.document_event_subscriber');
    $this->assertInstanceOf(
      \Drupal\archtech_library\EventSubscriber\DocumentEventSubscriber::class,
      $subscriber,
    );
  }

}
