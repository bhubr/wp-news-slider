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
		// var_dump($post_type);
		// var_dump($image_ids);
		/* Query Recent Posts */
		global $wpdb;
		$recent_posts = $wpdb->get_results(
			"SELECT ID,guid from $wpdb->posts WHERE ID IN (" . $args['images'] . ") ORDER BY ID DESC"
		);
		// $recent_posts = get_posts( array(
		// 	'post__in ' => $image_ids,
		// 	// 'showposts' => $number,
		// 	// 'nopaging' => 0,
		// 	'post_status' => 'inherit',
		// 	// 'caller_get_posts' => 1,
		// 	'post_type' => $post_type
		// ) );
		if ( empty( $recent_posts ) ) {
			return;
		}

		/* Recompute posts number */
		// $recent_posts = $query->get_posts();
		$post_count = count( $recent_posts );
		if ( $post_count < $number ) {
			$number = $post_count;
		}

		/* Setup posts before sending them to Twig view */
		$posts = array_map( function( $post ) {
			return array(
				// 'id' => $post->ID,
				// 'title'     => $post->post_title,
				// 'permalink' => get_the_permalink( $post ),
				// 'excerpt'   => post_html_excerpt( $post->post_content ),
				'thumbnail' => "<img id=\"image-{$post->ID}\" class=\"pure-img\" src=\"{$post->guid}\" />",
			);
		}, $recent_posts );
		if( empty( $args['bullet_style'] ) ) {
			$args['bullet_style'] = 'number';
		}
		if( empty( $args['direction'] ) ) {
			$args['direction'] = 'vertical';
		}

		$view = array(
			'id'      => $this->id,
			'posts'   => $posts,
			'options' => $args,
			'jsonOptions' => json_encode( $args ),
		);
		return $this->twig->render( 'shortcode_img.twig.html', $view );
	}
}