<?php

declare(strict_types=1);

namespace Drupal\Tests\ia_diary\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * @coversDefaultClass \Drupal\ia_diary\Controller\DiaryController
 * @group ia_diary
 */
final class DiaryKernelTest extends EntityKernelTestBase {

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
    'ia_diary',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'ia_diary']);
  }

  public function testModuleEnabled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('ia_diary'),
      'ia_diary module should be installed.'
    );
  }

  public function testNodeTypeExists(): void {
    $node_type = $this->container->get('entity_type.manager')
      ->getStorage('node_type')
      ->load('diary_entry');

    $this->assertNotNull($node_type, 'diary_entry node type should exist.');
  }

  public function testDiaryEntryCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'diary_entry',
        'title' => 'Test Diary Entry',
        'field_diary_weather' => 'Sunny',
        'field_diary_activities' => [
          'value' => 'Foundation pouring',
          'format' => 'plain_text',
        ],
        'field_diary_equipment' => [
          'value' => 'Concrete mixer',
          'format' => 'plain_text',
        ],
        'field_diary_workers_count' => 12,
        'field_diary_notes' => [
          'value' => 'All tasks completed on schedule',
          'format' => 'plain_text',
        ],
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('diary_entry', $node->bundle());
    $this->assertSame(12, (int) $node->get('field_diary_workers_count')->value);
  }

  public function testControllerServiceExists(): void {
    $controller = \Drupal\ia_diary\Controller\DiaryController::create(
      $this->container
    );
    $this->assertInstanceOf(
      \Drupal\ia_diary\Controller\DiaryController::class,
      $controller
    );
  }
}
