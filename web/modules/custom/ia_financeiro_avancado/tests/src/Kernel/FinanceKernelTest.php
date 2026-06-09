<?php

declare(strict_types=1);

namespace Drupal\Tests\ia_financeiro_avancado\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * @coversDefaultClass \Drupal\ia_financeiro_avancado\Controller\FinanceController
 * @group ia_financeiro_avancado
 */
final class FinanceKernelTest extends EntityKernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'field',
    'text',
    'datetime',
    'image',
    'file',
    'options',
    'system',
    'ia_financeiro_avancado',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'ia_financeiro_avancado']);
  }

  public function testModuleEnabled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('ia_financeiro_avancado'),
      'ia_financeiro_avancado module should be installed.'
    );
  }

  public function testNodeTypesExist(): void {
    $storage = $this->container->get('entity_type.manager')->getStorage('node_type');

    $this->assertNotNull($storage->load('reimbursement'), 'reimbursement type should exist.');
    $this->assertNotNull($storage->load('cashflow_entry'), 'cashflow_entry type should exist.');
  }

  public function testReimbursementCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'reimbursement',
        'title' => 'Travel Expenses',
        'field_reimb_amount' => '250.00',
        'field_reimb_category' => 'travel',
        'field_reimb_status' => 'pending',
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('reimbursement', $node->bundle());
    $this->assertSame('250.000000', $node->get('field_reimb_amount')->value);
    $this->assertSame('pending', $node->get('field_reimb_status')->value);
  }

  public function testCashflowEntryCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'cashflow_entry',
        'title' => 'Material Purchase',
        'field_cashflow_type' => 'expense',
        'field_cashflow_amount' => '1500.00',
        'field_cashflow_description' => [
          'value' => 'Cement and steel',
          'format' => 'plain_text',
        ],
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('cashflow_entry', $node->bundle());
  }
}
