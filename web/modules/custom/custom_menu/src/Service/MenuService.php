<?php

namespace Drupal\custom_menu\Service;

use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;

class MenuService {

  protected MenuLinkTreeInterface $menuTree;

  public function __construct(MenuLinkTreeInterface $menu_tree) {
    $this->menuTree = $menu_tree;
  }

  /**
   * Get structured menu tree for any menu.
   */
  public function getMenuTree(string $menu_name): array {
    $parameters = (new MenuTreeParameters())
      ->onlyEnabledLinks();

    $tree = $this->menuTree->load($menu_name, $parameters);

    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];

    $tree = $this->menuTree->transform($tree, $manipulators);

    return $this->buildTree($tree);
  }

  /**
   * Recursively build menu tree structure.
   */
  protected function buildTree(array $elements): array {
    $items = [];

    foreach ($elements as $element) {
      if (!$element->link) {
        continue;
      }

      $link = $element->link;
      $definition = $link->getPluginDefinition();

      // Get attributes from menu_link_attributes module
      $attributes = $definition['options']['attributes'] ?? [];
      $classes = $attributes['class'] ?? [];
      $target = $attributes['target'] ?? '_self';

      $item = [
        'title' => $link->getTitle() ?: '',
        'url' => $link->getUrlObject() ? $link->getUrlObject()->toString() : '#',
        'classes' => is_array($classes) ? implode(' ', $classes) : ($classes ?? ''),
        'target' => $target ?: '_self',
        'id' => strtolower($link->getTitle() ?? '') === 'buscar' ? 'openSearchModal' : '',
        'icon_left' => '',
        'icon_right' => '',
        'below' => [],
      ];

      // Handle icons from menu_bootstrap_icon module
      if (!empty($definition['options']['icon'])) {
        $icon = $definition['options']['icon'];
        $appearance = $definition['options']['icon_appearance'] ?? 'before';

        $item['icon_left'] = $appearance === 'before' ? $icon : '';
        $item['icon_right'] = $appearance === 'after' ? $icon : '';
      }

      // Recursively process children (submenus)
      if (!empty($element->subtree)) {
        $item['below'] = $this->buildTree($element->subtree);
      }
      else {
        $item['below'] = [];
      }

      $items[] = $item;
    }

    return $items;
  }
}
