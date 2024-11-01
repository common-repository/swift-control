<?php
/**
 * Script to run on Swift Control's un-installation.
 *
 * @package Swift_Control
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$misc_settings       = get_option( 'swift_control_misc_settings', array() );
$delete_on_uninstall = isset( $misc_settings['delete_on_uninstall'] ) ? absint( $misc_settings['delete_on_uninstall'] ) : 0;

if ( ! $delete_on_uninstall ) {
	return;
}

delete_option( 'swift_control_active_widgets' );
delete_option( 'swift_control_widget_settings' );
delete_option( 'swift_control_widget_settings' );
delete_option( 'swift_control_color_settings' );
delete_option( 'swift_control_misc_settings' );
delete_option( 'swift_control_discontinue_message' );
