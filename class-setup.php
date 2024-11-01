<?php
/**
 * Setup Swift Control.
 *
 * @package Swift_Control
 */

namespace SwiftControl;

/**
 * Setup Swift Control.
 */
class Setup {
	/**
	 * Setup action & filter hooks.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'setup' ) );
	}

	/**
	 * Setup action & filters.
	 */
	public function setup() {
		// Stop if Swift Control Pro is active.
		if ( defined( 'SWIFT_CONTROL_PRO_PLUGIN_URL' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'setup_text_domain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 999 );
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
		add_action( 'wp', array( $this, 'check_page' ) );
		add_action( 'wp', array( $this, 'remove_admin_bar' ) );

		// Process export-import.
		add_action( 'admin_init', array( $this, 'process_export' ) );
		add_action( 'admin_init', array( $this, 'process_import' ) );

		add_action( 'wp_ajax_swift_control_change_widgets_order', array( new Ajax\Change_Widgets_Order(), 'ajax' ) );
		add_action( 'wp_ajax_swift_control_change_widget_settings', array( new Ajax\Change_Widget_Settings(), 'ajax' ) );
		add_action( 'wp_ajax_swift_control_save_general_settings', array( new Ajax\Save_General_Settings(), 'ajax' ) );
		add_action( 'wp_ajax_swift_control_save_position', array( new Ajax\Save_Position(), 'ajax' ) );
		// add_action( 'wp_ajax_sc_discount_notice_dismissal', array( $this, 'dismiss_discount_notice' ) );
		add_action( 'wp_ajax_swift_control_migration', array( new Ajax\Migration(), 'ajax' ) );

		add_filter( 'plugin_action_links', array( $this, 'add_settings_link' ), 10, 4 );

		add_action( 'admin_notices', array( $this, 'migration_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'migration_notice_scripts' ) );

		// add_action( 'admin_notices', array( $this, 'discount_notice' ) );
		// add_action( 'admin_enqueue_scripts', array( $this, 'discount_notice_script' ) );
	}

	/**
	 * Setup textdomain.
	 */
	public function setup_text_domain() {
		load_plugin_textdomain( 'swift-control', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Notice about migration to the new "Better Admin Bar".
	 *
	 * @return void
	 */
	public function migration_notice() {

		$current_user = wp_get_current_user();
		?>

		<div class="notice notice-error swift-control-migration-notice">

			<div class="notice-body">
				<div class="notice-icon">
					<img src="<?php echo esc_url( SWIFT_CONTROL_PLUGIN_URL ); ?>/assets/img/logo.png">
				</div>
				<div class="notice-content">
					<h2>Swift Control is now Better Admin Bar!</h2>
					<p>
						Dear <?php echo esc_html( $current_user->display_name ); ?>, we're excited to let you know that Swift Control is now <strong>Better Admin Bar</strong>.
					</p>

					<p>
						<strong>What does that mean for me?</strong><br>
						Everything will stay the same. In fact, we have added some amazing new features to the plugin!<br>
					</p>

					<p>
						Please click the button below to migrate to the latest version.
					</p>

					<p>
						<a href="" style="font-weight: 700;" class="button button-primary swift-control-button swift-control-migration-button">
							Start Migration
						</a>
					</p>

					<div class="swift-control-migration-statuses">
						<div class="swift-control-migration-status migration-failed">
							<i class="dashicons dashicons-no"></i>
							<span>Migration failed:</span> <span class="error-message"></span>
						</div>
						<div class="swift-control-migration-status old-swift-control-uninstalled">
							<span class="loader"></span>
							<i class="dashicons dashicons-yes"></i>
							<span class="process-message">Old Swift Control is uninstalled.</span>
						</div>
						<div class="swift-control-migration-status new-better-admin-bar-installed">
							<span class="loader"></span>
							<i class="dashicons dashicons-yes"></i>
							<span class="process-message">New Better Admin Bar is installed.</span>
						</div>
						<div class="swift-control-migration-status new-better-admin-bar-activated">
							<span class="loader"></span>
							<i class="dashicons dashicons-yes"></i>
							<span class="process-message">New Better Admin Bar is activated.</span>
						</div>
					</div>
				</div>
			</div>

		</div>

		<?php

	}

	/**
	 * Print the migration styles.
	 */
	public function print_migration_styles() {

		echo '<style type="text/css">
			' . file_get_contents( SWIFT_CONTROL_PLUGIN_DIR . '/assets/css/swift-control-admin.css' ) . '
		</style>';

	}

	/**
	 * Enqueue the migration scripts.
	 */
	public function migration_notice_scripts() {

		wp_enqueue_script('updates');

		wp_enqueue_style( 'swift-control-discount', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/discount-notice.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );

		wp_enqueue_style( 'swift-control-admin', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/swift-control-admin.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );

		wp_enqueue_script( 'swift-control-migration', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/migration.js', array( 'jquery' ), SWIFT_CONTROL_PLUGIN_VERSION, true );

		$old_plugin_slug     = 'swift-control';
		$old_plugin_basename = $old_plugin_slug . '/' . $old_plugin_slug . '.php';

		$new_plugin_slug     = 'better-admin-bar';
		$new_plugin_basename = $new_plugin_slug . '/' . $new_plugin_slug . '.php';

		$activation_url = add_query_arg(
			array(
				'action'        => 'activate',
				'plugin'        => rawurlencode( $new_plugin_basename ),
				'plugin_status' => 'all',
				'paged'         => '1',
				'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $new_plugin_basename ),
			),
			esc_url( network_admin_url( 'plugins.php' ) )
		);

		$js_objects = array(
			'redirectUrl' => admin_url( 'options-general.php?page=better-admin-bar' ),
			'oldPlugin'   => [
				'slug'     => $old_plugin_slug,
				'basename' => $old_plugin_basename,
			],
			'newPlugin'   => [
				'slug'          => $new_plugin_slug,
				'basename'      => $new_plugin_basename,
				'activationUrl' => $activation_url,
			],
			'nonces'      => array(
				'migration' => wp_create_nonce( 'swift_control_migration' ),
			),
		);

		wp_localize_script(
			'swift-control-migration',
			'SwiftControlMigration',
			$js_objects
		);

	}

	/**
	 * Admin discount notice.
	 */
	public function discount_notice() {

		// Stop here if notice has been dismissed.
		if ( ! empty( get_option( 'swift_control_discontinue_message', 0 ) ) ) {
			return;
		}

		// Stop here if current user can't manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>

		<div class="notice notice-info swift-control-discontinue-notice is-dismissible">
			<h2>Get 50% OFF Better Admin Bar PRO!</h2>

			<p>
				Not going to update to the new Better Admin Bar free version? Would like to upgrade directly to Better Admin Bar PRO?<br>
				As a valued user of our plugin we would like to offer you a <strong style="color: green;">50% discount</strong> on <strong>Better Admin Bar PRO</strong> (yearly plan) for the first year.
			</p>

			<p>Grab it while it lasts.</p>

			<p><a target="_blank" href="https://betteradminbar.com/?edd_action=add_to_cart&download_id=241&edd_options%5Bprice_id%5D=1&discount=SCPRO50" style="font-weight: 700;" class="button button-primary">Upgrade to Better Admin Bar PRO and save 50%</a> <strong>Coupon limited to 100 users!</strong></p>
		</div>

		<?php

	}

	/**
	 * Script that handles discount notice dismissal.
	 */
	public function discount_notice_script() {

		// Stop here if notice has been dismissed.
		if ( ! empty( get_option( 'swift_control_discontinue_message', 0 ) ) ) {
			return;
		}

		// Stop here if current user can't manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_script( 'swift-control-discount', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/discount-notice.js', array( 'jquery' ), SWIFT_CONTROL_PLUGIN_VERSION, true );

		wp_localize_script(
			'swift-control-discount',
			'swiftControlDismissal',
			array(
				'nonces' => array(
					'dismissalNonce' => wp_create_nonce( 'Swift_Control_Dismiss_Discount_Notice' ),
				),
			)
		);

	}

	/**
	 * Dismiss discount notice.
	 */
	public function dismiss_discount_notice() {
		$nonce   = isset( $_POST['nonce'] ) ? $_POST['nonce'] : 0;
		$dismiss = isset( $_POST['dismiss'] ) ? absint( $_POST['dismiss'] ) : 0;

		if ( empty( $dismiss ) ) {
			wp_send_json_error( __( 'Invalid Request', 'swift-control' ) );
		}

		if ( ! wp_verify_nonce( $nonce, 'Swift_Control_Dismiss_Discount_Notice' ) ) {
			wp_send_json_error( __( 'Invalid Token', 'swift-control' ) );
		}

		update_option( 'swift_control_discontinue_message', 1 );
		wp_send_json_success( __( 'Discount notice has been dismissed', 'swift-control' ) );
	}

	/**
	 * Enqueue admin styles & scripts.
	 */
	public function admin_scripts() {
		$current_screen = get_current_screen();

		if ( 'settings_page_swift-control' !== $current_screen->id ) {
			return;
		}

		// Font Awesome 5.
		$this->deregister_font_awesome();
		wp_enqueue_style( 'font-awesome', SWIFT_CONTROL_PLUGIN_URL . '/assets/vendor/fontawesome-free/css/all.min.css', array(), '5.14.0' );

		// Icon picker.
		wp_enqueue_style( 'icon-picker', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/icon-picker.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );

		// Select2.
		wp_enqueue_style( 'select2', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/select2.min.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );

		// Settings page styling.
		wp_enqueue_style( 'settings-page', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/settings-page.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );
		wp_enqueue_style( 'setting-fields', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/setting-fields.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );

		// Swift Control admin styling.
		wp_enqueue_style( 'swift-control-admin', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/swift-control-admin.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );

		// Color picker dependency.
		wp_enqueue_style( 'wp-color-picker' );

		// jQuery UI dependencies.
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		// Select2.
		wp_enqueue_script( 'select2', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/select2.min.js', array( 'jquery' ), SWIFT_CONTROL_PLUGIN_VERSION, true );

		// Icon picker.
		wp_enqueue_script( 'icon-picker', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/icon-picker.js', array( 'jquery' ), SWIFT_CONTROL_PLUGIN_VERSION, true );

		$icons = file_get_contents( SWIFT_CONTROL_PLUGIN_DIR . '/assets/json/fontawesome5.json' );
		$icons = json_decode( $icons, true );
		$icons = $icons ? $icons : array();

		wp_localize_script(
			'icon-picker',
			'iconPickerIcons',
			$icons
		);

		// Color picker alpha.
		wp_enqueue_script( 'wp-color-picker-alpha', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/wp-color-picker-alpha.js', array( 'wp-color-picker', 'wp-i18n' ), '2.1.3', true );

		wp_enqueue_script( 'swift-control-widget-settings', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/swift-control-widget-settings.js', array( 'jquery-ui-sortable', 'icon-picker' ), SWIFT_CONTROL_PLUGIN_VERSION, true );
		wp_enqueue_script( 'swift-control-general-settings', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/swift-control-general-settings.js', array( 'select2', 'wp-color-picker-alpha' ), SWIFT_CONTROL_PLUGIN_VERSION, true );

		wp_localize_script(
			'swift-control-widget-settings',
			'SwiftControl',
			array(
				'nonces' => array(
					'changeWidgetsOrder'   => wp_create_nonce( 'change_widgets_order' ),
					'changeWidgetSettings' => wp_create_nonce( 'change_widget_settings' ),
					'saveGeneralSettings'  => wp_create_nonce( 'save_general_settings' ),
				),
				'labels' => array(
					'edit' => __( 'Edit', 'swift-control' ),
					'save' => __( 'Save', 'swift-control' ),
				),
			)
		);
	}

	/**
	 * Add settings link to plugin list page.
	 *
	 * @param array  $actions     An array of plugin action links.
	 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array  $plugin_data An array of plugin data. See `get_plugin_data()`.
	 * @param string $context     The plugin context. By default this can include 'all', 'active', 'inactive',
	 *                            'recently_activated', 'upgrade', 'mustuse', 'dropins', and 'search'.
	 *
	 * @return array The modified plugin action links.
	 */
	public function add_settings_link( $actions, $plugin_file, $plugin_data, $context ) {
		if ( SWIFT_CONTROL_PLUGIN_BASENAME === $plugin_file ) {
			$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=swift-control' ) ) . '">' . __( 'Settings', 'swift-control' ) . '</a>';

			array_unshift( $actions, $settings_link );
		}

		return $actions;
	}

	/**
	 * Add submenu under "Settings" menu item.
	 */
	public function add_submenu_page() {
		add_options_page( __( 'WP Swift Control', 'swift-control' ), __( 'Swift Control', 'swift-control' ), 'delete_others_posts', 'swift-control', array( $this, 'page_output' ) );
	}

	/**
	 * Swift Control page output.
	 */
	public function page_output() {
		require __DIR__ . '/templates/admin-page.php';
	}

	/**
	 * Whether or not to show the widgets in frontend.
	 * Only display the widgets when there's any active one.
	 */
	public function check_page() {
		// Only show to logged-in admin users.
		if ( ! is_user_logged_in() || ! current_user_can( 'delete_others_posts' ) ) {
			return;
		}

		// Stop if this is a customizer preview.
		if ( is_customize_preview() ) {
			return;
		}

		// Stop if current page is in edit mode inside page builder.
		if ( swift_control_is_inside_page_builder() ) {
			return;
		}

		// Stop if swift control doesn't have active widgets.
		if ( ! swift_control_has_active_widgets() ) {
			return;
		}

		if ( ! apply_filters( 'swift_control_frontend_display', true ) ) {
			return;
		}

		add_filter( 'body_class', array( $this, 'add_body_class' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_styles' ) );
		add_action( 'wp_footer', array( $this, 'frontend_output' ), 5 );
	}

	/**
	 * Add a class to body_class().
	 *
	 * @param array $classes The body classes.
	 * @return array $classes The modified body classes.
	 */
	public function add_body_class( $classes ) {
		$classes[] = 'has-swift-control';

		return $classes;
	}

	/**
	 * Remove admin bar on frontend for certain roles.
	 */
	public function remove_admin_bar() {
		$misc_settings = swift_control_get_misc_settings();

		if ( ! isset( $misc_settings['remove_admin_bar'] ) || empty( $misc_settings['remove_admin_bar'] ) ) {
			return;
		}

		$selected_roles = $misc_settings['remove_admin_bar'];

		// Backward compatibility: old value's format is int (0 / 1).
		if ( is_numeric( $selected_roles ) ) {
			add_filter( 'show_admin_bar', '__return_false' );
			return;
		}

		if ( in_array( 'all', $misc_settings['remove_admin_bar'], true ) ) {
			add_filter( 'show_admin_bar', '__return_false' );
			return;
		} else {
			$current_user = wp_get_current_user();

			foreach ( $current_user->roles as $role ) {
				if ( in_array( $role, $selected_roles, true ) ) {
					add_filter( 'show_admin_bar', '__return_false' );
					break;
				}
			}
		}
	}

	/**
	 * Enqueue frontend assets.
	 */
	public function frontend_scripts() {
		$misc_settings       = swift_control_get_misc_settings();
		$remove_font_awesome = isset( $misc_settings['remove_font_awesome'] ) ? absint( $misc_settings['remove_font_awesome'] ) : 0;

		/**
		 * If "Don't enqueue Font Awesome" setting is un-checked (disabled),
		 * then deregister any existing Font Awesome css, and enqueue our version.
		 */
		if ( ! $remove_font_awesome ) {
			$this->deregister_font_awesome();
			wp_enqueue_style( 'font-awesome', SWIFT_CONTROL_PLUGIN_URL . '/assets/vendor/fontawesome-free/css/all.min.css', array(), '5.14.0' );
		}

		// Swift Control frontend styling.
		wp_enqueue_style( 'swift-control', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/swift-control.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );

		// Swift Control frontend script.
		wp_enqueue_script( 'swift-interact', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/interact.min.js', array( 'jquery' ), SWIFT_CONTROL_PLUGIN_VERSION, true );
		wp_enqueue_script( 'swift-control', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/swift-control.js', array( 'jquery', 'swift-interact' ), SWIFT_CONTROL_PLUGIN_VERSION, true );
	}

	/**
	 * Dequeue & deregister existing Font Awesome
	 * to prevent conflict with our enqueue.
	 */
	public function deregister_font_awesome() {

		wp_dequeue_style( 'font-awesome' );
		wp_dequeue_style( 'fontawesome' );

		wp_deregister_style( 'font-awesome' );
		wp_deregister_style( 'fontawesome' );

	}

	/**
	 * Output the widgets to frontend.
	 */
	public function frontend_output() {
		require __DIR__ . '/templates/frontend-output.php';
	}

	/**
	 * Output the inline styles to frontend.
	 */
	public function inline_styles() {

		$color_settings = swift_control_get_color_settings();
		$default_colors = swift_control_get_default_color_settings();
		$css            = '';

		if ( $color_settings['setting_button_bg_color'] !== $default_colors['setting_button_bg_color'] ) {
			$css .= '.swift-control-widgets .swift-control-widget-setting .swift-control-widget-link, .swift-control-widgets .swift-control-widget-setting .swift-control-widget-link:hover {';
			$css .= 'background-color: ' . esc_attr( $color_settings['setting_button_bg_color'] ) . ';';
			$css .= '}';

			$css .= '.swift-control-widgets .swift-control-widget-setting::after {';
			$css .= 'color: ' . esc_attr( $color_settings['setting_button_bg_color'] ) . ';';
			$css .= '}';
		}

		if ( $color_settings['setting_button_icon_color'] !== $default_colors['setting_button_icon_color'] ) {
			$css .= '.swift-control-widgets .swift-control-widget-setting a {';
			$css .= 'color: ' . esc_attr( $color_settings['setting_button_icon_color'] ) . ';';
			$css .= '}';
		}

		if ( $color_settings['widget_bg_color_hover'] !== $default_colors['widget_bg_color_hover'] ) {
			$css .= '.swift-control-widgets .swift-control-widget-link:hover {';
			$css .= 'background-color: ' . esc_attr( $color_settings['widget_bg_color_hover'] ) . ';';
			$css .= '}';
		}

		if ( $color_settings['widget_bg_color'] !== $default_colors['widget_bg_color'] ) {
			$css .= '.swift-control-widgets .swift-control-widget-link {';
			$css .= 'background-color: ' . esc_attr( $color_settings['widget_bg_color'] ) . ';';
			$css .= '}';

			$css .= '.swift-control-widgets .is-disabled .swift-control-widget-link {';
			$css .= 'background-color: ' . esc_attr( $color_settings['widget_bg_color'] ) . ';';
			$css .= '}';
		}

		if ( $color_settings['widget_icon_color'] !== $default_colors['widget_icon_color'] ) {
			$css .= '.swift-control-widgets .swift-control-widget-link {';
			$css .= 'color: ' . esc_attr( $color_settings['widget_icon_color'] ) . ';';
			$css .= '}';
		}

		wp_add_inline_style( 'swift-control', $css );

	}

	/**
	 * Process widget export.
	 */
	public function process_export() {

		if ( ! isset( $_POST['swift_control_action'] ) || 'export' !== $_POST['swift_control_action'] ) {
			return;
		}

		if ( ! isset( $_POST['swift_control_export_nonce'] ) || empty( $_POST['swift_control_export_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['swift_control_export_nonce'], 'swift_control_export_widgets' ) ) {
			return;
		}

		$exporter = new Helpers\Export();

		$exporter->export();

	}

	/**
	 * Process widget import.
	 */
	public function process_import() {

		if ( ! isset( $_FILES['swift_control_import_file'] ) || empty( $_FILES['swift_control_import_file'] ) ) {
			return;
		}

		if ( ! isset( $_POST['swift_control_action'] ) || 'import' !== $_POST['swift_control_action'] ) {
			return;
		}

		if ( ! isset( $_POST['swift_control_import_nonce'] ) || empty( $_POST['swift_control_import_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['swift_control_import_nonce'], 'swift_control_import_widgets' ) ) {
			return;
		}

		$importer = new Helpers\Import();

		$importer->import();

	}

}
