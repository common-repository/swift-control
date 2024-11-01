<?php
/**
 * Change widget order.
 *
 * @package Swift_Control
 */

namespace SwiftControl\Ajax;

/**
 * Class to manage ajax request of changing widget order.
 */
class Migration {
	/**
	 * The old plugin slug.
	 *
	 * @var string
	 */
	private $old_plugin_basename = '';

	/**
	 * Setup the flow.
	 */
	public function ajax() {
		$this->validate();
		$this->migrate();
	}

	/**
	 * Validate the data.
	 */
	public function validate() {
		// Check if nonce is incorrect.
		if ( ! check_ajax_referer( 'swift_control_migration', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid token', 'swift-control' ), 401 );
		}

		// Check against old plugin basename existence.
		if ( ! isset( $_POST['old_plugin_basename'] ) || empty( $_POST['old_plugin_basename'] ) ) {
			wp_send_json_error( __( 'Old plugin basename is not specified', 'swift-control' ), 401 );
		}

		$this->old_plugin_basename = sanitize_text_field( $_POST['old_plugin_basename'] );
	}

	/**
	 * Migrate to Better Admin Bar.
	 */
	public function migrate() {
		if ( ! file_exists( WP_PLUGIN_DIR . '/' . $this->old_plugin_basename ) ) {
			wp_send_json_error( __( 'Old plugin is not found', 'swift-control' ), 403 );
		}

		deactivate_plugins( $this->old_plugin_basename );

		$deletion = delete_plugins( [ $this->old_plugin_basename ] );

		if ( $deletion && ! is_wp_error( $deletion ) ) {
			wp_send_json_success( __( 'Old Swift Control plugin has been removed', 'swift-control' ) );
		} elseif ( is_wp_error( $deletion ) ) {
			wp_send_json_error( $deletion->get_error_message(), 403 );
		} elseif ( null === $deletion ) {
			wp_send_json_error( __( 'Filesystem credentials are required', 'swift-control' ), 403 );
		} else {
			wp_send_json_error( __( 'Old plugin is not specified', 'swift-control' ), 401 );
		}
	}
}
