<?php 
/**
 * Plugin Name:Word Count Plugin
 * Description:Word count plugin
 * Version:1.0.1
 * Author:Wayne
 * Author URI:http://weblinks.cc
 * Text Domain:wcpdomain
 * Domain Path:/languages
 */

if (!defined('ABSPATH')) exit;

class WordCountPlugin {

	public function __construct() {
		add_action( 'admin_menu', array($this,'adminPage'), 10, 1 );  //$this指向实例本身的class
		add_action( 'admin_init', array($this,'settings'), 10, 1 );

		add_filter( 'the_content', array($this,'ifWrap'), 10, 1 );

		add_action('init',array($this,'languages'));
	}

	function languages() {
		load_plugin_textdomain( 'wcpdomain', false, dirname(plugin_basename( __FILE__ )) . '/languages' );
	}

	function ifWrap($content) {
		if (is_main_query() && is_single() && (get_option( 'wcp_wordcount', '1' ) || get_option( 'wcp_charactercount', '1' ) || get_option( 'wcp_readtime', '1' ))) {
			return $this->createHtml($content);
		}

		return $content;
	}

	function createHtml($content) {
		$html = '<h3>' . esc_html(get_option( 'wcp_headline', 'Post Statics' )) . '</h3><p>';

		//get word count once because both word count and read time will need it
		if (get_option( 'wcp_wordcount', '1' ) || get_option( 'wcp_readtime', '1' )) {
					$wordcount = str_word_count(strip_tags($content));

		}
		if (get_option( 'wcp_wordcount') == '1') {
			$html .= esc_html__('This post has','wcpdomain') . ' ' . $wordcount . ' ' . __('words.','wcpdomain').'<br>'; 
		}
		if (get_option( 'wcp_charactercount') == '1') {
			$html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>'; 
		}
		if (get_option( 'wcp_readtime') == '1') {
			$html .= 'This post will take about ' . round($wordcount/225) . ' minutes to read.<br>'; 
		}

		$html .= '</p>';



		if (get_option( 'wcp_location', '0' ) == '0') {
			return $html . $content;
		}
		return $content . $html;
	}

	function settings() {
		add_settings_section( 'wcp_first_section', null, null, 'word-count-settings-page' );
		add_settings_field( 'wcp_location', 'Display Location', array($this,'locationHTML'), 'word-count-settings-page', 'wcp_first_section' );

		register_setting( 'word-count', 'wcp_location', array( 
			'sanitize_callback' => array($this,'sanitize_location'),
			'default'  => 0,
			 ) );

		add_settings_field( 'wcp_headline', 'Headline Text', array($this,'headlineHtml'), 'word-count-settings-page', 'wcp_first_section' );
		register_setting( 'word-count', 'wcp_headline', array( 

			'sanitize_callback'  => 'sanitize_text_field',
			'default'  => 'POST STATISCS'

			 ) );

		add_settings_field( 'wcp_wordcount', 'Word Count', array($this,'checkboxHtml'),'word-count-settings-page', 'wcp_first_section',array('theName' => 'wcp_wordcount')  );
		register_setting( 'word-count', 'wcp_wordcount',array('sanitize_callback' => 'sanitize_text_field','default'  => '1') );
		add_settings_field( 'wcp_charactercount', 'character Count', array($this,'checkboxHtml'),'word-count-settings-page', 'wcp_first_section',array('theName' => 'wcp_charactercount')  );
		register_setting( 'word-count', 'wcp_charactercount',array('sanitize_callback' => 'sanitize_text_field','default'  => '1') );
		add_settings_field( 'wcp_readtime', 'Readtime', array($this,'checkboxHtml'),'word-count-settings-page', 'wcp_first_section',array('theName' => 'wcp_readtime')  );
		register_setting( 'word-count', 'wcp_readtime',array('sanitize_callback' => 'sanitize_text_field','default'  => '1') );

		//add_settings_field( string $id, string $title, callable $callback, string $page, string $section = 'default', array $args = array() )
		//上面函数最后的array() 里面的数组会传递到回调函数里，可以通过参数传递来实现。

	}

	function sanitize_location($input) { //每次调用sanitize函数的时候都会传递$input参数即用户希望保存的值
		if ($input != '0' && $input != '1') {
			add_settings_error( 'wcp_location', 'wcp_location_code', 'Display location must be begin or end');
					return get_option('wcp_location');

		}


		return $input;
	}

	//reusable checbox function
	function checkboxHtml($args) { ?>
		<input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked( get_option($args['theName']), '1' ); ?>>
		<?php 

	}

	function wordcountHtml() { ?>
		<input type="checkbox" name="wcp_wordcount" value="1" <?php checked( get_option( 
			'wcp_wordcount'), '1' ); ?>>

		<?php 

	}

	function headlineHtml() { ?>
		<input type="text" name="wcp_headline" value="<?php echo esc_attr( get_option('wcp_headline') ); ?>">

		<?php 

	}
	function locationHTML() { ?>
		<select name="wcp_location">
			<option value="0" <?php if (get_option('wcp_location') == '0') echo "selected"  ?>>Begin of the article.</option>
			<option value="1" <?php if (get_option('wcp_location') == '1') echo "selected"  ?>>End of the article.</option>
		</select>
		<?php 
	}

	function adminPage() {
	add_options_page( 'Word Count Settings', __( 'Word Count', 'wcpdomain' ), 'manage_options', 'word-count-settings-page', array($this,'settingsHtml') );
}

function settingsHtml() { ?>

	<div class="wrap">
		<h1>Word Count Settings</h1>
		<form action="options.php" method="POST">	
			<?php 
			settings_fields( 'word-count' );
			do_settings_sections( 'word-count-settings-page' );
			submit_button();

			 ?>
		</form>
	</div>
	<?php 	

}


}
$WordCountplugin = new WordCountPlugin();



