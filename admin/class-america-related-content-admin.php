<?php

/**
	* The admin-specific functionality of the plugin.
	*
	* @link       https://github.com/IIP-Design
	* @since      1.0.0
	*
	* @package    America_Related_Content
	* @subpackage America_Related_Content/admin
	*/




/**
	* The admin-specific functionality of the plugin.
	*
	* Defines the plugin name, version, and two examples hooks for how to
	* enqueue the admin-specific stylesheet and JavaScript.
	*
	* @package    America_Related_Content
	* @subpackage America_Related_Content/admin
	* @author     Office of Design, U.S. Department of State <kleekampnf@america.gov>
	*/

class America_Related_Content_Admin {

	/**
		* The ID of this plugin.
		*
		* @since    1.0.0
		* @access   private
		* @var      string    $plugin_name    The ID of this plugin.
		*/

	private $plugin_name;


	/**
		* The version of this plugin.
		*
		* @since    1.0.0
		* @access   private
		* @var      string    $version    The current version of this plugin.
		*/

	private $version;


	/**
		* Initialize the class and set its properties.
		*
		* @since    1.0.0
		* @param      string    $plugin_name       The name of this plugin.
		* @param      string    $version    The version of this plugin.
		*/

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


	/**
		* Register the stylesheets for the admin area.
		*
		* @since    1.0.0
		*/

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, AMERICA_RELATED_CONTENT_DIR . 'css/america-related-content-admin.css', array(), $this->version, 'all' );
	}


	/**
		* Register the JavaScript for the admin area.
		*
		* @since    1.0.0
		*/

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, AMERICA_RELATED_CONTENT_DIR . 'js/america-related-content-admin.js', array( 'jquery' ), $this->version, false );
	}


	/**
		* Set relate content by taxonomy (Category) by default in the `america_related_content` field
		*
		* @param $value Array - The value of the field as found in the database
		* @param $post_id Int - The post id which the value was loaded from
		* @param $field Array - The field object (actually an array, not object)
		*
		* @see https://www.advancedcustomfields.com/resources/acfload_value/
		*
		* @since 1.0.0
		*/

	public function add_default_taxonomy_field( $value, $post_id, $field ) {
		if ( $value !== NULL ) {
			return $value;
		}

		$value = array(
			array( 'acf_fc_layout' => 'america_relate_by_taxonomy' ),
		);

		return $value;
	}


	/**
		* Remove all the unwanted post types
		*
		* @return $post_types Array - The post types you want to appear in the Post Object
		* 														select box
		*
		* @since 1.0.0
		*/

	private function filtered_site_post_types() {
		// Exclude "private" post types
		$exclude = array(
			'attachment' => 'attachment',
			'revision' => 'revision',
			'nav_menu_item' => 'nav_menu_item',
			'acf-field-group' => 'acf-field-group',
			'acf-field' => 'acf-field',
			'mc4wp-form' => 'mc4wp-form',
		);

		if ( has_filter( 'america_exclude_post_type' ) ) {
			$exclude = apply_filters( 'america_exclude_post_type', $exclude );
		}

		$post_types = array_diff( get_post_types(), $exclude );

		return $post_types;
	}


	/**
		* Remove all unwanted taxonomies
		*
		* @return $taxonomies Array - The taxonomies you want to appear in the
		*															`america_relate_by_taxonomy_select` select field
		*
		* @since 1.0.0
		*/

	static function filtered_site_taxonomies() {
		// Exclude "private" taxonomies
		$exclude = array(
			'link_category' => 'link_category',
			'nav_menu' => 'nav_menu',
		);

		if ( has_filter( 'america_exclude_taxonomy' ) ) {
			$exclude = apply_filter( 'america_exclude_taxonomy', $exclude );
		}

		$taxonomies = array_diff( get_taxonomies(), $exclude );

		return $taxonomies;
	}


	/**
		* Adjust the WP_Query to include only the desired post types for the `america_related_post` field
		*
		* @param $args Array - The WP_Query arguments
		* @param $field Array - The field array containing all attributes & settings
		* @param $post_id Int - The current post ID being edited
		*
		* @return $args Array - The augmented WP_Query arguments
		* @since 1.0.0
		*/

	public function limit_types_america_related_post_field( $args, $field, $post_id ) {
		$post_type = $this->filtered_site_post_types();
		$args['post_type'] = $post_type;

		return $args;
	}


	/**
		* Populate the `america_relate_by_taxonomy_select` field with the site's taxonomies
		*
		* @return $field
		* @since 1.0.0
		*/

	public function limit_taxonomies_america_relate_by_taxonomy_select( $field ) {
		$taxonomies = America_Related_Content_Admin::filtered_site_taxonomies();
		$field['choices'] = $taxonomies;

		return $field;
	}
}
