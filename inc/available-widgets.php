<?php
/**
 * Default available widget items.
 *
 * @package Swift_Control
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return array(
	'new_page' => array(
		'text'       => __( 'New Page', 'swift-control' ),
		'url'        => admin_url( 'post-new.php?post_type=page' ),
		'new_tab'    => false,
		'icon_class' => 'fas fa-file-alt',
	),
	'new_post' => array(
		'text'       => __( 'New Post', 'swift-control' ),
		'url'        => admin_url( 'post-new.php' ),
		'new_tab'    => false,
		'icon_class' => 'fas fa-pencil-alt',
	),
	'themes'   => array(
		'text'       => __( 'Themes', 'swift-control' ),
		'url'        => admin_url( 'themes.php' ),
		'new_tab'    => false,
		'icon_class' => 'far fa-images',
	),
	'plugins'  => array(
		'text'       => __( 'Plugins', 'swift-control' ),
		'url'        => admin_url( 'plugins.php' ),
		'new_tab'    => false,
		'icon_class' => 'fas fa-plug',
	),
);
