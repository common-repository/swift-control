<?php
/**
 * Swift Control's frontend output template.
 *
 * @package Swift_Control
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$setting_url = admin_url( 'options-general.php?page=swift-control' );

$active_widgets   = swift_control_get_active_widgets();
$display_settings = swift_control_get_display_settings();

// Get saved widget settings.
$saved_widget_settings = swift_control_get_saved_widget_settings();

$widget_list  = '';
$extra_styles = '';

$transition_value = 75;
$showing_delay    = 0;
$hiding_delay     = ( count( $active_widgets ) - 1 ) * $transition_value;

foreach ( $active_widgets as $widget_key ) {
	// If the setting from database is empty, then take the data from default settings.
	$widget_settings = ! empty( $saved_widget_settings[ $widget_key ] ) ? $saved_widget_settings[ $widget_key ] : array();
	$parsed_settings = swift_control_parse_widget_settings( $widget_key, $widget_settings );

	$widget_class = '';
	$widget_class = swift_control_parse_widget_class( $widget_class, $widget_key );
	$icon_class   = $parsed_settings['icon_class'];
	$widget_name  = $parsed_settings['text'];
	$widget_name  = swift_control_parse_widget_name( $widget_name, $widget_key );
	$widget_url   = $parsed_settings['url'];
	$widget_url   = swift_control_parse_widget_url( $widget_url, $widget_key );
	$tab_target   = $parsed_settings['new_tab'];
	$redirect_url = $parsed_settings['redirect_url'];
	$target_attr  = $tab_target ? 'target="_blank"' : '';
	$target_attr  = 'logout' === $widget_key ? '' : $target_attr;

	ob_start();
	?>

	<li class="swift-control-widget-item <?php echo esc_attr( $widget_class ); ?>" data-widget-key="<?php echo esc_attr( $widget_key ); ?>">
		<a class="swift-control-widget-link" href="<?php echo esc_url( $widget_url ); ?>" <?php echo $target_attr; ?>>
			<i class="<?php echo esc_attr( $icon_class ); ?>"></i>
		</a>
		<span class="swift-control-widget-title"><?php echo esc_html( $widget_name ); ?></span>
	</li>

	<?php
	$widget_list .= ob_get_clean();

	ob_start();
	?>

	.swift-control-widgets [data-widget-key="<?php echo esc_attr( $widget_key ); ?>"] {
		transition-delay: <?php echo esc_attr( $hiding_delay ); ?>ms;
	}

	.swift-control-widgets.is-expanded [data-widget-key="<?php echo esc_attr( $widget_key ); ?>"] {
		transition-delay: <?php echo esc_attr( $showing_delay ); ?>ms;
	}

	<?php
	$extra_styles  .= ob_get_clean();
	$showing_delay += $transition_value;
	$hiding_delay  -= $transition_value;
}
?>

<style>
	<?php
	// We don't hook this extra styles to `class-setup.php` because we need the loop.
	echo $extra_styles;
	?>
</style>

<?php
$position       = array();
$saved_position = get_user_meta( get_current_user_id(), 'swift_control_position', true );
$saved_position = empty( $saved_position ) ? array() : $saved_position;

$position['x']            = isset( $saved_position['x'] ) ? (float) esc_attr( $saved_position['x'] ) : 0;
$position['x_direction']  = isset( $saved_position['x_direction'] ) ? esc_attr( $saved_position['x_direction'] ) : 'left';
$position['y']            = isset( $saved_position['y'] ) ? (float) esc_attr( $saved_position['y'] ) : 0;
$position['y_direction']  = isset( $saved_position['y_direction'] ) ? esc_attr( $saved_position['y_direction'] ) : 'bottom';
$position['y_percentage'] = isset( $saved_position['y_percentage'] ) ? (float) esc_attr( $saved_position['y_percentage'] ) : 0;

$has_arrow_class = $display_settings['remove_indicator'] ? '' : 'has-arrow';
$expanded_class  = $display_settings['expanded'] ? 'is-expanded' : '';
$pinned_class    = '';

if ( ! isset( $saved_position['x_direction'] ) ) {
	if ( is_rtl() ) {
		$position['x_direction'] = 'right';

		$pinned_class = 'is-pinned-right';
	}
} else {
	$pinned_class = 'right' === $position['x_direction'] ? 'is-pinned-right' : '';
}
?>

<ul class="swift-control-widgets is-invisible <?php echo esc_attr( $has_arrow_class ); ?> <?php echo esc_attr( $expanded_class ); ?> <?php echo esc_attr( $pinned_class ); ?>">

	<li class="swift-control-widget-item swift-control-widget-setting">
		<a class="swift-control-widget-link" href="<?php echo esc_url( $setting_url ); ?>" target="_blank">
			<i class="fas fa-cog"></i>
		</a>
	</li>

	<?php echo $widget_list; ?>

</ul>

<div class="swift-control-helper-panels"></div>

<?php
wp_localize_script(
	'swift-control',
	'swiftControlOpt',
	array(
		'size'          => 55,
		'settingButton' => array(
			'hidingDelay' => $showing_delay + 350,
		),
		'position'      => array(
			'x'            => $position['x'],
			'x_direction'  => $position['x_direction'],
			'y'            => $position['y'],
			'y_direction'  => $position['y_direction'],
			'y_percentage' => $position['y_percentage'],
		),
		'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		'nonce'         => wp_create_nonce( 'save_position' ),
	)
);
