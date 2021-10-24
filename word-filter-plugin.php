<?php 
/**
 * Plugin Name:Word Filter Plugin
 * Description:Replace a list of words
 * Version:1.0
 * Author:Wayne
 * Author URI:https://udemy.com
 */
if(!defined('ABSPATH')) exit;

class WordFilterPlugin {
	function __construct() {
		add_action('admin_menu',array($this,'ourAdminMenu'));
		add_action('admin_init',array($this,'ourSettings'));
		if(get_option('plugin_words_to_filter')) add_filter('the_content',array($this,'filterLogic'));
	}

	function ourSettings() {
		add_settings_section( 'replacement-text-section', '', '', 'word-filter-options' );
		register_setting( 'replacementFields', 'replacementText' );
		add_settings_field( 'replacementText', 'Filtered Text', array($this,'replacementFieldHtml'), 'word-filter-options', 'replacement-text-section' );
	}

	function replacementFieldHtml() { ?>

		<input type="text" name="replacementText" value="<?php echo esc_attr(get_option('replacementText','***')) ?>">
		<p class="description">Leave blank to simply remove the filtered words</p>
	<?php }

	function filterLogic($content) {
		$badwords = explode(',', get_option('plugin_words_to_filter'));
		$badWordsTrimmed = array_map('trim',$badwords);
		return str_ireplace($badWordsTrimmed, esc_html(get_option('replacementText','****')), $content);
	}


