<?php

namespace Drupal\custom_menu\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;

/**
 * Custom Menu Drush commands.
 */
class CustomMenuCommands extends DrushCommands {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct();
  }

  /**
   * Creates custom menus.
   *
   * @command custom-menu:create
   * @option menus Comma-separated list of menu IDs to create
   * @usage custom-menu:create --menus=main,footer-about
   */
  public function createMenus(array $options = ['menus' => []]) {
    $menus = $options['menus'] ?: [
      'menus-cta' => 'CTA',
      'menu-footer-about' => 'Sobre',
      'menu-footer-programs' => 'Programas',
      'menu-footer-privacy-terms' => 'Privacidade',
    ];

    $menu_storage = $this->entityTypeManager->getStorage('menu');

    foreach ($menus as $id => $label) {
      $existing = $menu_storage->load($id);
      if ($existing) {
        $this->logger()->warning('Menu @id already exists, skipping.', ['@id' => $id]);
        continue;
      }

      $menu = $menu_storage->create([
        'id' => $id,
        'label' => $label,
        'description' => "Menu {$label}",
        'langcode' => 'pt-br',
      ]);
      $menu->save();
      $this->logger()->success('Created menu @label (@id).', ['@label' => $label, '@id' => $id]);
    }
  }

}