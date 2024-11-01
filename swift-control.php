<?php
/**
 * Plugin Name: Swift Control
 * Plugin URI: https://wpswiftcontrol.com/
 * Description: Quickly access all important areas of your website.
 * Version: 2.0.1
 * Author: David Vongries
 * Author URI: https://github.com/MapSteps/
 * Text Domain: swift-control
 *
 * @package Swift_Control
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Helper constants.
define( 'SWIFT_CONTROL_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'SWIFT_CONTROL_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'SWIFT_CONTROL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SWIFT_CONTROL_PLUGIN_VERSION', '2.0.1' );

require __DIR__ . '/autoload.php';
