<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://properprogramming.com
 * @since      1.0.0
 * @author     Micheal Parisi (Proper Programming, LLC)
 * @copyright  2020
 *
 * @package    c_t_c_Change_Case_Data
 * @subpackage c_t_c_Change_Case_Data/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * https://pippinsplugins.com/batch-processing-for-big-data/
 *
 * @package    c_t_c_Change_Case_Data
 * @subpackage c_t_c_Change_Case_Data/admin
 * @author     Michael Parisi <mgparisicpu@gmail.comm>
 */
class c_t_c_Change_Case_Data_Admin {
	const DEBUG = FALSE;
	const OPTION_KEY = 'tc_change_case_defaults';
	public $term = TRUE;
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 * @access     private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;
	/**
	 * The text domain of this plugin for translations
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 * @access     private
	 * @var      string $plugin_text_domain The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 */
	public function __construct($plugin_name, $version, $plugin_text_domain) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in c_t_c_Change_Case_Data_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The c_t_c_Change_Case_Data_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url(__FILE__) . 'css/change-titles-case-admin.css',
			array(),
			$this->version,
			'all'
		);

	}

	// Removes from the beginning of a string

	public function html_form_page_content() {

		//show the form
		include_once('partials/change-titles-case-admin-display.php');
	}

	/**
	 * Action for saving the form
	 */
	public function save_form() {
		$is_admin = $this->enforce_admin_permissions();
		//Not the right action, don't do anything
		if(!isset($_POST['action']) || $_POST['action'] !== 'td_form_response') {
			return FALSE;
		}

		if(self::DEBUG) {
			error_log("Saving Form!");
		}

		if(!$is_admin) {
			if(self::DEBUG) {
				error_log("Not Admin!");
			}
			return FALSE;
		}

		/**
		 * Check the nonce.
		 */
		if(!check_admin_referer('save_form', 'td_change_case_data__nonce')) {
			//todo: add error message.
			exit('No access!');
		} else {
			if(self::DEBUG) {
				error_log('Nonce passes for Change Titles Case Update');
			}
		}
		self::save_option(
			self::convert_post_to_string('change_case_uppercases'),
			self::convert_post_to_string('change_case_lowercases')
		);

		return TRUE;
	}

	/**
	 * @return bool|\WP_Error
	 */
	public function enforce_admin_permissions() {
		if(!current_user_can('manage_options') || !current_user_can('administrator')) {
			return new WP_Error('rest_forbidden', esc_html__('Private', 'myplugin'), array('status' => 401));
		}
		return TRUE;
	}

	/**
	 * @param        $type
	 * @param string $string
	 */
	public static function save_option($uppercases, $lowercases) {
		$array = array('uppercases' => strtolower($uppercases), 'lowercases' => strtolower($lowercases));
		if(get_option(C_T_C_TC_OPTION_KEY)) {
			//error_log(print_r($array, true));
			update_option(C_T_C_TC_OPTION_KEY, $array);
		} else {
			add_option(C_T_C_TC_OPTION_KEY, $array);
		}
	}

	/*
	 * Callback for the add_submenu_page action hook
	 *
	 * The plugin's HTML form is loaded from here
	 *
	 * @since      1.0.0
	 * @author 	Micheal Parisi (Proper Programming, LLC)
	 * @copyright 2020
	 */

	/**
	 * @param $type
	 * return's array of User Data.
	 */
	public static function convert_post_to_string($type) {
		/**
		 * Join the $_POST results in a csv.
		 *
		 * @var $string
		 */
		$string = NULL;
		$count = 0;
		$dupeArr = array();

		//error_log(print_r($_POST, true));
		/*
		 * Put all of the values in the array.  Lowercasing and trimming them all.  Dropping all empty ones.
		 * Removing all duplicates.
		 */
		foreach($_POST as $key => $value) {

			//error_log(print_r(strpos($key, $type) ), true);
			$position = strpos($key, $type);

			if($position === 0 && isset($value)) {
				$value = trim($value);
				if(isset($value)) {
					$value = strtolower($value);
					$value = str_replace("\\'", "'", trim($value));
					$value = str_replace('\"', '"', trim($value));
					if(!in_array($value, $dupeArr)) {
						$dupeArr[] = str_replace('u00a0', '', $value);;
					}
				}
			}
		}
		//Sorting the filtered list.
		sort($dupeArr);
		//Imploding it into a list of commas.
		$string = implode(',', $dupeArr);

		return $string;
	}

	/**
	 * Register the init_routes (plural)
	 */
	public function init_routes() {
		//Route for the load CSV options./wp-json/change-titles-case/v1/ini'
		register_rest_route(
			'change-titles-case/v1',
			'/init/',
			[
				'methods'  => ['POST'],
				'callback' => [$this, 'get_CSV_Post_Request_Handler'],
			]
		);
	}

	/**
	 * Todo: Needs Nonce check.
	 */
	public function get_CSV_Post_Request_Handler() {

		if($this->enforce_admin_permissions()) {
			//put change_case_uppercases and change_case_losercases
			$options = get_option(self::OPTION_KEY);
			if(is_array($options)) {
				return json_encode(
					array(
						'change_case_uppercases' => $options['uppercases'],
						'change_case_lowercases' => $options['lowercases'],
					)
				);
			}
		}
		return new WP_Error('no_permission', 'Invalid permissions', array('status' => 401));
		//get_option('change_case_case_'.$type);

	}

	/**
	 * Register the Bulk Actions
	 * Note: This is done here so that it can be triggered when wordpress is done loading all plugins and themes.
	 *
	 * @param \c_t_c_Change_Case_Data_Admin $plugin_admin
	 */
	public function register_bulk_actions() {

		$types = get_post_types([], 'objects');
		foreach($types as $type) {
			if(isset($type->name)) {
				// you'll probably want to do something else.
				add_filter(
					'bulk_actions-edit-' . $type->name,
					array(
						$this,
						'register_my_bulk_actions_posts',
					)
				);
				add_filter(
					'handle_bulk_actions-edit-' . $type->name,
					array(
						$this,
						'change_case_bulk_action_handler',
					),
					10,
					3
				);
			}
		}

		$types = get_taxonomies([], 'objects');
		//print_r($types);
		foreach($types as $type) {
			if(isset($type->name)) {
				// you'll probably want to do something else.
				add_filter(
					'bulk_actions-edit-' . $type->name,
					array(
						$this,
						'register_my_bulk_actions_categories',
					)
				);
				add_filter(
					'handle_bulk_actions-edit-' . $type->name,
					array(
						$this,
						'change_case_bulk_action_handler',
					),
					10,
					3
				);
			}
		}
	}

	public function loaded_html_form_submenu_page() {
		// called when the particular page is loaded.
	}

	public function loaded_ajax_form_submenu_page() {
		// called when the particular page is loaded.
	}

	/**
	 * Adds Change to Cases on Admin Posts Bulk Edit window.
	 *
	 * @param $bulk_actions
	 * @return mixed
	 */
	public function register_my_bulk_actions_posts($bulk_actions) {
		$bulk_actions['change_case_posts_to_upper'] = __('Change to Uppercase', 'change_case_posts_to_upper');
		$bulk_actions['change_case_posts_to_lower'] = __('Change to Lowercase', 'change_case_posts_to_lower');
		$bulk_actions['change_case_posts_to_mixed'] = __('Change to Mixedcase', 'change_case_posts_to_mixed');
		//$bulk_actions['email_to_eric'] = __( 'Change to Upper', 'email_to_eric');
		return $bulk_actions;
	}

	/*
	 * Callback for the load-($html_form_page_hook)
	 * Called when the plugin's submenu HTML form page is loaded
	 *
	 * @since      1.0.0
	 * @author 	Micheal Parisi (Proper Programming, LLC)
	 * @copyright 2020
	*/

	/**
	 * Adds Change to Cases on Admin Posts Bulk Edit window.
	 *
	 * @param $bulk_actions
	 * @return mixed
	 * @copyright  2020
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 */
	public function register_my_bulk_actions_categories($bulk_actions) {
		$bulk_actions['change_case_categories_to_upper'] = __('Change to Uppercase', 'change_case_categories_to_upper');
		$bulk_actions['change_case_categories_to_lower'] = __('Change to Lowercase', 'change_case_categories_to_lower');
		$bulk_actions['change_case_categories_to_mixed'] = __('Change to Mixedcase', 'change_case_categories_to_mixed');
		//$bulk_actions['email_to_eric'] = __( 'Change to Upper', 'email_to_eric');
		return $bulk_actions;
	}

	/*
	 * Callback for the load-($ajax_form_page_hook)
	 * Called when the plugin's submenu Ajax form page is loaded
	 *
	 * @since      1.0.0
	 * @author 	Micheal Parisi (Proper Programming, LLC)
	 * @copyright 2020
	 */

	/**
	 * Performs the transformation
	 *
	 * @param        $redirect_to
	 * @param string $doaction
	 * @param array  $post_ids
	 * @return mixed
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 * @since      1.0.0
	 */
	public function change_case_bulk_action_handler($redirect_to, $doaction, $post_ids) {

		//error_log(print_r($post_ids, true));
		//error_log(print_r($_POST, true));
		//error_log(print_r($_GET, true));

		if(
			$doaction !== 'change_case_posts_to_upper' && $doaction !== 'change_case_posts_to_lower' &&
			$doaction !== 'change_case_posts_to_mixed' && $doaction !== 'change_case_posts_to_capitalized' &&
			$doaction !== 'change_case_categories_to_upper' && $doaction !== 'change_case_categories_to_lower' &&
			$doaction !== 'change_case_categories_to_capitalized' && $doaction !== 'change_case_categories_to_mixed') {

			if(self::DEBUG) {
				error_log("Wasn't a proper action.");
			}
			return $redirect_to;
		}

		if(self::DEBUG) {
			error_log("Action $doaction performed by change-titles-case.");
		}
		$count = 0;
		switch($doaction) {
			case 'change_case_posts_to_upper':
				$count = $this->change_case_case('posts', $post_ids, 'upper');
				break;
			case 'change_case_posts_to_lower':
				$count = $this->change_case_case('posts', $post_ids, 'lower');
				break;
			case 'change_case_posts_to_capitalized':
				$count = $this->change_case_case('posts', $post_ids, 'capitalized');
				break;
			case 'change_case_posts_to_mixed';
				$count = $this->change_case_case('posts', $post_ids, 'mixed');
				break;
			case 'change_case_categories_to_upper':
				$count = $this->change_case_case('categories', $post_ids, 'upper');
				break;
			case 'change_case_categories_to_lower':
				$count = $this->change_case_case('categories', $post_ids, 'lower');
				break;
			case 'change_case_categories_to_capitalized':
				$count = $this->change_case_case('categories', $post_ids, 'capitalized');
				break;
			case 'change_case_categories_to_mixed';
				$count = $this->change_case_case('categories', $post_ids, 'mixed');
				break;
		}

		//$redirect_to = add_query_arg('bulk_emailed_posts', count($post_ids), $redirect_to);
		$redirect_to = add_query_arg('bulk_change_case_response', $count, $redirect_to);

		return $redirect_to;
	}

	/**
	 * Selects which transfer case function to call for POST, CATEGORY, ETC.
	 *
	 * @param string $type             Posts or Otherwise Categories
	 * @param array  $ids              ID's selected
	 * @param string $change_case_type Type of transformation,  'upper', 'lower', and other ('mixed')
	 * @return int
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 * @since      1.0.0
	 */
	public function change_case_case($type, $ids, $change_case_type = 'mixed') {
		if(empty($ids)) {
			exit('No Posts Submitted');
		}
		if($type == 'posts') {
			return $this->change_case_case_post($ids, $change_case_type);
		} else {
			return $this->change_case_case_category($ids, $change_case_type);
		}
	}

	public function change_case_case_post($ids, $change_case_type = 'mixed') {
		global $post;


		$count = 0;
		foreach($ids as $id) {
			$theTitle = get_the_title($id);
			$new_title = self::get_new_title($change_case_type, $theTitle);

			if($theTitle !== FALSE && $theTitle !== $new_title) {

				$count++;

				wp_update_post(
					array(
						'ID'         => $id,
						'post_title' => $new_title // see function below
					)
				);
			}
		}
		return $count;
	}

	/**
	 * @param        $change_case_type
	 * @param string $theTitle
	 * @return string
	 */
	private static function get_new_title($change_case_type, string $theTitle): string {
		if($change_case_type == 'upper') {
			$new_title = strtoupper($theTitle);
		} elseif($change_case_type == 'lower') {
			$new_title = strtolower($theTitle);
		} else {
			$new_title = self::to_title_case($theTitle);
		}
		return $new_title;
	}

	/**
	 * Converts case to title case.
	 *
	 * @param $string
	 * @return string
	 */
	private static function to_title_case($string) {
		$option = get_option(C_T_C_TC_OPTION_KEY);
		/* Words that should be entirely lower-case */
		$uppercases = explode(',', mb_strtolower(html_entity_decode($option['uppercases'])));
		/* Words that should be entirely upper-case (need to be lower-case in this list!) */
		$lowercases = explode(',', mb_strtolower(html_entity_decode($option['lowercases'])));
		/* split title string into array of words */
		$words = explode(' ', mb_strtolower($string));

		$no_punc_upper = self::remove_punctuation_from_array($uppercases);
		$no_punc_lower = self::remove_punctuation_from_array($lowercases);
		$no_punc_words = self::remove_punctuation_from_array($words);

		//error_log(implode(',', $no_punc_upper));
		//error_log(implode(',', $no_punc_lower));
		/* iterate over words */

		foreach($words as $position => $word) {
			/* re-capitalize lowercases */
			$no_punc_word = $no_punc_words[$position];
			error_log($no_punc_word . '///' . print_r($no_punc_upper, TRUE) . '///' . print_r($no_punc_lower, TRUE));
			if(in_array($no_punc_word, $no_punc_upper)) {
				$words[$position] = mb_strtoupper($word);
				/* capitalize first letter of all other words, if... */
			} elseif(
			(0 === $position || !in_array($no_punc_word, $no_punc_lower))) {
				$words[$position] = self::mb_ucfirst($word, 'UTF-8');
				/* ...first word of the title string and not in lowercases... */ /* ...or not in above lower-case list*/ //Remove the single and double, caps the first then re add... For Caps Quotes.
				$words = self::caps_first("'", $words, $position);
				$words = self::caps_first("“", $words, $position);
				$words = self::caps_first('"', $words, $position);
				$words = self::caps_first("&#34;", $words, $position);
				$words = self::caps_first("&#39;", $words, $position);
				$words = self::caps_first("&apos;", $words, $position);
				$words = self::caps_first("&#8217;", $words, $position);

			} else {
				$words[$position] = mb_strtolower($word);
			}
		}
		/* re-combine word array */
		$string = implode(' ', $words);
		/* return title string in title case */
		return $string;
	}

	static private function remove_punctuation_from_array($array) {
		$no_punc = array();
		foreach($array as $key => $word) {
			/* Remove Punctuation from the word */

			$decoded = html_entity_decode($word);
			$stripped = self::stripQuotes($decoded);
			$no_period = rtrim($stripped, '.');
			$no_ex = rtrim($no_period, '!');
			$no_quest = rtrim($no_ex, '?');
			$encoded = htmlentities($no_quest);
			$no_punc[$key] = trim($encoded);

		}
		return $no_punc;
	}

	/**
	 * Remove the first and last quote from a quoted string of text
	 *
	 * @param mixed $text
	 */
	private static function stripQuotes($text) {
		$text = str_replace('"', "", $text);
		$text = str_replace("'", "", $text);
		$text = str_replace("’", "", $text);
		return $text;
	}

	static private function mb_ucfirst($string, $encoding) {
		$string = mb_strtolower($string);
		$strlen = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, $strlen - 1, $encoding);
		return mb_strtoupper($firstChar, $encoding) . $then;
	}

	/**
	 *
	 * @param        $word
	 * @param string $ignore
	 * @param int    $single_quote_pos
	 * @param array  $words
	 * @param        $position
	 * @return array
	 */
	private static function caps_first(string $ignore, array $words, $position): array {
		$char_pos = strrpos($words[$position], $ignore);

		if($char_pos === 0) {
			$ltrim = self::lStringTrim($words[$position], $ignore);
			$ucfirst = ucfirst($ltrim);
			error_log("ltrim: $ltrim UCFirst: $ucfirst");
			$words[$position] = $ignore . $ucfirst;
		}
		return $words;
	}

	private static function lStringTrim($string, $trim) {

		if(mb_substr($string, 0, mb_strlen($trim)) == $trim) {

			$string = mb_substr($string, mb_strlen($trim));

		}

		return $string;

	}

	/**
	 *
	 * Transforms case for categories.
	 *
	 * @param array  $ids              ID's selected
	 * @param string $change_case_type Type of transformation,  'upper', 'lower', and other ('mixed')
	 * @return int
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 */
	public function change_case_case_category($ids, $change_case_type = 'mixed') {

		if(empty($ids)) {

			if(self::DEBUG) {
				error_log("No Terms Submitted");
			}
			exit('No Terms Submitted');
		} elseif(!isset($_POST['taxonomy'])) {

			if(self::DEBUG) {
				error_log("No Taxonomy Included in post data");
			}
			exit('No Taxonomy Included in post data');
		}
		if(self::DEBUG) {
			error_log(
				"Category ID's:" . implode(',', $ids) . " change_case_type:" . $change_case_type
			);
		}

		$return = NULL;
		$count = 0;
		foreach($ids as $id) {
			$theTitle = get_term_by('id', $id, $_POST['taxonomy'])->name;
			$new_title = self::get_new_title($change_case_type, $theTitle);

			if($theTitle !== $new_title) {
				$count++;
				$output = $id . '/' . esc_html($_POST['taxonomy']) . '==' . $theTitle . ' --> ' . $new_title;

				if(self::DEBUG) {
					error_log($output);
				}
				//echo $output;
				$return .= '<div>' . $output . '</div>';

				wp_update_term(
					$id,
					$_POST['taxonomy'],
					array('name' => $new_title)
				);

			}
		}
		return $count;
	}

	/**
	 *
	 */
	public function change_case_bulk_action_admin_notice() {
		if(!empty($_REQUEST['bulk_change_case_response'])) {
			$emailed_count = intval($_REQUEST['bulk_change_case_response']);
			printf(
				'<div id="message" class="updated fade">' . _n(
					'Changed cases on a single post.',
					'Changed cases on %s posts.',
					$emailed_count,
					$this->plugin_name
				) . '</div>',
				$emailed_count
			);
		}
	}

	/**
	 * Print Admin Notices
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 */
	public function print_plugin_admin_notices() {
		if(isset($_REQUEST['td_admin_add_notice'])) {
			if($_REQUEST['td_admin_add_notice'] === "success") {
				$html = '<div class="notice notice-success is-dismissible"> 
							<p><strong>The request was successful. </strong></p><br>';
				$html .= '<pre>' . htmlspecialchars(print_r($_REQUEST['td_response'], TRUE)) . '</pre></div>';
				echo $html;
			}

			// handle other types of form notices

		} else {
			return;
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in c_t_c_Change_Case_Data_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The c_t_c_Change_Case_Data_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url(__FILE__) . 'js/change-titles-case-admin.js',
			array('jquery'),
			$this->version,
			TRUE
		);

		wp_localize_script(
			$this->plugin_name,
			'change_titles_case',
			array(
				'api_nonce' => wp_create_nonce('wp_rest'),
				'api_url'   => site_url('/wp-json/change-titles-case/v1/init'),
			)
		);
	}

	/**
	 * Callback for the admin menu
	 *
	 * @since    1.0.0
	 * @hooked \c_t_c_Change_Case_Data\define_admin_hooks();
	 */
	public function add_plugin_admin_menu() {
		$html_form_page_hook = add_submenu_page(
			'options-general.php',
			__('Title Case', $this->plugin_text_domain), //page title
			__('Title Case', $this->plugin_text_domain), //menu title
			'edit_others_pages', //capability
			$this->plugin_name, //menu_slug
			array($this, 'html_form_page_content') //callback for page content
		);;

		/*
		 * The $page_hook_suffix can be combined with the load-($page_hook) action hook
		 * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page)
		 *
		 * The callback below will be called when the respective page is loaded
		 */
		add_action('load-' . $html_form_page_hook, array($this, 'loaded_html_form_submenu_page'));
		//add_action('load-' . $ajax_form_page_hook, array($this, 'loaded_ajax_form_submenu_page'));
	}
}