	function ourAdminMenu() {
		//add_menu_page( 'Words to filter', 'Word Filter', 'manage_options', 'word_filter', array($this,'wordFilterPage'),plugin_dir_url( __FILE__ ) . 'custom.svg',100);
		$filterMainPage = add_menu_page( 'Words to filter', 'Word Filter', 'manage_options', 'word_filter', array($this,'wordFilterPage'), 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0wIDIzdi0xMC42NjhjMC0uOTkuMDgyLTEuOTUyIDEuMzI0LTIuMjIzIDEuNDMzLS4zMTIgMi43NjgtLjU4NiAyLjEyMS0xLjczNi0xLjk2Ni0zLjUwMS0uNTIxLTUuMzczIDEuNTU1LTUuMzczIDIuMTE3IDAgMy41MjcgMS45NDQgMS41NTYgNS4zNzMtLjMxMS41NDEtLjE3NS44ODguMjAxIDEuMTM2LjIyNS0uMjA4LjUyOC0uMzY3LjkzNC0uNDYxIDEuNjg0LS4zODkgMy4zNDQtLjczNiAyLjU0NS0yLjIwOS0yLjM2Ni00LjM2NC0uNjc0LTYuODM5IDEuODY2LTYuODM5IDIuNDkxIDAgNC4yMjYgMi4zODMgMS44NjYgNi44MzktLjc3NSAxLjQ2NC44MjYgMS44MTIgMi41NDUgMi4yMDkuMzUyLjA4MS42MjcuMjEyLjg0LjM4LjI5LS4yNDIuMzY5LS41NzEuMDkxLTEuMDU1LTEuOTcxLTMuNDI5LS41NjEtNS4zNzMgMS41NTYtNS4zNzMgMi4wNzYgMCAzLjUyMSAxLjg3MiAxLjU1NSA1LjM3My0uNjQ3IDEuMTUuNjg4IDEuNDI0IDIuMTIxIDEuNzM2IDEuMjQyLjI3MSAxLjMyNCAxLjIzMyAxLjMyNCAyLjIyM3YxMC42NjhjMCAuNTUyLS40NDggMS0xIDFoLTIyYy0uNTUyIDAtMS0uNDQ4LTEtMXptMjMtOGgtMjJ2OGgyMnYtOHptLTE4LjE5MyAxbC0uOTc2IDIuMDE0LTIuMjE3LjMwNSAxLjYxNSAxLjU1Mi0uMzk1IDIuMjA0IDEuOTczLTEuMDU3IDEuOTczIDEuMDU2LS4zOTMtMi4yMDMgMS42MTMtMS41NTItMi4yMTctLjMwNS0uOTc2LTIuMDE0em03LjE5MyAwbC0uOTc2IDIuMDE0LTIuMjE3LjMwNSAxLjYxNSAxLjU1Mi0uMzk1IDIuMjA0IDEuOTczLTEuMDU3IDEuOTczIDEuMDU2LS4zOTMtMi4yMDMgMS42MTMtMS41NTItMi4yMTctLjMwNS0uOTc2LTIuMDE0em03LjE5MyAwbC0uOTc2IDIuMDE0LTIuMjE3LjMwNSAxLjYxNSAxLjU1Mi0uMzk1IDIuMjA0IDEuOTczLTEuMDU3IDEuOTczIDEuMDU2LS4zOTMtMi4yMDMgMS42MTMtMS41NTItMi4yMTctLjMwNS0uOTc2LTIuMDE0em0tMTQuODg2IDMuNTJsLS41MTItLjQ5MS43MDItLjA5Ny4zMS0uNjM5LjMxLjYzOS43MDMuMDk3LS41MTEuNDkxLjEyNS42OTktLjYyNy0uMzM1LS42MjUuMzM0LjEyNS0uNjk4em03LjE5MyAwbC0uNTEyLS40OTEuNzAyLS4wOTcuMzEtLjYzOS4zMS42MzkuNzAzLjA5Ny0uNTExLjQ5MS4xMjUuNjk5LS42MjctLjMzNS0uNjI1LjMzNC4xMjUtLjY5OHptNy4xOTMgMGwtLjUxMi0uNDkxLjcwMi0uMDk3LjMxLS42MzkuMzEuNjM5LjcwMy4wOTctLjUxMS40OTEuMTI1LjY5OS0uNjI3LS4zMzUtLjYyNS4zMzQuMTI1LS42OTh6bS0xMi41ODktNS41MmwtLjAwMS0yLjEyNmMwLS41MjYuMDE4LTEuMDQ2LjE0Ny0xLjQ5OC0uMzE5LS4xOTktLjU3My0uNDU4LS43MjktLjgxMS0uMTYtLjM2LS4yNjEtLjk0NS4xNjgtMS42OS43OTItMS4zNzkgMS4wMTktMi41NjQuNjIyLTMuMjUxLS40ODctLjg0LTIuMTMtLjgzMS0yLjYwOC0uMDE0LS4zOTcuNjc5LS4xNzQgMS44NzEuNjE0IDMuMjczLjQxOS43NDcuMzE2IDEuMzMuMTU2IDEuNjg4LS4yMjkuNTA5LS41MzUuOTg3LTIuOTM2IDEuNTE2LS4zNjkuMDgtLjUzNy4xMTYtLjUzNyAxLjI0NWwuMDAxIDEuNjY4aDUuMTAzem0xMC45OTggMHYtMi4xMjJjMC0xLjQzOC0uMTkzLTEuNzEzLS44MTMtMS44NTYtMi43NDYtLjYzMy0zLjA5OC0xLjE3Mi0zLjM1OS0xLjc0NC0uMTgxLS4zOTUtLjMwMS0xLjA0OC4xNTQtMS45MDcgMS4wMjItMS45MjkgMS4yNzgtMy41ODIuNzAzLTQuNTM4LS42NzItMS4xMTUtMi43MDQtMS4xMjUtMy4zODQuMDE3LS41NzcuOTY5LS4zMTggMi42MTMuNzEyIDQuNTEyLjQ2NS44NTcuMzQ4IDEuNTEuMTY5IDEuOTA5LS40OTEgMS4wODgtMS44MzggMS4zOTktMy4yNjUgMS43MjctLjgyOS4xOTYtLjkxNi41ODctLjkxNiAxLjg3NmwuMDAxIDIuMTI2aDkuOTk4em01Ljg5NyAwbC4wMDEtMS42NjhjMC0xLjEyOS0uMTY4LTEuMTY1LS41MzctMS4yNDUtMi40MDEtLjUyOS0yLjcwNy0xLjAwNy0yLjkzNi0xLjUxNi0uMTYtLjM1OC0uMjYzLS45NDEuMTU2LTEuNjg4Ljc4OC0xLjQwMiAxLjAxMS0yLjU5NC42MTQtMy4yNzMtLjQ3OC0uODE3LTIuMTIxLS44MjYtMi42MDguMDE0LS4zOTcuNjg3LS4xNyAxLjg3Mi42MjIgMy4yNTEuNDI5Ljc0NS4zMjggMS4zMy4xNjggMS42OS0uMTI3LjI4OC0uMzIuNTEzLS41NTkuNjk1LjE2Mi40NzguMTgyIDEuMDQ0LjE4MiAxLjYxOHYyLjEyMmg0Ljg5N3oiLz48L3N2Zz4=',100);
		add_submenu_page( 'word_filter', 'General', 'General', 'manage_options', 'word_filter', array($this,'wordFilterPage'));
		add_submenu_page( 'word_filter', 'Filter Options', 'Options', 'manage_options', 'word-filter-options', array($this,'optionsSubPage'));
		add_action( "load-{$filterMainPage}", array($this,'mainPageAsstes'), 10, 1 );
	}

	function mainPageAsstes() {
		wp_enqueue_style('filteradmincss',plugin_dir_url( __FILE__ ) . '/css/style.css');
	}

	function optionsSubPage() { ?>

		<div class="wrap">
			<h1>Word Filter Option</h1>
			<form action="options.php" method="POST">
				
				<?php 
				settings_errors();
				settings_fields( 'replacementFields' ); 
				 do_settings_sections( 'word-filter-options' ); 
				  submit_button(); ?>
			</form>
		</div>

		<?php 
		
	}

	function wordFilterPage() {?>

		<div class="wrap">
			<h1>Word Filter</h1>
			<?php if(isset($_POST['justsubmitted']) && $_POST['justsubmitted'] == 'true') $this->handleForm(); ?>
			<form method="POST">
				<input type="hidden" name="justsubmitted" value="true">
				<?php wp_nonce_field('saveFilterWords','ourNonce'); ?>
				<label for="plugin_words_to_filter">
					<p>Enter a comma seperated list of words to filter from your sites content.</p>
				</label>
				<div class="word-filter__flex_container">
					<textarea name="plugin_words_to_filter" id="plugin_words_to_filter" placeholder="bad,me,horrable"><?php echo esc_textarea(get_option( 'plugin_words_to_filter')); ?></textarea>
				</div>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
			</form>
		</div>

		<?php 
	}

	function handleForm() {
	if (wp_verify_nonce( 'ourNonce', 'saveFilterWords' ) && current_user_can( 'manage_options' )) { 
		update_option( 'plugin_words_to_filter', sanitize_text_field( $_POST['plugin_words_to_filter'] )); ?>
		<div class="updated">
			<p>You filtered words were saved.</p>
		</div>

		<?php 
		 
	} else { ?>
		<div class="error">
			<p>Sorry you don't have the permission.</p>
		</div>
		<?php 

	}} }

$wordFilterPlugin = new WordFilterPlugin();
