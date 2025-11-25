<?php
/**
 * Plugin Name: Custom User Orchestrator
 * Description: Fetches and caches remote users, exposes REST endpoint, admin UI, WP-CLI and Gutenberg block.
 * Version: 1.0.0
 * Author: Shammas Mustafa
 * Text Domain: custom-user-orchestrator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Cuo\Plugin;

add_action( 'plugins_loaded', function() {
    $plugin = new Plugin( __FILE__ );
    $plugin->run();
} );
