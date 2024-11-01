<?php
/**
 * Default active widget keys.
 *
 * @package Swift_Control
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return array(
	'dashboard'        => array(
		'text'       => __( 'Dashboard', 'swift-control' ),
		'url'        => admin_url(),
		'new_tab'    => false,
		'icon_class' => 'fas fa-tachometer-alt',
	),
	'edit_post_type'   => array(
		'text'       => __( 'Edit {Post_Type}', 'swift-control' ),
		'url'        => 'auto',
		'new_tab'    => false,
		'icon_class' => 'fas fa-edit',
	),
	'theme_customizer' => array(
		'text'       => __( 'Customize', 'swift-control' ),
		'url'        => admin_url( 'customize.php' ),
		'new_tab'    => false,
		'icon_class' => 'fas fa-paint-brush',
	),
);
