<?php

/**
 * @file
 * ArchTech Suite DDEV settings — manually managed.
 */

$host = "db";
$port = 5432;
$driver = "pgsql";

$databases['default']['default']['database'] = "db";
$databases['default']['default']['username'] = "db";
$databases['default']['default']['password'] = "db";
$databases['default']['default']['host'] = $host;
$databases['default']['default']['port'] = $port;
$databases['default']['default']['driver'] = $driver;

$settings['hash_salt'] = 'df885a841f4cffe580b418d908718e3b784a8f39fb561e870f814b1f50878aab';

// Recommended setting for Drupal 10 only
$settings['state_cache'] = TRUE;

// This will prevent Drupal from setting read-only permissions on sites/default.
$settings['skip_permissions_hardening'] = TRUE;

// This will ensure the site can only be accessed through the intended host
// names. Additional host patterns can be added for custom configurations.
$settings['trusted_host_patterns'] = ['.*'];

// Set $settings['config_sync_directory'] if not set in settings.php.
if (empty($settings['config_sync_directory'])) {
  $settings['config_sync_directory'] = 'sites/default/files/sync';
}

// Override drupal/symfony_mailer default config to use Mailpit.
$config['symfony_mailer.settings']['default_transport'] = 'sendmail';
$config['symfony_mailer.mailer_transport.sendmail']['plugin'] = 'smtp';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['user'] = '';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['pass'] = '';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['host'] = 'localhost';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['port'] = '1025';

// Enable verbose logging for errors.
$config['system.logging']['error_level'] = 'verbose';

// ---------------------------------------------------------------------------
// Redis — connection settings (enable after `ddev composer require drupal/redis`)
// ---------------------------------------------------------------------------
$settings['redis.connection']['host'] = 'redis';
$settings['redis.connection']['port'] = 6379;
$settings['redis.connection']['password'] = NULL;
$settings['redis.connection']['prefix'] = 'archtech';
$settings['cache']['default'] = 'cache.backend.redis';
$settings['cache']['bins']['default'] = 'cache.backend.redis';
$settings['cache']['bins']['entity'] = 'cache.backend.redis';
$settings['cache']['bins']['render'] = 'cache.backend.redis';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.redis';
// Note: example.services.yml omitted intentionally — it overrides
// cache_tags.invalidator.checksum to depend on redis.factory, which
// causes container compilation failures during drush cr on Drupal 11.
// Redis cache bins still function without this override.

// ---------------------------------------------------------------------------
// RabbitMQ — message broker connection (via archtech_events module)
// ---------------------------------------------------------------------------
$settings['rabbitmq.host'] = 'rabbitmq';
$settings['rabbitmq.port'] = 5672;
$settings['rabbitmq.user'] = 'archtech';
$settings['rabbitmq.password'] = 'archtech';
$settings['rabbitmq.vhost'] = '/';
$settings['rabbitmq.management_port'] = 15672;
