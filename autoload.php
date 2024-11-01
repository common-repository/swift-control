<?php
/**
 * Autoloading
 *
 * @package Swift_Control
 */

namespace SwiftControl;

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Require helper classes.
require __DIR__ . '/helpers/class-export.php';
require __DIR__ . '/helpers/class-import.php';
require __DIR__ . '/helpers.php';

// Require ajax classes.
require __DIR__ . '/ajax/class-change-widgets-order.php';
require __DIR__ . '/ajax/class-change-widget-settings.php';
require __DIR__ . '/ajax/class-save-general-settings.php';
require __DIR__ . '/ajax/class-save-position.php';
require __DIR__ . '/ajax/class-migration.php';

// Require setup classes.
require __DIR__ . '/class-setup.php';

// Init classes.
new Setup();
