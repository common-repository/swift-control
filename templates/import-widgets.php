<?php
/**
 * Swift Control import widgets template.
 *
 * @package Swift_Control
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<form method="post" action="<?php menu_page_url( 'swift-control', true ); ?>" enctype="multipart/form-data">
	<input type="hidden" name="swift_control_action" value="import">
	<?php wp_nonce_field( 'swift_control_import_widgets', 'swift_control_import_nonce' ); ?>

	<div class="neatbox is-smooth has-bigger-heading has-medium-gap">
		<h2><?php _e( 'Import Widgets', 'swift-control' ); ?></h2>

		<div class="neatbox-content has-bottom-gapless">
			<p>
				<?php _e( 'Select the Widgets JSON file you would like to import. When you click the import button below, Swift Control will import the widgets.', 'swift-control' ); ?>
			</p>

			<div class="setting-fields is-gapless">
				<div class="fields-wrapper">
					<label class="block-label" for="swift_control_import_file">Select File</label>
					<input type="file" name="swift_control_import_file" id="swift_control_import_file">
				</div>
			</div>
		</div>

		<?php submit_button( __( 'Import File', 'swift-control' ), 'primary', 'submit_import' ); ?>
	</div>
</form>
