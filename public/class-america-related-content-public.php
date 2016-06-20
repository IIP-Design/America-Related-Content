<?php

/**
	* The public-facing functionality of the plugin.
	*
	* @link       https://github.com/IIP-Design
	* @since      1.0.0
	*
	* @package    America_Related_Content
	* @subpackage America_Related_Content/public
	*/




/**
	* The public-facing functionality of the plugin.
	*
	* Defines the plugin name, version, and two examples hooks for how to
	* enqueue the admin-specific stylesheet and JavaScript.
	*
	* @package    America_Related_Content
	* @subpackage America_Related_Content/public
	* @author     Office of Design, U.S. Department of State <kleekampnf@america.gov>
	*/

class America_Related_Content_Public {

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
		* @param      string    $plugin_name       The name of the plugin.
		* @param      string    $version    The version of this plugin.
		*/

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->template_loader = new America_Related_Content_Template_Loader;
	}


	/**
		* Register the stylesheets for the public-facing side of the site.
		*
		* @since    1.0.0
		*/

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, AMERICA_RELATED_CONTENT_URL . 'public/css/america-related-content-public.css', array(), $this->version, 'all' );
	}


	/**
		* Register the JavaScript for the public-facing side of the site.
		*
		* @since    1.0.0
		*/

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, AMERICA_RELATED_CONTENT_URL . '/public/js/america-related-content-public.js', array( 'jquery' ), $this->version, false );
	}


	/**
		* Activates the public portion of the plugin
		*/

	public function america_related_content_activate() {
		if ( ! have_rows( 'america_related_content' ) ) {
			return;
		}

		if ( ! is_single() ) {
			return;
		}

		$data = $this->america_related_content();

		$this->template_loader->set_template_data( $data, 'related_posts' );
		$this->template_loader->get_template_part( 'related-content' );
	}


	/**
		* The main function used to tie the whole thing together.
		*
		* @return $posts Array - An array of Wordpress post objects
		* @since 1.0.0`
		*/

	private function america_related_content() {
		while ( have_rows( 'america_related_content' ) ) : the_row();

			if ( get_row_layout() === 'america_relate_by_taxonomy' ) :
				$taxonomy = get_sub_field( 'america_relate_by_taxonomy_select' );
				$count = get_sub_field( 'america_number_of_related_posts' );
				$posts = $this->america_get_related_posts_by_taxonomy( $taxonomy, $count );
			elseif ( get_row_layout() === 'america_relate_by_manually' ) :
				$posts = $this->america_get_related_posts_manually();
			endif;

		endwhile;

		return $posts;
	}


	/**
		* Get the manually promoted posts
		*
		* @return $posts Array - An array of post objects
		* @since 1.0.0
		*/

	private function america_get_related_posts_manually() {
		$posts = array();

		while ( have_rows( 'america_relate_by_manually_repeater' ) ) : the_row();
			array_push( $posts, get_sub_field( 'america_related_post' ) );
		endwhile;

		return $posts;
	}


	/**
		* The related posts based on taxonomy type
		*
		* @param $taxonomy String - Taxonomy type
		*
		* @return $posts Array - An array of post objects
		* @since 1.0.0
		*/

	private function america_get_related_posts_by_taxonomy( $taxonomy, $count ) {
		Global $wp_query;

		$posts = array();
		$post_id = $wp_query->get_queried_object_id();
		$post_taxonomies = get_post_taxonomies( $post_id );

		if ( ! in_array( $taxonomy, $post_taxonomies ) ) {
			return $posts;
		}

		// @todo Hard coding taxonomy conditions for now

		if ( $taxonomy === 'category' ) :
			$posts = $this->america_get_related_categories( $post_id, $count );
		elseif ( $taxonomy === 'post_tag' ) :
			$posts = $this->america_get_related_tags( $post_id, $count );
		elseif ( $taxonomy === 'post_format' ) :
			$posts = $this->america_get_related_post_format( $post_id, $count );
		endif;

		return $posts;
	}


	/**
		* Get an array of posts by category
		*
		* @param $post_id Int - The current post's ID
		*
		* @return $posts Array - An array of post objects
		* @since 1.0.0
		*/

	private function america_get_related_categories( $post_id, $count ) {
		$posts = array();
		$categories = wp_get_post_categories( $post_id );

		// Loop through each category
		foreach( $categories as $category ) {
			$args = array(
				'category' => $category,
			);

			// Merge the query results with the $posts array to keep the array flat for easier sorting
			$posts = array_merge( $posts, get_posts( $args ) );
		}

		// Remove the current post from the $posts array
		$posts = $this->filter_results( $post_id, $posts, $count );

		return $posts;
	}


	/**
		* Get an array of posts by tags
		*
		* @param $post_id Int - The current post's ID
		*
		* @return $posts Array - An array of post objects
		* @since 1.0.0
		*/

	private function america_get_related_tags( $post_id, $count ) {
		$posts = array();
		$tags = wp_get_post_tags( $post_id );

		// Get just the `term_id` from the tag objects
		$tags = array_map( function( $x ) { return $x->term_id; }, $tags );

		$args = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'post_tag',
					'field' => 'term_id',
					'terms' => $tags,
				),
			),
		);

		$posts = get_posts( $args );

		// Remove the current post from the $posts array
		$posts = $this->filter_results( $post_id, $posts, $count );

		return $posts;
	}


	/**
		* Get an array of posts by post_format
		*
		* @param $post_id Int - The current post's ID
		*
		* @return $posts Array - An array of post objects
		* @since 1.0.0
		*/

	private function america_get_related_post_format( $post_id, $count ) {
		$posts = array();
		$post_format = get_post_format( $post_id );

		$args = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => array( "post-format-{$post_format}" ),
				),
			),
		);

		$posts = get_posts( $args );

		// Remove the current post from the $posts array
		$posts = $this->filter_results( $post_id, $posts, $count );

		return $posts;
	}


	/**
		* Filter the array of posts. Primarily used to:
		* 		* Limit results list to the number in the `america_number_of_related_posts` field,
		* 		* Remove current post from the post list
		*
		* @param $post_id Int - The current post's ID
		* @param $posts Array - An array of post objects
		* @param $count Int - The number of posts to limit the results to
		*
		* @return $posts Array - An array of post objects
		* @since 1.0.0
		*/

	private function filter_results( $post_id, $posts, $count ) {
		if ( empty( $post_id ) || empty( $posts ) ) {
			return $posts;
		}

		$posts = array_unique( $posts, SORT_REGULAR );

		foreach( $posts as $key => $value ) {
			$post_format = get_post_format( $value->ID );

			// Remove current post from the results
			if ( $value->ID === $post_id ) {
				unset( $posts[$key] );
			}

			// @todo For now, hard code post format link removal
			if ( $post_format === 'link' ) {
				unset ( $posts[$key] );
			}
		}

		// Only return the requested amount of posts
		$posts = array_slice( $posts, 0, $count );

		return $posts;
	}


	/**
		* Add an image size for the plugin so that it's self contained
		*/

	public function add_image_sizes() {
		add_image_size( 'related_thumb', 359, 269, true );
	}
}
