<?php
/**
 * Class for the actual Post Slider Widget
 */
require_once 'vendor/autoload.php';
require_once 'class-twig-wordpress-widget.php';

/**
 * WP_Widget_Simple_Post_Slider widget class
 */
class WP_Widget_Simple_Post_Slider extends WP_Widget {

	/**
	 * Constructor
	 *
	 * Initialize stuff, register WP actions
	 */
	function __construct() {
		$widget_ops = array( 'classname' => 'simple_post_slider', 'description' => __( 'The most recent posts on your site' ) );
		parent::__construct( 'simple-post-slider', __( 'Simple Post Slider', 'wpnsw' ), $widget_ops );
		$this->alt_option_name = 'simple_post_slider';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );

		$loader = new Twig_Loader_Filesystem( __DIR__ . '/templates' );
		$this->twig = new Twig_Environment($loader, array(
			// 'cache' => __DIR__ . '/compilation_cache',
		) );
		$this->twig->addExtension( Twig_WordPress_Widget::get_instance( $this ) );

	}

	/**
	 * Outputs widget
	 * @param array arguments for this instance
	 * @param string instance the instance
	 */
	function widget($args, $instance) {
		$cache = wp_cache_get( 'simple_post_slider', 'widget' );
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		$output = '';
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'News Slider', 'wpnsw' ) : $instance['title'], $instance, $this->id_base );
		$number = get_sanitized_post_number( (int) $instance['number'] );

		/* Query Recent Posts */
		$query = new WP_Query( array( 'showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1 ) );
		if ( ! $query->have_posts() ) {
			return;
		}

		/* Recompute posts number */
		$recent_posts = $query->get_posts();
		$post_count = count( $recent_posts );
		if ( $post_count < $number ) {
			$number = $post_count;
		}

		$output .= $args['before_widget'];
		if ( ! empty( $instance['title_is_link'] ) ) {
			$title = convert_title_link( $title );
		}
		if ( ! empty( $title ) ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		/* Setup posts before sending them to Twig view */
		$posts = array_map( function( $post ) {
			return array(
				'title'     => $post->post_title,
				'permalink' => get_the_permalink( $post ),
				'excerpt'   => post_html_excerpt( $post->post_content ),
				'thumbnail' => get_the_post_thumbnail( $post, 'thumbnail' ),
			);
		}, $recent_posts );

		if( empty( $instance['bullet_style'] ) ) {
			$instance['bullet_style'] = 'number';
		}
		if( empty( $instance['direction'] ) ) {
			$instance['direction'] = 'vertical';
		}

		$view = array(
			'id'      => $this->id,
			'posts'   => $posts,
			'options' => $instance,
			'jsonOptions' => json_encode( $instance ),
		);
		$output .= $this->twig->render( 'widget.twig.html', $view );
		$output .= $args['after_widget'];

		// Reset the global $the_post as this query will have stomped on it.
		wp_reset_postdata();

		$cache[ $args['widget_id'] ] = $output;
		wp_cache_set( 'widget_simple_post_slider', $cache, 'widget' );
		echo $output;
	}

	/**
	 * Register the widget
	 */
	static function register(){
		register_widget( 'WP_Widget_Simple_Post_Slider' );
	}

	/**
	 * Update widget instance options
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance = array( 'title_is_link' => 0, 'show_thumbs' => 0 );
		$checkboxes = array('title_is_link', 'show_thumbs', 'autoplay');
		foreach( $checkboxes as  $field ) {
			if ( array_key_exists($field, $new_instance ) && $new_instance[ $field ] === 'on' ) {
				$instance[ $field ] = 1;
			}
		}
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['interval'] = ( ! empty( $new_instance['interval'] ) ? (int) $new_instance['interval'] : 5 );
		$instance['direction'] = $new_instance['direction'];
		$instance['bullet_style'] = $new_instance['bullet_style'];

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_news_slider'] ) ) {
			delete_option( 'widget_news_slider' );
		}

		return $instance;
	}

	/**
	 * Yet another bloody thing that I don't remind of
	 */
	function flush_widget_cache() {
		wp_cache_delete( 'widget_news_slider', 'widget' );
	}

	/**
	 * Options form for this widget instance
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => "", 'number' => 0, 'interval' => 0, 'show_thumbs' => false, 'title_is_link' => false ) );

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$direction = isset( $instance['direction'] ) ? esc_attr( $instance['direction'] ) : 'vertical';
		$bullet_style = isset( $instance['bullet_style'] ) ? esc_attr( $instance['bullet_style'] ) : 'number';
		$bullet_style = isset( $instance['autoplay'] ) ? esc_attr( $instance['autoplay'] ) : 1;

		if ( !isset( $instance['number'] ) || !$number = (int) $instance['number'] ) {
			$number = 5;
		}
		if ( !isset($instance['interval']) || !$interval = (int) $instance['interval'] ) {
			$interval = 5;
		}

		echo $this->twig->render( 'options_form.twig.html', array(
			'title'        => $title,
			'number'       => $number,
			'interval'     => $interval,
			'instance'     => $instance,
			'direction'    => $direction,
			'bullet_style' => $bullet_style,
			'str'          => array(
				'title'         => __( 'Title:' ),
				'number'        => __( 'Number of posts to show:' ),
				'interval'      => __( 'Slide interval (seconds):', 'wpnsw' ),
				'title_is_link' => __( 'Turn widget title into a link', 'wpnsw' ),
				'show_thumbs'   => __( 'Show post thumbnails', 'wpnsw' ),
				'autoplay'      => __( 'Automatically start sliding', 'wpnsw' ),
				'direction'     => __( 'Slide direction (horizontal/vertical)', 'wpnsw' ),
				'bullet_style'  => __( 'Bullet style (number/bullet)', 'wpnsw' ),
				'directions'    => array(
					'vertical'    => __( 'Vertical', 'wpnsw' ),
					'horizontal'  => __( 'Horizontal', 'wpnsw' ),
				),
				'bullet_styles' => array(
					'number'      => __( 'Number', 'wpnsw' ),
					'bullet'      => __( 'Bullet', 'wpnsw' ),
				),
			)
		) );
	}
}
