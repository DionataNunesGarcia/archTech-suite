# Drupal Module Structure Reference

Complete guide to Drupal module structure and development patterns for Drupal 8-11+.

## Basic Module Structure

```
mymodule/
├── mymodule.info.yml          # Module metadata (required)
├── mymodule.module            # Hook implementations
├── mymodule.routing.yml       # Route definitions
├── mymodule.services.yml      # Service definitions
├── mymodule.permissions.yml   # Custom permissions
├── mymodule.links.menu.yml    # Menu links
├── mymodule.links.task.yml    # Tab links
├── mymodule.links.action.yml  # Action links
├── composer.json              # Composer dependencies
├── config/
│   ├── install/              # Default configuration
│   └── schema/               # Configuration schema
│       └── mymodule.schema.yml
├── src/
│   ├── Controller/           # Controllers
│   ├── Form/                 # Forms
│   ├── Plugin/               # Plugins (Blocks, Fields, etc.)
│   │   └── Block/
│   ├── Entity/               # Custom entities
│   ├── EventSubscriber/      # Event subscribers
│   └── Service/              # Custom services
├── templates/                # Twig templates
└── tests/
    ├── src/
    │   ├── Unit/            # Unit tests
    │   ├── Kernel/          # Kernel tests
    │   └── Functional/      # Functional tests
    └── modules/             # Test modules
```

## Module Info File (mymodule.info.yml)

Required metadata for every module:

```yaml
name: My Module
description: 'Description of what the module does.'
type: module
core_version_requirement: ^9 || ^10 || ^11
package: Custom

# Optional dependencies
dependencies:
  - drupal:node
  - drupal:views
  - webform:webform

# Optional configuration dependencies
config_devel:
  install:
    - core.entity_view_mode.node.teaser
  optional:
    - views.view.frontpage

# Optional PHP requirement
php: ^8.1

# Optional - mark as hidden
hidden: true

# Optional - module version
version: 1.0.0
```

## Routing (mymodule.routing.yml)

Define routes for pages and controllers:

```yaml
# Simple page route
mymodule.hello:
  path: '/hello'
  defaults:
    _controller: '\Drupal\mymodule\Controller\HelloController::content'
    _title: 'Hello World'
  requirements:
    _permission: 'access content'

# Route with parameters
mymodule.user_page:
  path: '/user/{user}/custom'
  defaults:
    _controller: '\Drupal\mymodule\Controller\UserController::view'
  requirements:
    _permission: 'access content'
    user: \d+
  options:
    parameters:
      user:
        type: entity:user

# Form route
mymodule.settings_form:
  path: '/admin/config/mymodule/settings'
  defaults:
    _form: '\Drupal\mymodule\Form\SettingsForm'
    _title: 'My Module Settings'
  requirements:
    _permission: 'administer site configuration'

# Route with custom access
mymodule.custom_access:
  path: '/custom-access'
  defaults:
    _controller: '\Drupal\mymodule\Controller\CustomController::content'
  requirements:
    _custom_access: '\Drupal\mymodule\Access\CustomAccessCheck::access'
```

## Controllers

### Basic Controller

**src/Controller/HelloController.php**
```php
<?php

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for My Module routes.
 */
class HelloController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function content() {
    $build['content'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Hello World!'),
    ];
    return $build;
  }

}
```

### Controller with Dependency Injection

**src/Controller/AdvancedController.php**
```php
<?php

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller with dependency injection.
 */
class AdvancedController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs an AdvancedController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Builds the response.
   */
  public function content() {
    // Use injected services
    $count = $this->database->query('SELECT COUNT(*) FROM {node}')->fetchField();

    $build['content'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Total nodes: @count', ['@count' => $count]),
    ];
    return $build;
  }

}
```

## Forms

### Configuration Form

**src/Form/SettingsForm.php**
```php
<?php

namespace Drupal\mymodule\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure My Module settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mymodule_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mymodule.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('mymodule.settings');

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $config->get('api_key'),
      '#required' => TRUE,
    ];

    $form['enable_feature'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Feature'),
      '#default_value' => $config->get('enable_feature'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('mymodule.settings')
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('enable_feature', $form_state->getValue('enable_feature'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
```

