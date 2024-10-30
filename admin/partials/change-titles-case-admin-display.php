<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://properprogramming.com
 * @since      1.0.0
 * @author     Micheal Parisi (Proper Programming, LLC)
 * @copyright  2020
 *
 * @package    c_t_c_Change_Case_Data
 * @subpackage c_t_c_Change_Case_Data/admin/partials
 */
?>

	<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php

/**
 * The form to be loaded on the plugin's admin page
 * todo: update with Admin check.
 */
if(current_user_can('edit_others_pages')) {

	// Generate a custom nonce value.wp_nonce_field
	// Build the Form
	?>

	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<h2><?php _e('Mixed Case Options', $this->plugin_name); ?></h2>
	<?php _e(
		'<p>Offers Exceptions to the Mix Case Conversion.  Enter a word in each field. Comma separated lists are acceptable if bulk adding words.</p>',
		$this->plugin_name
	); ?>
	<?php _e(
		'<p>When using Mixed Cases, Uppercase words will transform into UPPERCASE while lowercase words will transform into lowercase.</p>',
		$this->plugin_name
	); ?>

	<div class='td_change_titles_case__form'>

		<form method='post' id='td_change_titles_case__form'>

			<?php //echo c_t_c_Change_Case_Data_Admin::generate_post_select('post_types');; ?>
			<input type='hidden' name='action' value='td_form_response'>
			<?php wp_nonce_field('save_form', 'td_change_case_data__nonce') ?>
			<div class='container'>
				<div id=change_case_main" class='row'>
					<div class='change_case col-md-6'>
						<h3><?php _e('Words to UPPERCASE', $this->plugin_name); ?></h3>

						<div id='change_case_uppercases'>
							<div class='change_case_grid'>
								<label for='change_case_uppercases_0'><input type='text' id='change_case_uppercases_0' class='change_case change_case_uppercase' size='20' name='change_case_uppercases_0' value='' placeholder='Input Value'/></label>
								<a href='#' id='remove_btn_change_case_uppercase_1' class='remove_change_case_uppercases'>Remove</a>
							</div>
						</div>
						<p><a href='#' id='add_change_case_uppercases'><?php _e(
									'Add Exception',
									$this->plugin_name
								); ?></a></p>
					</div>

					<div class='change_case col-md-6'>
						<h3><?php _e('Words to lowercase', $this->plugin_name); ?></h3>
						<div id='change_case_lowercases'>
							<div class='change_case_grid'>
								<label for='change_case_lowercases_0'><input type='text' id='change_case_lowercases_0' class='change_case change_case_lowercase' size='20' name='change_case_lowercases_0' value='' placeholder='Input Value'/></label>
								<a href='#' id='remove_btn_change_case_lowercase_1' class='remove_change_case_lowercases'>Remove</a>
							</div>
						</div>
						<p><a href='#' id='add_change_case_lowercases'><?php _e(
									'Add Exception',
									$this->plugin_name
								); ?></a></p>
					</div>
				</div>
			</div>
			<p class='submit'>
				<input type='submit' name='submit' id='submit' class='button button_primary' value='Submit'>
			</p>
		</form>
	</div>
	<?php
} else {
	?>
	<p> <?php __('You are not authorized to perform this operation.', $this->plugin_name) ?> </p>
	<?php
}