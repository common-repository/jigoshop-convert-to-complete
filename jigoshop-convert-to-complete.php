<?php
 /**
 * Plugin Name:         Jigoshop Convert to Complete
 * Plugin URI:          http://www.chriscct7.com
 * Description:         Adds the ability to convert bulk orders to complete
 * Author:              Chris Christoff
 * Author URI:          http://www.chriscct7.com
 *
 * Contributors:        chriscct7
 *
 * Version:             1.0
 * Requires at least:   3.5.0
 * Tested up to:        3.6 Beta 3
 *
 * Text Domain:         jtc
 * Domain Path:         /languages/
 *
 * @category            Plugin
 * @copyright           Copyright © 2013 Chris Christoff
 * @author              Chris Christoff
 * @package             JCTC
 */
add_action('admin_menu', 'jigoshop_convert_to_complete_menu_main',100);

function jigoshop_convert_to_complete_menu_main() {
		add_submenu_page( 'edit.php?post_type=shop_order', 'Convert to Complete', 'Convert to Complete', 'manage_options', 'jigoshop-convert-to-complete', 'jigoshop_convert_to_complete');
}

function jigoshop_convert_to_complete() {
?>
<div class="wrap">
		<h2><?php _e('Convert to Complete', 'jigoshop' ); ?></h2>
		<form method="post" action="admin.php?page=jigoshop-convert-to-complete">
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Product ID\'s to use (seperated by commas):', 'jigoshop' ); ?>	
						</th>
						<td>
							<textarea rows="4" cols="50" name="jigoshop_order_ids" type="text" class="regular-text" value="" placeholder="ID's to use" /></textarea>
						</td>
					</tr>
						<tr valign="top">	
							<th scope="row" valign="top">
								<?php _e('Set as Complete', 'jigoshop' ); ?>
							</th>
							<td>
							<?php wp_nonce_field( 'jigoshop_convert_to_complete_run', 'jigoshop_convert_to_complete_run' ); ?>
							<input type="submit" class="button-secondary" name="jigoshop_convert_to_complete_run" value="<?php _e('Run Plugin', 'jigoshop' ); ?>"/>
							</td>
						</tr>
				</tbody>
			</table>	
</form>
</div>
<?php
}
function jigoshop_convert_to_complete_run() {
	// listen for our activate button to be clicked
	if( isset( $_POST['jigoshop_convert_to_complete_run'] ) ) {

		global $wpdb;
		$tags = explode(',', $_POST['jigoshop_order_ids']);
		$errors = array();
		$anyconverted = 0;
		foreach($tags as $key) {
		if ($key != null && $key != 0){
			$save     =      $key;
			$key      =      str_replace(' ', "", $key); //remove spaces from string
			$keycheck =      preg_replace("/[^0-9]/", "", $save);
			if ($key == $keycheck){
				// Check to make sure its an order
				$testmeta = get_post_meta((int) "$key", 'order_items', true);
				if (  !empty ( $testmeta) ){
				$anyconverted++;
				$order = new jigoshop_order((int) "$key");
				wp_set_object_terms($order->id, array( 'completed'), 'shop_order_status', false);
				do_action( 'order_status_completed' , $order->id );
				if ('completed' == 'completedd') {
					update_post_meta( $order->id, '_js_completedd_date', current_time('mysql') );
				}					
				}
				else {
				// save in errors and don't proceed
				echo '<script type="text/javascript">alert("Order: '.$key.' does not exist! Click ok to continue."); </script>';
				array_push($errors,$save);
				}
				
			}
			else{
				// save in errors and don't proceed
				echo '<script type="text/javascript">alert("Bad order ID: '.$key.'! Click ok to continue."); </script>';
				array_push($errors,$save);
			}
		}
		else{
		}
		}
		if ($anyconverted == 0){
			echo '<script type="text/javascript">alert("No Valid Order Numbers Found!"); </script>';
			return;
		}
		else if (empty($errors)){
			echo '<script type="text/javascript">alert("Conversion Done Sucessfully"); </script>';
			return;
		}else{
			echo '<script type="text/javascript">alert("Conversion Done Sucessfully except for the order ID\'s indicated previously"); </script>';
			return;	
		}
	}
}
add_action('admin_init', 'jigoshop_convert_to_complete_run');