### Simple Form

**src/Form/ContactForm.php**
```php
<?php

namespace Drupal\mymodule\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a contact form.
 */
class ContactForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mymodule_contact_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('message')) < 10) {
      $form_state->setErrorByName('message', $this->t('Message is too short.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Process form submission
    $this->messenger()->addStatus($this->t('Thank you for your message!'));
  }

}
```

## Services (mymodule.services.yml)

```yaml
services:
  mymodule.custom_service:
    class: Drupal\mymodule\Service\CustomService
    arguments: ['@entity_type.manager', '@current_user', '@logger.factory']

  mymodule.event_subscriber:
    class: Drupal\mymodule\EventSubscriber\MyModuleSubscriber
    arguments: ['@current_user']
    tags:
      - { name: event_subscriber }
```

### Custom Service

**src/Service/CustomService.php**
```php
<?php

namespace Drupal\mymodule\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Psr\Log\LoggerInterface;

/**
 * Custom service for My Module.
 */
class CustomService {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a CustomService object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user, LoggerInterface $logger) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
    $this->logger = $logger;
  }

  /**
   * Performs a custom operation.
   */
  public function doSomething() {
    $this->logger->info('Custom service method called by user @uid', [
      '@uid' => $this->currentUser->id(),
    ]);
    // Custom logic here
  }

}
```

## Plugins

### Block Plugin

**src/Plugin/Block/CustomBlock.php**
```php
<?php

namespace Drupal\mymodule\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a custom block.
 *
 * @Block(
 *   id = "mymodule_custom_block",
 *   admin_label = @Translation("Custom Block"),
 *   category = @Translation("Custom"),
 * )
 */
class CustomBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'custom_text' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['custom_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom Text'),
      '#default_value' => $this->configuration['custom_text'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['custom_text'] = $form_state->getValue('custom_text');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => $this->configuration['custom_text'],
    ];
  }

}
```

## Permissions (mymodule.permissions.yml)

```yaml
administer mymodule:
  title: 'Administer My Module'
  description: 'Configure My Module settings'
  restrict access: true

access mymodule content:
  title: 'Access My Module content'
  description: 'View content provided by My Module'

# Dynamic permissions callback
permission_callbacks:
  - Drupal\mymodule\MyModulePermissions::permissions
```

## Menu Links (mymodule.links.menu.yml)

```yaml
mymodule.admin:
  title: 'My Module'
  description: 'My Module settings'
  route_name: mymodule.settings_form
  parent: system.admin_config
  weight: 10
```

## Configuration Schema (config/schema/mymodule.schema.yml)

```yaml
mymodule.settings:
  type: config_object
  label: 'My Module settings'
  mapping:
    api_key:
      type: string
      label: 'API Key'
    enable_feature:
      type: boolean
      label: 'Enable Feature'
```

## Event Subscribers

**src/EventSubscriber/MyModuleSubscriber.php**
```php
<?php

namespace Drupal\mymodule\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * My Module event subscriber.
 */
class MyModuleSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['onRequest', 0],
    ];
  }

  /**
   * Kernel request event handler.
   */
  public function onRequest(RequestEvent $event) {
    // React to request event
  }

}
```

## Best Practices

1. **Namespacing**: Follow PSR-4 autoloading standards
2. **Dependency Injection**: Use DI in classes, `\Drupal::service()` in .module files
3. **Coding Standards**: Follow Drupal coding standards (use PHPCS)
4. **Documentation**: Add comprehensive docblocks
5. **Security**: Sanitize output, validate input, check permissions
6. **Performance**: Cache when possible, avoid loading unnecessary data
7. **Testing**: Write unit, kernel, and functional tests
8. **Configuration**: Use config entities for exportable configuration
9. **Hooks**: Implement hooks in .module file, not in classes
10. **Services**: Create reusable services for business logic
