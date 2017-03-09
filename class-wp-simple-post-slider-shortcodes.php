<?php
/**
 * Class for the actual Post Slider shortcode
 */

/**
 * WP_Simple_Post_Slider_Shortcodes widget class
 */
class WP_Simple_Post_Slider_Shortcodes {
	function __construct() {
		$loader = new Twig_Loader_Filesystem( __DIR__ . '/templates' );
		$twig_options = WP_DEBUG === true ? array() : array(
			'cache' => __DIR__ . '/compilation_cache'
		);
		$this->twig = new Twig_Environment( $loader, $twig_options );
		add_shortcode('simple_slider', [$this, 'do_shortcode']);
		// add_action('init', [$this, 'register_slider_post_type']);
	}

	function register_slider_post_type() {
		// register_post_type('slider_post', []);
	}

	/**
	 * Outputs widget
	 * @param array arguments for this instance
	 * @param string instance the instance
	 */
	function do_shortcode($args) {
		$number = isset($args['number']) ? $args['number'] : 5;
		$post_type = isset($args['post_type']) ? $args['post_type'] : 'attachment';
		$image_ids = array_map(function($id) { return (int)$id; }, explode(',', $args['images']));

		global $wpdb;
		$posts = $wpdb->get_results(
			"SELECT ID,guid from $wpdb->posts WHERE ID IN (" . $args['images'] . ") ORDER BY ID DESC"
		);
		$post_metas = $wpdb->get_results(
			"SELECT post_id, meta_value from $wpdb->postmeta WHERE post_id IN (" . $args['images'] . ") AND meta_key = '_wp_attachment_image_alt'"
		);
		$post_metas_indexed = array_reduce( $post_metas, function( $carry, $meta ) {
			$carry[ $meta->post_id ] = $meta->meta_value;
			return $carry;
		}, [] );
		$recent_posts = array_map( function( $post ) use( $post_metas_indexed ) {
			$post->img_alt = array_key_exists( $post->ID, $post_metas_indexed ) ?
				$post_metas_indexed[ $post->ID ] : '';
			return $post;
		}, $posts );

		if ( empty( $recent_posts ) ) {
			return;
		}

		/* Recompute posts number */
		$post_count = count( $recent_posts );
		if ( $post_count < $number ) {
			$number = $post_count;
		}

		/* Setup posts before sending them to Twig view */
		$posts = array_map( function( $post ) {
			return array(
				'thumbnail' => "<img id=\"image-{$post->ID}\" class=\"pure-img\" src=\"{$post->guid}\" alt=\"{$post->img_alt}\" />",
			);
		}, $recent_posts );
		if( empty( $args['bullet_style'] ) ) {
			$args['bullet_style'] = 'number';
		}
		if( empty( $args['direction'] ) ) {
			$args['direction'] = 'vertical';
		}

		$view = array(
			'posts'   => $posts,
			'options' => $args,
			'jsonOptions' => json_encode( $args ),
		);
		return $this->twig->render( 'shortcode_img.twig.html', $view );
	}
}