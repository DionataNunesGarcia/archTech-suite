# Common Drupal Hooks Reference

This reference provides commonly used Drupal hooks across versions 8-11+.

## Entity Hooks

### hook_entity_presave()
Called before an entity is saved.
```php
function mymodule_entity_presave(Drupal\Core\Entity\EntityInterface $entity) {
  if ($entity->getEntityTypeId() == 'node') {
    // Modify entity before saving
  }
}
```

### hook_entity_insert()
Called after a new entity is created.
```php
function mymodule_entity_insert(Drupal\Core\Entity\EntityInterface $entity) {
  // React to new entity creation
}
```

### hook_entity_update()
Called after an existing entity is updated.
```php
function mymodule_entity_update(Drupal\Core\Entity\EntityInterface $entity) {
  // React to entity updates
}
```

### hook_entity_delete()
Called after an entity is deleted.
```php
function mymodule_entity_delete(Drupal\Core\Entity\EntityInterface $entity) {
  // Clean up related data
}
```

### hook_entity_view()
Alter entity display.
```php
function mymodule_entity_view(array &$build, Drupal\Core\Entity\EntityInterface $entity, \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display, $view_mode) {
  if ($entity->getEntityTypeId() == 'node') {
    $build['#attached']['library'][] = 'mymodule/custom-library';
  }
}
```

### hook_entity_view_alter()
Alter entity view arrays after all modules have added their implementations.
```php
function mymodule_entity_view_alter(array &$build, Drupal\Core\Entity\EntityInterface $entity, \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display) {
  // Modify the render array
}
```

## Form Hooks

### hook_form_alter()
Modify any form.
```php
function mymodule_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
  if ($form_id == 'node_article_form') {
    $form['title']['#description'] = 'Custom description';
  }
}
```

### hook_form_FORM_ID_alter()
Modify a specific form by form ID.
```php
function mymodule_form_node_article_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
  $form['actions']['submit']['#value'] = t('Save Article');
}
```

## Node Hooks

### hook_node_presave()
Act before a node is saved.
```php
function mymodule_node_presave(Drupal\node\NodeInterface $node) {
  // Modify node before saving
  $node->setTitle('Prefix: ' . $node->getTitle());
}
```

### hook_node_insert()
Respond to node creation.
```php
function mymodule_node_insert(Drupal\node\NodeInterface $node) {
  // Log or notify on new node
}
```

### hook_node_update()
Respond to node updates.
```php
function mymodule_node_update(Drupal\node\NodeInterface $node) {
  // React to node changes
}
```

### hook_node_delete()
Respond to node deletion.
```php
function mymodule_node_delete(Drupal\node\NodeInterface $node) {
  // Clean up related data
}
```

### hook_node_access()
Control access to nodes.
```php
function mymodule_node_access(\Drupal\node\NodeInterface $node, $op, \Drupal\Core\Session\AccountInterface $account) {
  // Return AccessResult::allowed(), AccessResult::forbidden(), or AccessResult::neutral()
}
```

## Page & Theme Hooks

### hook_preprocess_HOOK()
Preprocess variables for templates.
```php
function mymodule_preprocess_page(&$variables) {
  $variables['custom_var'] = 'Custom value';
}

function mymodule_preprocess_node(&$variables) {
  $node = $variables['node'];
  $variables['custom_date'] = $node->getCreatedTime();
}
```

### hook_theme()
Register theme implementations.
```php
function mymodule_theme($existing, $type, $theme, $path) {
  return [
    'mymodule_custom_template' => [
      'variables' => [
        'title' => NULL,
        'content' => NULL,
      ],
      'template' => 'mymodule-custom-template',
    ],
  ];
}
```

### hook_page_attachments()
Add attachments (CSS, JS, metatags) to a page.
```php
function mymodule_page_attachments(array &$attachments) {
  $attachments['#attached']['library'][] = 'mymodule/global-styling';
  $attachments['#attached']['drupalSettings']['mymodule']['setting'] = 'value';
}
```

### hook_theme_suggestions_HOOK()
Provide theme suggestions.
```php
function mymodule_theme_suggestions_page_alter(array &$suggestions, array $variables) {
  if ($node = \Drupal::routeMatch()->getParameter('node')) {
    $suggestions[] = 'page__node__' . $node->bundle();
  }
}
```

## Menu & Routing Hooks

### hook_menu_links_discovered_alter()
Alter menu links.
```php
function mymodule_menu_links_discovered_alter(&$links) {
  // Modify menu links
}
```

## Block Hooks

### hook_block_access()
Control access to blocks.
```php
function mymodule_block_access(\Drupal\block\Entity\Block $block, $operation, \Drupal\Core\Session\AccountInterface $account) {
  // Return AccessResult
}
```

### hook_block_view_alter()
Alter block content.
```php
function mymodule_block_view_alter(array &$build, \Drupal\Core\Block\BlockPluginInterface $block) {
  if ($build['#plugin_id'] == 'system_branding_block') {
    $build['#pre_render'][] = 'mymodule_prerender_branding';
  }
}
```

## User Hooks

### hook_user_login()
Act when user logs in.
```php
function mymodule_user_login($account) {
  // Log user login, set message, etc.
}
```

### hook_user_logout()
Act when user logs out.
```php
function mymodule_user_logout($account) {
  // Cleanup on logout
}
```

## Cron Hooks

### hook_cron()
Perform periodic tasks.
```php
function mymodule_cron() {
  // Run periodic maintenance
  $request_time = \Drupal::time()->getRequestTime();
  \Drupal::state()->set('mymodule.last_cron', $request_time);
}
```

## Installation Hooks

### hook_install()
Perform setup tasks when module is installed.
```php
function mymodule_install() {
  // Set default configuration
  \Drupal::configFactory()->getEditable('mymodule.settings')
    ->set('default_value', 'example')
    ->save();
}
```

### hook_uninstall()
Clean up when module is uninstalled.
```php
function mymodule_uninstall() {
  // Delete configuration and data
  \Drupal::state()->delete('mymodule.settings');
}
```

### hook_schema()
Define database tables (in .install file).
```php
function mymodule_schema() {
  $schema['mymodule_table'] = [
    'description' => 'Stores custom data',
    'fields' => [
      'id' => [
        'type' => 'serial',
        'not null' => TRUE,
        'description' => 'Primary Key',
      ],
      'name' => [
        'type' => 'varchar',
        'length' => 255,
        'not null' => TRUE,
        'default' => '',
        'description' => 'Name field',
      ],
    ],
    'primary key' => ['id'],
  ];
  return $schema;
}
```

## Token Hooks

### hook_tokens()
Provide custom tokens.
```php
function mymodule_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];
  if ($type == 'node') {
    foreach ($tokens as $name => $original) {
      if ($name == 'custom-token') {
        $replacements[$original] = 'Custom value';
      }
    }
  }
  return $replacements;
}
```

### hook_token_info()
Define custom tokens.
```php
function mymodule_token_info() {
  $info['tokens']['node']['custom-token'] = [
    'name' => t('Custom Token'),
    'description' => t('A custom token for nodes.'),
  ];
  return $info;
}
```

## Best Practices

1. **Naming**: Always prefix hooks with your module name
2. **Type hints**: Use proper type hints for all parameters
3. **Documentation**: Add docblocks explaining what the hook does
4. **Performance**: Be mindful of hooks that run frequently (e.g., hook_entity_view)
5. **Dependencies**: Inject services via dependency injection in classes, use `\Drupal::service()` in .module files
6. **Deprecations**: Check for deprecated hooks when upgrading Drupal versions
