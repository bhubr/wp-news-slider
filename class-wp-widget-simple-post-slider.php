<?php
require_once 'vendor/autoload.php';

/**
 * WP_Widget_Simple_Post_Slider widget class
 */
class WP_Widget_Simple_Post_Slider extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'widget_news_slider', 'description' => __( "The most recent posts on your site") );
        parent::__construct('news-slider', __( 'News Slider', 'wpnsw' ), $widget_ops);
        $this->alt_option_name = 'widget_news_slider';

        add_action( 'save_post', array(&$this, 'flush_widget_cache') );
        add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
        add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
    }

    function widget($args, $instance) {
        $cache = wp_cache_get('widget_news_slider', 'widget');

        if ( !is_array($cache) )
            $cache = array();

        if ( isset($cache[$args['widget_id']]) ) {
            echo $cache[$args['widget_id']];
            return;
        }

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('News Slider', 'wpnsw' ) : $instance['title'], $instance, $this->id_base);
        
        $number = get_sanitized_post_number( (int) $instance['number'] ); 
        /* Query Recent Posts */
        $query = new WP_Query(array('showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1));
        
        if ( !$query->have_posts() ) return;

        /* Recompute posts number */
        $recent_posts = $query->get_posts();
        $post_count = count( $recent_posts );
        if( $post_count < $number ) $number = $post_count;

        echo $before_widget;
        if( !empty( $instance['title_is_link'] ) ) $title = convert_title_link( $title );
        if ( !empty( $title ) ) echo $before_title . $title . $after_title;

        $loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
        $twig = new Twig_Environment($loader, array(
            //'cache' => __DIR__ . '/compilation_cache',
        ));

        $posts = array_map(function($post) {
            //var_dump($post);
            return array(
                'title'     => $post->post_title,
                'permalink' => get_the_permalink($post),
                'content'   => $post->post_content,
                'thumbnail' => get_the_post_thumbnail($post, 'thumbnail')
            );
        }, $recent_posts);
        // var_dump($posts);
        echo $twig->render('template.twig.html', array('name' => 'Fabien', 'posts' => $posts));


        echo $after_widget;

        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();

        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set('widget_news_slider', $cache, 'widget');
    }
    
        static function register(){
        register_widget('WP_Widget_Simple_Post_Slider');
    }

    function update( $new_instance, $old_instance ) {

        $instance = $old_instance;
        $instance = array( 'title_is_link' => 0, 'show_thumbs' => 0 );
        foreach ( $instance as $field => $val ) {
            if ( isset($new_instance[$field]) )
                $instance[$field] = 1;
        }
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['interval'] = ( !empty( $new_instance['interval'] ) ? (int) $new_instance['interval'] : 5 );
        
        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['widget_news_slider']) )
            delete_option('widget_news_slider');

        return $instance;
    }

    function flush_widget_cache() {
        wp_cache_delete('widget_news_slider', 'widget');
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => "", 'number' => 0, 'interval' => 0, 'show_thumbs' => false, 'title_is_link' => false ) );

        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
            $number = 5;
        if ( !isset($instance['interval']) || !$interval = (int) $instance['interval'] )
            $interval = 5;
?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label>
        <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="2" /></p>

        <p><label for="<?php echo $this->get_field_id('interval'); ?>"><?php _e('Slide interval (seconds):', 'wpnsw' ); ?></label>
        <input id="<?php echo $this->get_field_id('interval'); ?>" name="<?php echo $this->get_field_name('interval'); ?>" type="text" value="<?php echo $interval; ?>" size="3" /></p>

        <input class="checkbox" type="checkbox" <?php checked($instance['title_is_link'], true) ?> id="<?php echo $this->get_field_id('title_is_link'); ?>" name="<?php echo $this->get_field_name('title_is_link'); ?>" />
        <label for="<?php echo $this->get_field_id('title_is_link'); ?>"><?php _e('Turn widget title into a link', 'wpnsw' ); ?></label><br />

        <input class="checkbox" type="checkbox" <?php checked($instance['show_thumbs'], true) ?> id="<?php echo $this->get_field_id('show_thumbs'); ?>" name="<?php echo $this->get_field_name('show_thumbs'); ?>" />
        <label for="<?php echo $this->get_field_id('show_thumbs'); ?>"><?php _e('Show post thumbnails', 'wpnsw' ); ?></label><br />

        <?php
    }
}
