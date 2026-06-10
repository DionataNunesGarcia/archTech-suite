<?php

declare(strict_types=1);

namespace Drupal\Tests\ia_marketing_digital\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * @coversDefaultClass \Drupal\ia_marketing_digital\Controller\CampaignController
 * @group ia_marketing_digital
 */
final class MarketingKernelTest extends EntityKernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'field',
    'text',
    'datetime',
    'options',
    'system',
    'ia_marketing_digital',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'ia_marketing_digital']);
  }

  public function testModuleEnabled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('ia_marketing_digital'),
      'ia_marketing_digital module should be installed.'
    );
  }

  public function testNodeTypeExists(): void {
    $node_type = $this->container->get('entity_type.manager')
      ->getStorage('node_type')
      ->load('campaign');

    $this->assertNotNull($node_type, 'campaign node type should exist.');
  }

  public function testCampaignCreation(): void {
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'campaign',
        'title' => 'Summer Campaign 2026',
        'field_campaign_name' => 'Summer Campaign 2026',
        'field_campaign_channel' => 'instagram',
        'field_campaign_budget' => '5000.00',
        'field_campaign_status' => 'planned',
      ]);

    $node->save();

    $this->assertNotNull($node->id(), 'Node should have an ID.');
    $this->assertSame('campaign', $node->bundle());
    $this->assertSame('planned', $node->get('field_campaign_status')->value);
  }
}
