<?php

declare(strict_types=1);

namespace Drupal\Tests\archtech_proposals\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * @coversDefaultClass \Drupal\archtech_proposals\Controller\ProposalController
 * @group archtech_proposals
 */
final class ProposalsTest extends KernelTestBase {

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
    'archtech_proposals',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['node', 'filter', 'archtech_proposals']);
  }

  public function testModuleInstalled(): void {
    $this->assertTrue(
      $this->container->get('module_handler')->moduleExists('archtech_proposals'),
    );
  }

  public function testProposalNodeTypeExists(): void {
    $nodeType = NodeType::load('proposal');
    $this->assertNotNull($nodeType, 'Proposal node type should exist.');
    $this->assertSame('Proposal', $nodeType->label());
  }

  public function testProposalCanBeCreated(): void {
    $node = Node::create([
      'type' => 'proposal',
      'title' => 'Villa Project Proposal',
      'field_proposal_description' => ['value' => 'A luxury villa design.', 'format' => 'plain_text'],
      'field_proposal_status' => 'draft',
      'field_proposal_budget' => '150000.00',
    ]);
    $node->save();

    $this->assertNotEmpty($node->id());
    $loaded = Node::load($node->id());
    $this->assertSame('draft', $loaded->get('field_proposal_status')->value);
    $this->assertSame('150000.00', $loaded->get('field_proposal_budget')->value);
  }

  public function testProposalStatusWorkflow(): void {
    $node = Node::create([
      'type' => 'proposal',
      'title' => 'Status Test',
      'field_proposal_description' => ['value' => 'Test', 'format' => 'plain_text'],
      'field_proposal_status' => 'draft',
    ]);
    $node->save();

    $node->set('field_proposal_status', 'sent');
    $node->save();
    $this->assertSame('sent', Node::load($node->id())->get('field_proposal_status')->value);

    $node->set('field_proposal_status', 'accepted');
    $node->save();
    $this->assertSame('accepted', Node::load($node->id())->get('field_proposal_status')->value);
  }

  public function testEventSubscriberRegistered(): void {
    $subscriber = $this->container->get('archtech_proposals.proposal_event_subscriber');
    $this->assertInstanceOf(
      \Drupal\archtech_proposals\EventSubscriber\ProposalEventSubscriber::class,
      $subscriber,
    );
  }

}
