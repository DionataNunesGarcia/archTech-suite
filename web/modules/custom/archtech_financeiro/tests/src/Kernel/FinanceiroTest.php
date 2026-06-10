<?php

declare(strict_types=1);

namespace Drupal\Tests\archtech_financeiro\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * @coversDefaultClass \Drupal\archtech_financeiro\Controller\InvoiceController
 * @group archtech_financeiro
 */
final class FinanceiroTest extends KernelTestBase {

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
    'archtech_financeiro',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'filter', 'archtech_financeiro']);
  }

  public function testModuleInstalled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('archtech_financeiro'),
    );
  }

  public function testInvoiceNodeTypeExists(): void {
    $nodeType = NodeType::load('invoice');
    $this->assertNotNull($nodeType, 'Invoice node type should exist.');
    $this->assertSame('Invoice', $nodeType->label());
  }

  public function testInvoiceCanBeCreated(): void {
    $node = Node::create([
      'type' => 'invoice',
      'title' => 'INV-2024-001',
      'field_invoice_amount' => '25000.00',
      'field_invoice_status' => 'draft',
    ]);
    $node->save();

    $this->assertNotEmpty($node->id());
    $loaded = Node::load($node->id());
    $this->assertSame('25000.00', $loaded->get('field_invoice_amount')->value);
    $this->assertSame('draft', $loaded->get('field_invoice_status')->value);
  }

  public function testInvoiceStatusWorkflow(): void {
    $node = Node::create([
      'type' => 'invoice',
      'title' => 'INV-2024-002',
      'field_invoice_amount' => '5000.00',
      'field_invoice_status' => 'draft',
    ]);
    $node->save();

    $node->set('field_invoice_status', 'sent');
    $node->save();
    $this->assertSame('sent', Node::load($node->id())->get('field_invoice_status')->value);

    $node->set('field_invoice_status', 'paid');
    $node->set('field_invoice_paid_date', '2024-06-15');
    $node->save();
    $loaded = Node::load($node->id());
    $this->assertSame('paid', $loaded->get('field_invoice_status')->value);
    $this->assertSame('2024-06-15', $loaded->get('field_invoice_paid_date')->value);
  }

  public function testEventSubscriberRegistered(): void {
    $subscriber = $this->container->get('archtech_financeiro.invoice_event_subscriber');
    $this->assertInstanceOf(
      \Drupal\archtech_financeiro\EventSubscriber\InvoiceEventSubscriber::class,
      $subscriber,
    );
  }

}
