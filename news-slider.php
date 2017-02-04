<?php
/*
Plugin Name: WP News Slider Widget
Plugin URI: http://www.efectorelativo.net/laboratory/noobSlide/
Description: Display HTML excerpts from recent posts in a sidebar slider widget
Author: Benoit Hubert (widget), noobSlide's authors (slider code) and  Grzegorz Winiarski (Tiny Plugin for TinyMCE)
Version: 0.5.5-20111021
Author URI: http://benoit.hubert2.free.fr
Copyright 2010-2011 Benoit Hubert
*/ 



add_filter('mce_external_plugins', "wpnsw_mceplugin_register");
add_filter('mce_buttons', 'wpnsw_mceplugin_add_button', 0);

function wpnsw_mceplugin_add_button($buttons)
{
    array_push($buttons, "separator", "wpnsw_mceplugin");
    return $buttons;
}

function wpnsw_mceplugin_register($plugin_array)
{
    $url = plugins_url("editor_plugin.js", __FILE__) ;

    $plugin_array['wpnsw_mceplugin'] = $url;
    return $plugin_array;
}


/** * close all open xhtml tags at the end of the string
 * * @param string $html
 * @return string
 * @author Milian <mail@mili.de>
 */
 if( !function_exists( 'closetags' ) ) {
function closetags($html) {
  #put all opened tags into an array
  preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
  $openedtags = $result[1];   #put all closed tags into an array
  preg_match_all('#</([a-z]+)>#iU', $html, $result);
  $closedtags = $result[1];
  $len_opened = count($openedtags);
  # all tags are closed
  if (count($closedtags) == $len_opened) {
    return $html;
  }
  $openedtags = array_reverse($openedtags);
  # close tags
  for ($i=0; $i < $len_opened; $i++) {
    if (!in_array($openedtags[$i], $closedtags)){
      $html .= '</'.$openedtags[$i].'>';
    } else {
      unset($closedtags[array_search($openedtags[$i], $closedtags)]);    }
  }  return $html;
}  
}

/** 
* word-sensitive substring function with html tags awareness 
* @param text The text to cut 
* @param len The maximum length of the cut string 
* @returns string 
**/ 


