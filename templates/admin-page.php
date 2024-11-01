<?php
/**
 * Swift Control page template.
 *
 * @package Swift_Control
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="wrap settingstuff swift-control-settings">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php
	require_once __DIR__ . '/widget-settings.php';
	?>

</div>
