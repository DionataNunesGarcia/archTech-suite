<?php

declare(strict_types=1);

namespace Drupal\Tests\ia_tasks\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * @coversDefaultClass \Drupal\ia_tasks\Controller\TaskController
 * @group ia_tasks
 */
final class TaskKernelTest extends EntityKernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'field',
    'text',
    'datetime',
    'taxonomy',
    'options',
    'system',
    'ia_tasks',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('taxonomy_term');
    $this->installConfig(['node', 'taxonomy', 'ia_tasks']);
  }

  public function testModuleEnabled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('ia_tasks'),
      'ia_tasks module should be installed.'
    );
  }

  public function testNodeTypeExists(): void {
    $node_type = $this->container->get('entity_type.manager')
      ->getStorage('node_type')
      ->load('task');

    $this->assertNotNull($node_type, 'task node type should exist.');
  }

  public function testTaskCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'task',
        'title' => 'Review Structural Plans',
        'field_task_title' => 'Review Structural Plans',
        'field_task_description' => [
          'value' => 'Complete review of the structural engineering drawings',
          'format' => 'plain_text',
        ],
        'field_task_priority' => 'high',
        'field_task_status' => 'todo',
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('task', $node->bundle());
    $this->assertSame('high', $node->get('field_task_priority')->value);
    $this->assertSame('todo', $node->get('field_task_status')->value);
  }

  public function testTaskStatusTransition(): void {
    $storage = $this->container->get('entity_type.manager')->getStorage('node');
    $node = $storage->create([
      'type' => 'task',
      'title' => 'Test Status Transition',
      'field_task_title' => 'Test Status Transition',
      'field_task_priority' => 'medium',
      'field_task_status' => 'backlog',
    ]);
    $node->save();

    $node->set('field_task_status', 'in_progress');
    $node->save();

    $reloaded = $storage->load($node->id());
    $this->assertSame('in_progress', $reloaded->get('field_task_status')->value);
  }
}
