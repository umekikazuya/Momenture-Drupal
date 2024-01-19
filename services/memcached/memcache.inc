<?php

/**
 * @file
 * Contains caching configuration.
 */

/**
 * Use memcache as cache backend.
 *
 * Autoload memcache classes and service container in case module is not
 * installed. Avoids the need to patch core and allows for overriding the
 * default backend when installing Drupal.
 *
 * @see https://www.drupal.org/node/2766509
 */
if (\file_exists($svc_yml = DRUPAL_ROOT . '/modules/contrib/memcache/memcache.services.yml')
  && (($has_mcd = \class_exists(($mcd = 'Memcached'), FALSE)) || \class_exists('Memcache', FALSE))
) {
  if ($has_mcd) {
    $settings['memcache']['extension'] = $mcd;
  }
  if (\class_exists($cac = '\\Composer\\Autoload\\ClassLoader')) {
    $cl = new $cac();
    $cl->addPsr4('Drupal\\memcache\\', DRUPAL_ROOT . '/modules/contrib/memcache/src');
    $cl->register();
    $settings['container_yamls'][] = $svc_yml;
    if ($has_mcd) {
      $settings['memcache']['options'][$mcd::OPT_COMPRESSION] = TRUE;
      $settings['memcache']['options'][$mcd::OPT_TCP_NODELAY] = TRUE;
    }
    $settings['bootstrap_container_definition'] = [
      'parameters' => [],
      'services' => [
        'database' => [
          'class' => '\\Drupal\\Core\\Database\\Connection',
          'factory' => '\\Drupal\\Core\\Database\\Database::getConnection',
          'arguments' => ['default'],
        ],
        'settings' => [
          'class' => '\\Drupal\\Core\\Site\\Settings',
          'factory' => '\\Drupal\\Core\\Site\\Settings::getInstance',
        ],
        'memcache.settings' => [
          'class' => '\\Drupal\\memcache\\MemcacheSettings',
          'arguments' => ['@settings'],
        ],
        'memcache.factory' => [
          'class' => '\\Drupal\\memcache\\Driver\\MemcacheDriverFactory',
          'arguments' => ['@memcache.settings'],
        ],
        'memcache.timestamp.invalidator.bin' => [
          'class' => '\\Drupal\\memcache\\Invalidator\\MemcacheTimestampInvalidator',
          'arguments' => [
            '@memcache.factory',
            'memcache_bin_timestamps',
            0.001,
          ],
        ],
        'memcache.backend.cache.container' => [
          'class' => '\\Drupal\\memcache\\DrupalMemcacheInterface',
          'factory' => ['@memcache.factory', 'get'],
          'arguments' => ['container'],
        ],
        'cache_tags_provider.container' => [
          'class' => '\\Drupal\\Core\\Cache\\DatabaseCacheTagsChecksum',
          'arguments' => ['@database'],
        ],
        'cache.container' => [
          'class' => '\\Drupal\\memcache\\MemcacheBackend',
          'arguments' => [
            'container',
            '@memcache.backend.cache.container',
            '@cache_tags_provider.container',
            '@memcache.timestamp.invalidator.bin',
            '@memcache.settings',
          ],
        ],
      ],
    ];

    /*
     * Configure the servers, clusters and bins.
     *
     * Not need to set this if the only memcache backend is localhost:11211.
     * By default the main settings will be:
     *   $settings['memcache']['servers'] = ['localhost:11211' => 'default'];
     *   $settings['memcache']['bins'] = ['default' => 'default'];
     */
    if (!isset($settings['memcache']['servers'])) {
      $settings['memcache']['servers'] = ['localhost:11211' => 'default'];
    }
    if (!isset($settings['memcache']['bins'])) {
      $settings['memcache']['bins'] = ['default' => 'default'];
    }

    /*
     * Configure the prefixing.
     *
     * If you want to have multiple Drupal installations
     * share memcached instances, you need to include a unique prefix
     * for each Drupal installation in the memcache config in settings.php:
     *   $settings['memcache']['key_prefix'] = 'something_unique';
     */
    if (!isset($settings['memcache']['key_prefix'])) {
      $settings['memcache']['key_prefix'] = '';
    }

    /*
     * Configure the cache class.
     *
     * Make memcache the default cache class, for example:
     *   $settings['cache']['default'] = 'cache.backend.memcache';
     * If you wish to arbitrarily send cache bins to memcache,
     * then you can do the following. E.g. for the cache_render bin:
     *   $settings['cache']['bins']['render'] = 'cache.backend.memcache';
     */
    $settings['cache']['bins']['depcalc'] = 'cache.backend.database';
    $settings['cache']['bins']['bootstrap'] = 'cache.backend.memcache';
    $settings['cache']['bins']['discovery'] = 'cache.backend.memcache';
    $settings['cache']['bins']['config'] = 'cache.backend.memcache';
    $settings['cache']['default'] = 'cache.backend.memcache';
  }
}
