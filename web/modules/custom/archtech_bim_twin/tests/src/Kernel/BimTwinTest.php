<?php

declare(strict_types=1);

namespace Drupal\Tests\archtech_bim_twin\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * @coversDefaultClass \Drupal\archtech_bim_twin\Controller\BimModelController
 * @group archtech_bim_twin
 */
final class BimTwinTest extends KernelTestBase {

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
    'archtech_bim_twin',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installConfig(['node', 'filter', 'archtech_bim_twin']);
  }

  public function testModuleInstalled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('archtech_bim_twin'),
    );
  }

  public function testBimModelNodeTypeExists(): void {
    $nodeType = NodeType::load('bim_model');
    $this->assertNotNull($nodeType, 'BIM Model node type should exist.');
    $this->assertSame('BIM Model', $nodeType->label());
  }

  public function testBimModelCanBeCreated(): void {
    $node = Node::create([
      'type' => 'bim_model',
      'title' => 'Tower A - Structural Model',
      'field_bim_status' => 'uploaded',
      'field_bim_metadata' => json_encode([
        'author' => 'Structural Engineering Dept',
        'version' => '2.1',
        'ifc_schema' => 'IFC4',
      ]),
    ]);
    $node->save();

    $this->assertNotEmpty($node->id());
    $loaded = Node::load($node->id());
    $this->assertSame('uploaded', $loaded->get('field_bim_status')->value);

    $metadata = json_decode($loaded->get('field_bim_metadata')->value, TRUE);
    $this->assertSame('IFC4', $metadata['ifc_schema']);
  }

  public function testBimModelStatusWorkflow(): void {
    $node = Node::create([
      'type' => 'bim_model',
      'title' => 'Test IFC Model',
      'field_bim_status' => 'uploaded',
    ]);
    $node->save();

    $node->set('field_bim_status', 'processing');
    $node->save();
    $this->assertSame('processing', Node::load($node->id())->get('field_bim_status')->value);

    $node->set('field_bim_status', 'validated');
    $node->save();
    $this->assertSame('validated', Node::load($node->id())->get('field_bim_status')->value);
  }

  public function testValidationErrorsField(): void {
    $node = Node::create([
      'type' => 'bim_model',
      'title' => 'Model with Errors',
      'field_bim_status' => 'error',
      'field_bim_validation_errors' => ['value' => "Missing structural elements\nIFC schema mismatch", 'format' => 'plain_text'],
    ]);
    $node->save();

    $loaded = Node::load($node->id());
    $this->assertStringContainsString('Missing structural elements', $loaded->get('field_bim_validation_errors')->value);
  }

  public function testEventSubscriberRegistered(): void {
    $subscriber = $this->container->get('archtech_bim_twin.bim_event_subscriber');
    $this->assertInstanceOf(
      \Drupal\archtech_bim_twin\EventSubscriber\BimEventSubscriber::class,
      $subscriber,
    );
  }

}
