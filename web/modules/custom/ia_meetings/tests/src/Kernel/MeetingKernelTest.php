<?php

declare(strict_types=1);

namespace Drupal\Tests\ia_meetings\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * @coversDefaultClass \Drupal\ia_meetings\Controller\MeetingController
 * @group ia_meetings
 */
final class MeetingKernelTest extends EntityKernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'field',
    'text',
    'datetime',
    'options',
    'system',
    'paragraphs',
    'entity_reference_revisions',
    'ia_meetings',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('paragraph');
    $this->installConfig(['node', 'paragraphs', 'ia_meetings']);
  }

  public function testModuleEnabled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('ia_meetings'),
      'ia_meetings module should be installed.'
    );
  }

  public function testNodeTypeExists(): void {
    $node_type = $this->container->get('entity_type.manager')
      ->getStorage('node_type')
      ->load('meeting_record');

    $this->assertNotNull($node_type, 'meeting_record node type should exist.');
  }

  public function testMeetingCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'meeting_record',
        'title' => 'Weekly Sync',
        'field_meeting_status' => 'scheduled',
        'field_meeting_agenda' => [
          'value' => 'Review sprint progress',
          'format' => 'plain_text',
        ],
        'field_meeting_attendees' => [
          'value' => 'John, Maria, Carlos',
          'format' => 'plain_text',
        ],
        'field_meeting_decisions' => [
          'value' => 'Extend deadline by 1 week',
          'format' => 'plain_text',
        ],
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('meeting_record', $node->bundle());
    $this->assertSame('scheduled', $node->get('field_meeting_status')->value);
  }

  public function testControllerServiceExists(): void {
    $controller = \Drupal\ia_meetings\Controller\MeetingController::create(
      $this->container
    );
    $this->assertInstanceOf(
      \Drupal\ia_meetings\Controller\MeetingController::class,
      $controller
    );
  }
}
