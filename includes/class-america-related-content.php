<?php

/**
	* The file that defines the core plugin class
	*
	* A class definition that includes attributes and functions used across both the
	* public-facing side of the site and the admin area.
	*
	* @link       https://github.com/IIP-Design
	* @since      1.0.0
	*
	* @package    America_Related_Content
	* @subpackage America_Related_Content/includes
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
	* @package    America_Related_Content
	* @subpackage America_Related_Content/includes
	* @author     Office of Design, U.S. Department of State <kleekampnf@america.gov>
	*/

class America_Related_Content {

	/**
		* The loader that's responsible for maintaining and registering all hooks that power
		* the plugin.
		*
		* @since    1.0.0
		* @access   protected
		* @var      America_Related_Content_Loader    $loader    Maintains and registers all hooks for the plugin.
		*/

	protected $loader;


	/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
		 */

	protected $plugin_name;


	/**
		* The current version of the plugin.
		*
		* @since    1.0.0
		* @access   protected
		* @var      string    $version    The current version of the plugin.
		*/

	protected $version;


	/**
		* Define the core functionality of the plugin.
		*
		* Set the plugin name and the plugin version that can be used throughout the plugin.
		* Load the dependencies, define the locale, and set the hooks for the admin area and
		* the public-facing side of the site.
		*
		* @since    1.0.0
		*/

	public function __construct() {
		$this->plugin_name = 'america-related-content';
		$this->version = '1.0.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}


	/**
		* Load the required dependencies for this plugin.
		*
		* Include the following files that make up the plugin:
		*
		* - America_Related_Content_Loader. Orchestrates the hooks of the plugin.
		* - America_Related_Content_i18n. Defines internationalization functionality.
		* - America_Related_Content_Admin. Defines all hooks for the admin area.
		* - America_Related_Content_Public. Defines all hooks for the public side of the site.
		*
		* Create an instance of the loader which will be used to register the hooks
		* with WordPress.
		*
		* @since    1.0.0
		* @access   private
		*/

	private function load_dependencies() {
		require_once AMERICA_RELATED_CONTENT_DIR . 'includes/class-america-related-content-loader.php';
		require_once AMERICA_RELATED_CONTENT_DIR . 'includes/class-america-related-content-i18n.php';
		require_once AMERICA_RELATED_CONTENT_DIR . 'includes/class-gamajo-template-loader.php';
		require_once AMERICA_RELATED_CONTENT_DIR . 'includes/class-america-related-content-template-loader.php';
		require_once AMERICA_RELATED_CONTENT_DIR . 'includes/advanced-custom-fields/acf.php';
		require_once AMERICA_RELATED_CONTENT_DIR . 'admin/class-america-related-content-admin.php';
		require_once AMERICA_RELATED_CONTENT_DIR . 'public/class-america-related-content-public.php';

		/**
			* Custom settings for Advanced Custom Fields
			*/

		add_filter( 'acf/settings/path', function() {
			return AMERICA_RELATED_CONTENT_DIR . 'includes/advanced-custom-fields/';
		});

		add_filter( 'acf/settings/dir', function() {
			return AMERICA_RELATED_CONTENT_URL . 'includes/advanced-custom-fields/';
		});

		add_filter( 'acf/settings/save_json', function( $path ) {
			$path = AMERICA_RELATED_CONTENT_DIR . 'includes/acf-json';
			return $path;
		});

		add_filter( 'acf/settings/load_json', function( $paths ) {
			unset($paths[0]);
		  $paths[] = AMERICA_RELATED_CONTENT_DIR . 'includes/acf-json';
		  return $paths;
		});

		// Hide Advanced Custom Fields from Wordpress Admin Menu
		add_filter('acf/settings/show_admin', '__return_false');

		$this->loader = new America_Related_Content_Loader();
	}


	/**
		* Define the locale for this plugin for internationalization.
		*
		* Uses the America_Related_Content_i18n class in order to set the domain and to register the hook
		* with WordPress.
		*
		* @since    1.0.0
		* @access   private
		*/

	private function set_locale() {
		$plugin_i18n = new America_Related_Content_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}


	/**
		* Register all of the hooks related to the admin area functionality
		* of the plugin.
		*
		* @since    1.0.0
		* @access   private
		*/

	private function define_admin_hooks() {
		$plugin_admin = new America_Related_Content_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'acf/load_value/name=america_related_content', $plugin_admin, 'add_default_taxonomy_field', 10, 3 );
		$this->loader->add_filter( 'acf/fields/post_object/query/name=america_related_post', $plugin_admin, 'limit_post_types', 10, 3 );
		$this->loader->add_filter( 'acf/load_field/name=america_relate_by_taxonomy_select', $plugin_admin, 'limit_taxonomies_america_relate_by_taxonomy_select' );
	}


	/**
		* Register all of the hooks related to the public-facing functionality
		* of the plugin.
		*
		* @since    1.0.0
		* @access   private
		*/

	private function define_public_hooks() {
		$plugin_public = new America_Related_Content_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'tha_content_bottom', $plugin_public, 'america_related_content_activate' );
		$this->loader->add_action( 'after_setup_theme', $plugin_public, 'add_image_sizes' );
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
		* The name of the plugin used to uniquely identify it within the context of
		* WordPress and to define internationalization functionality.
		*
		* @since     1.0.0
		* @return    string    The name of the plugin.
		*/

	public function get_plugin_name() {
		return $this->plugin_name;
	}


	/**
		* The reference to the class that orchestrates the hooks with the plugin.
		*
		* @since     1.0.0
		* @return    America_Related_Content_Loader    Orchestrates the hooks of the plugin.
		*/

	public function get_loader() {
		return $this->loader;
	}


	/**
		* Retrieve the version number of the plugin.
		*
		* @since     1.0.0
		* @return    string    The version number of the plugin.
		*/

	public function get_version() {
		return $this->version;
	}
}
