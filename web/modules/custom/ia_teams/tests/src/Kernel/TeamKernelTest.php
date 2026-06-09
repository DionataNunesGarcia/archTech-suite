<?php

declare(strict_types=1);

namespace Drupal\Tests\ia_teams\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * @coversDefaultClass \Drupal\ia_teams\Controller\TeamController
 * @group ia_teams
 */
final class TeamKernelTest extends EntityKernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'field',
    'text',
    'datetime',
    'options',
    'system',
    'ia_teams',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'ia_teams']);
  }

  public function testModuleEnabled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('ia_teams'),
      'ia_teams module should be installed.'
    );
  }

  public function testNodeTypesExist(): void {
    $storage = $this->container->get('entity_type.manager')->getStorage('node_type');

    $this->assertNotNull($storage->load('team_member'), 'team_member type should exist.');
    $this->assertNotNull($storage->load('project_allocation'), 'project_allocation type should exist.');
  }

  public function testTeamMemberCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'team_member',
        'title' => 'Carlos Silva',
        'field_tm_role' => 'Senior Architect',
        'field_tm_squad' => 'design',
        'field_tm_skills' => [
          'value' => 'Revit, AutoCAD, BIM Management',
          'format' => 'plain_text',
        ],
        'field_tm_availability' => 80,
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('team_member', $node->bundle());
    $this->assertSame('design', $node->get('field_tm_squad')->value);
  }

  public function testProjectAllocationCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'project_allocation',
        'title' => 'Allocation Q1 2026',
        'field_alloc_start_date' => '2026-01-01',
        'field_alloc_end_date' => '2026-06-30',
        'field_alloc_percentage' => 75,
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('project_allocation', $node->bundle());
  }
}
