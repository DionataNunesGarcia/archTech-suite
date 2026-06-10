<?php

declare(strict_types=1);

namespace Drupal\Tests\archtech_suppliers\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * @coversDefaultClass \Drupal\archtech_suppliers\Controller\SupplierController
 * @group archtech_suppliers
 */
final class SuppliersTest extends KernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'system',
    'text',
    'options',
    'field',
    'filter',
    'jsonapi',
    'rest',
    'serialization',
    'archtech_suppliers',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'filter', 'archtech_suppliers']);
  }

  public function testModuleInstalled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('archtech_suppliers'),
    );
  }

  public function testSupplierNodeTypeExists(): void {
    $nodeType = NodeType::load('supplier');
    $this->assertNotNull($nodeType, 'Supplier node type should exist.');
    $this->assertSame('Supplier', $nodeType->label());
  }

  public function testSupplierCanBeCreated(): void {
    $node = Node::create([
      'type' => 'supplier',
      'title' => 'ABC Construction Materials',
      'field_supplier_category' => 'materials',
      'field_supplier_rating' => '4.5',
      'field_supplier_email' => 'contact@abc-materials.com',
      'field_supplier_phone' => '+55 11 99999-0000',
      'field_supplier_sla_score' => '92.50',
    ]);
    $node->save();

    $this->assertNotEmpty($node->id());
    $loaded = Node::load($node->id());
    $this->assertSame('materials', $loaded->get('field_supplier_category')->value);
    $this->assertSame('4.5', $loaded->get('field_supplier_rating')->value);
    $this->assertSame('92.50', $loaded->get('field_supplier_sla_score')->value);
  }

  public function testSupplierRatingUpdate(): void {
    $node = Node::create([
      'type' => 'supplier',
      'title' => 'Test Supplier',
      'field_supplier_category' => 'other',
      'field_supplier_rating' => '3.0',
    ]);
    $node->save();

    $node->set('field_supplier_rating', '4.8');
    $node->save();

    $loaded = Node::load($node->id());
    $this->assertSame('4.8', $loaded->get('field_supplier_rating')->value);
  }

  public function testEventSubscriberRegistered(): void {
    $subscriber = $this->container->get('archtech_suppliers.supplier_event_subscriber');
    $this->assertInstanceOf(
      \Drupal\archtech_suppliers\EventSubscriber\SupplierEventSubscriber::class,
      $subscriber,
    );
  }

}
