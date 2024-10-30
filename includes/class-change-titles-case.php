<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://properprogramming.com
 * @since      1.0.0
 * @author     Micheal Parisi (Proper Programming, LLC)
 * @copyright  2020
 *
 * @package    c_t_c_Change_Case_Data
 * @subpackage c_t_c_Change_Case_Data/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @author     Micheal Parisi (Proper Programming, LLC) <mgparisicpu@gmail.comm>
 * @copyright  2020
 * @package    c_t_c_Change_Case_Data
 * @subpackage c_t_c_Change_Case_Data/includes
 */
class c_t_c_Change_Case_Data {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 * @access     protected
	 * @var      c_t_c_Change_Case_Data_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 * @access     protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 * @access     protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_text_domain The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 */
	public function __construct() {
		$this->version = CHANGE_CASE_DATA_VERSION;

		$this->plugin_name = 'change-titles-case';
		$this->plugin_text_domain = 'change-titles-case';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - c_t_c_Change_Case_Data_Loader. Orchestrates the hooks of the plugin.
	 * - c_t_c_Change_Case_Data_i18n. Defines internationalization functionality.
	 * - c_t_c_Change_Case_Data_Admin. Defines all hooks for the admin area.
	 * - c_t_c_Change_Case_Data_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since      1.0.0
	 * @author     Micheal Parisi (Proper Programming, LLC)
	 * @copyright  2020
	 * @access     private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-change-titles-case-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-change-titles-case-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-change-titles-case-admin.php';

		$this->loader = new c_t_c_Change_Case_Data_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the c_t_c_Change_Case_Data_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new c_t_c_Change_Case_Data_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new c_t_c_Change_Case_Data_Admin(
			$this->get_plugin_name(), $this->get_version(), $this->get_text_domain()
		);

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		//Add a tools menu for our plugin
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

		//when a form is submitted to admin-post.php
		//$this->loader->add_action( 'admin_post_td_form_response', $plugin_admin, 'the_form_response');

		//when a form is submitted to admin-ajax.php
		//$this->loader->add_action( 'wp_ajax_td_form_response', $plugin_admin, 'the_form_response');

		// Register admin notices
		//$this->loader->add_action( 'admin_notices', $plugin_admin, 'print_plugin_admin_notices');
		//$this->loader->add_action('wp_ajax_td_set_title_case', $plugin_admin, 'change_titles_case');

		$this->loader->add_action('wp_loaded', $plugin_admin, 'register_bulk_actions');
		//add_action( 'wp_ajax_td_get_fields', 'td_post_type_field_select_action_callback' );
		//$this->loader->add_filter('bulk_actions-edit-page', $plugin_admin, 'register_my_bulk_actions' );
		//$this->loader->add_filter('bulk_actions-edit-post', $plugin_admin, 'register_my_bulk_actions' );
		//$this->loader->add_filter( 'handle_bulk_actions-edit-page', $plugin_admin, 'c_t_c_change_case_bulk_action_handler', 10, 3 );
		//$this->loader->add_filter( 'handle_bulk_actions-edit-post', $plugin_admin, 'c_t_c_change_case_bulk_action_handler', 10, 3 );
		$this->loader->add_action('admin_notices', $plugin_admin, 'change_case_bulk_action_admin_notice');
		$this->loader->add_action('wp_loaded', $plugin_admin, 'save_form');
		$this->loader->add_action('rest_api_init', $plugin_admin, 'init_routes');

		//$this->loader->add_action( 'added_option', $plugin_admin, 'save_form' , 10, 2);
		//$this->loader->add_action( 'updated_option', $plugin_admin, 'save_form' , 10, 3);
		//add_action('added_option', 'callback_function');
		//add_action('updated_option', 'callback_function', 10, 3);
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_text_domain() {
		return $this->plugin_text_domain;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    c_t_c_Change_Case_Data_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new c_t_c_Change_Case_Data_Public(
			$this->get_plugin_name(), $this->get_version(), $this->get_text_domain()
		);

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

	}


}
