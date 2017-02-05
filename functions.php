<?php
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


/**
 * Close all open xhtml tags at the end of the string
 * @param string $html
 * @param array  $tags_to_strip
 * @return string
 * @author Milian <mail@mili.de> and Benoît Hubert
 */
if( !function_exists( 'close_and_strip_tags' ) ) {
    function close_and_strip_tags($html, $tags_to_strip = array()) {
        // put all opened tags into an array
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        // put all closed tags into an array
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);

        // all tags are closed
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        // close tags
        for ($i = 0 ; $i < $len_opened ; $i++) {
            if (!in_array($openedtags[$i], $closedtags)){
                $html .= '</'.$openedtags[$i].'>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        for ($i = 0 ; $i < $len_opened ; $i++) {
            echo $openedtags[$i];
            if (in_array($openedtags[$i], $tags_to_strip)){
                echo "FOUND " . $openedtags[$i];
            }
        }
        return $html;
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


// Word- and HTML-Tag-sensitive substring
/*function wts_substr( $text, $max_len = 230 ) { 
    // Do something only if text is too long
    if( (strlen($text) > $max_len) ) { 
        // find out position of last white space in specified range (0, $max_len)
        $white_space_pos = strpos( $text, " ", $max_len ) - 1; 
//      $tag_opener_pos  = strrpos( $text, "<", $white_space_pos + 1 );
//      $tag_closer_pos  = strpos(  $text, ">", $tag_opener_pos );
        if( $white_space_pos > 0 ) 
            $text = substr($text, 0, $white_space_pos + 1 );
    } 
    return $text; 
} */


// Replace some tags
function wpnsw_replace( $string ) {
    $tags_to_replace = array(
        'h1'    => array( 'h1', 'style="font-size:medium; text-align:center;"' )
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
            $thumb_str = "<div class='sliderthumb'><img alt='thumb alignleft' src='".$thumbnail[0]."' /></div>";
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
    return ( close_and_strip_tags( $string, ['img'] ) . ' [...]' );
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

function wp_widget_init() {
    register_widget('WP_Widget_Simple_Post_Slider');
}