function wpnsw_localize() {
	load_plugin_textdomain( 'wpnsw', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/* Scripts JS */
function wpnsw_load_scripts() {
	if( !is_admin() ) {
		//wp_enqueue_script('jquery');
		if( !wp_script_is( 'mootools' ) )
			wp_enqueue_script( 'mootools', plugins_url( '/includes/mootools-1.2.2-core-nc.js', __FILE__) );
		if( !wp_script_is( 'noobslide' ) )
			wp_enqueue_script( 'noobslide', plugins_url( '/includes/_class.noobSlide.packed.js', __FILE__), array( 'mootools' ), '3.11.10' );
		wp_enqueue_script( 'news_slider_js', plugins_url( 'news-slider.js?ts=' . time(), __FILE__), array( 'mootools', 'noobslide' ) );
	}

}

// CSS Tableau
function wpnsw_load_styles() {
	$current_user = wp_get_current_user();
	if (!is_admin() )
		wp_enqueue_style('news_slider_style', plugins_url('/news-slider.css?ts=' . time(), __FILE__) );
}

add_action( 'wp_print_scripts', 'wpnsw_load_scripts' );
add_action( 'get_header', 'wpnsw_load_styles' );
add_action( 'init', 'wpnsw_localize' );

// Word- and HTML-Tag-sensitive substring
/*function wts_substr( $text, $max_len = 230 ) { 
    // Do something only if text is too long
	if( (strlen($text) > $max_len) ) { 
		// find out position of last white space in specified range (0, $max_len)
        $white_space_pos = strpos( $text, " ", $max_len ) - 1; 
//		$tag_opener_pos  = strrpos( $text, "<", $white_space_pos + 1 );
//		$tag_closer_pos  = strpos(  $text, ">", $tag_opener_pos );
        if( $white_space_pos > 0 ) 
            $text = substr($text, 0, $white_space_pos + 1 );
    } 
    return $text; 
} */


// Replace some tags
function wpnsw_replace( $string ) {
	$tags_to_replace = array(
		'h1'	=> array( 'h1', 'style="font-size:medium; text-align:center;"' )
	);
	
	foreach( $tags_to_replace as $tag => $replacement ) {
		$replacement_tag   = $replacement[0];
		$replacement_attrs = $replacement[1];
		$string = str_replace( "<$tag", "<$replacement_tag $replacement_attrs", $string );
		//$string = preg_replace( "/<$tag/", "<$replacement_tag $replacement_attrs", $string );
		$string = str_replace( "</$tag", "</$replacement_tag", $string );
	}
	
	return $string;
}

// Word- and Tag-Sensitive substring
if( !function_exists( 'wts_substr' ) ) {
function wts_substr( $text, $max_len = 230 ) { 
    // Do something only if text is too long
	if( ( strlen( $text ) > $max_len ) ) { 
		// find out position of last white space in specified range (0, $max_len)
        $white_space_pos = strpos( $text, " ", $max_len ) - 1; 
        if( $white_space_pos > 0 ) 
            $cut_text = substr($text, 0, $white_space_pos + 1 );
		$tag_opener_pos  = strrpos( $cut_text, "<" );
		if( $tag_opener_pos > 0 ) {
			$tag_closer_pos  = strpos( $text, "</", $tag_opener_pos );
			if( $tag_closer_pos ) {
				$cut_pos = max( $tag_closer_pos, $white_space_pos + 1 );
				$cut_text = substr($text, 0, $cut_pos );
			}
		}
		if( isset( $cut_text ) ) return $cut_text;
    } 
    return $text; 
}
}

// concatenate content and thumb, and if no thumb get first thumb from gallery
function wpnsw_shiba_filter( $post_id, $content, $thumbnail, $show_thumbs ) {

	global $wpdb;
	/* Preg match setup */
	$matches = array();
	$match = "";
	$id_attrs = array();
	$ids = array();
	$pattern_to_replace = array();
	$replacement_a = array("[Galerie photos]");

	// We enter this condition if we find a gallery
	if( preg_match( '/\[gallery(.)*]/', $content, $matches ) == 1 ) {

			$match = $matches[0];
			$pattern_to_replace[] = '/\[gallery(.)*]/'; // replace the [gallery attr1="xx" attr2="y"] whatever its content
			// An ID was specified for this gallery, then let's retrieve it
			if( preg_match( '/id="(\d)+"/', $match, $id_attrs ) == 1 ) {
					$id_string = $id_attrs[0];
					if( preg_match( '/(\d)+/', $id_string, $ids ) == 1) $post_id = $ids[0]; // replace $post_id parameter with this one
			}
			$posts = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_parent = '$post_id' AND post_type='attachment' ORDER BY ID ASC" );
			$post = $posts[0];
			$thumb_url = wp_get_attachment_thumb_url( $post->ID );
			$meta = wp_get_attachment_metadata( $post->ID, true );
			$thumb_data = $meta['sizes']['thumbnail'];
			if( empty( $thumbnail ) ) $thumbnail = array( $thumb_url, $thumb_data['width'], $thumb_data['height'] );
	}

	$thumb_str = "";
	if( !empty( $thumbnail ) && $show_thumbs ) {
			$thumb_str = "<div class='sliderthumb'><img alt='thumb' src='".$thumbnail[0]."' /></div>";
			$ie7marginleft = 2 + $thumbnail[1];
			$ie7width = 284 - $thumbnail[1];
			$thumb_str .= "<!--[if lt IE 7]><div class='slidercontent' style='margin-left: {$ie7marginleft}px; width: {$ie7width}px;'><![endif]-->\n";
	}

	return array( preg_replace($pattern_to_replace, $replacement_a, $content ), $thumb_str ); 
}


/* Post html excerpt, cut post content after 245 characters or <!--cuthere--> tag */
if( !function_exists( 'post_html_excerpt' ) ) {
function post_html_excerpt( $string ) {
	$morepos = strpos( $string, '<!--cuthere-->' );
	$len = ( $morepos ? $morepos + 13 : 245 );
	$string = wpnsw_replace( wts_substr( $string, $len ) );
	return ( closetags( $string ) . ' [...]' );
}	
}
/* Those functions should be put inside the widget class */
	function get_sanitized_post_number( $number ) {
		if ( empty( $number ) )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 12 )
			$number = 12;
		return $number;
	}

	function get_numbered_buttons( $number, $id ) {
		$out = "<p class='buttons' id='$id'>";
		for( $i = 1; $i <= $number ; $i++ )
			$out .= "<span>$i</span>\n";
		$out .= "</p>";
		return $out;
	}
	
	function get_play_stop_buttons() {
		return "<p class='buttons'>\n\t<span id='stop8'>" 
			. __( 'Stop', 'wpnsw' ) . "</span>\n\t<span id='play8'>"
			. __( 'Play &gt;', 'wpnsw' ) . "</span>\n</p>";
	}

	function convert_title_link( $title ) {
		$page_posts_link = get_permalink( get_option( 'page_for_posts' ) );
		$link_open = "<a href='$page_posts_link'>";
		$link_close = "</a>";
		return $link_open . $title . $link_close;
	}
 /**
 * Recent_Posts widget class
 *
 * @since 2.8.0
 */
class WP_Widget_News_Slider extends WP_Widget {

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
		 ?>

		<div class="sample sample8">
			<input type="hidden" id="wpnsw_interval" value="<?php echo( 1000 * (int)$instance['interval'] ); ?>" />
			<?php echo get_numbered_buttons( $number, "handles8" ); ?>
			<div class="mask1">
					<div id="box8">

					<?php 	global $post; while ($query->have_posts()) : $query->the_post(); ?>

						<div class="excerpt">
							<p class="buttons">
              	<span class="prev"><?php _e( '&lt;&lt; Previous', 'wpnsw' ); ?></span>
                  <span class="next"><?php _e( 'Next &gt;&gt;', 'wpnsw' ); ?></span>
              </p>
							<?php
							echo "<h3><a href='". get_permalink() . "' title='" . esc_attr(get_the_title() ? get_the_title() : get_the_ID()) . "'>" . ( get_the_title() ? get_the_title() : get_the_ID() ) . "</a></h3>\n";
							$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');
							$content = get_the_content();
							$content_and_thumb = wpnsw_shiba_filter( $post->ID, $content, $thumbnail, $instance['show_thumbs'] );
							$content = '<div class="thumb-content">' . post_html_excerpt( $content_and_thumb[0] );
							echo $content_and_thumb[1] . $content . "<br /><a href='" . get_permalink() . "'>Lire la suite...</a></div>\n"; 
							if( $thumbnail && !empty( $instance['show_thumbs'] ) ) echo "<!--[if lt IE 7]></div><![endif]-->\n";
							?>
							
						</div><!-- END excerpt -->

					<?php endwhile; ?>
					</div><!-- END box8 -->
			</div><!-- END mask1 -->
		<?php echo get_play_stop_buttons(); ?>
		<?php echo get_numbered_buttons( $number, "handles8_more" ); ?>
</div><!-- END sample8 -->

<?php 
		echo $after_widget;

		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_news_slider', $cache, 'widget');
	}
	
        static function register(){
		register_widget('WP_Widget_News_Slider');
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
function wp_widget_init() {
	register_widget('WP_Widget_News_Slider');
}
//add_action('init', 'wp_widget_init', 2);
add_action("widgets_init", array('WP_Widget_News_Slider', 'register'));

?>