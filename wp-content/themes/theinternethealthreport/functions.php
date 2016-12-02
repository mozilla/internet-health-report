<?php

// Remove wp_head junk
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Define constants
define('THEME_TEMPLATE_DIRECTORY', get_template_directory_uri());

// Prevent File Modifications
define('DISALLOW_FILE_MODS', true);
show_admin_bar(false);

/*
 * Setup function
 */
function theme_setup() {
  register_nav_menu('main', 'Main');
  add_theme_support('post-thumbnails');
  add_theme_support('title-tag');
  add_theme_support('html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
  ));
  add_theme_support('post-formats', array(
    'aside',
    'image',
    'video',
    'quote',
    'link',
    'gallery',
    'status',
    'audio',
    'chat',
  ));
}
add_action( 'init', 'theme_setup' );

// Remove default posts edit from admin
function post_remove() {
  remove_menu_page('edit.php');
}
add_action('admin_menu', 'post_remove');

// Allow json file upload
function my_myme_types($mime_types){
  $mime_types['json'] = 'application/json';
  return $mime_types;
}
add_filter('upload_mimes', 'my_myme_types', 1, 1);


// Create Custom Post Types
function register_custom_posts() {
  register_post_type('stories',
    array(
      'labels' => array(
        'name' => __( 'Stories' ),
        'singular_name' => __( 'Story' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'stories'),
      'supports' => array('title', 'excerpt', 'thumbnail'),
    )
  );

  register_post_type('charts',
    array(
      'labels' => array(
        'name' => __( 'Charts' ),
        'singular_name' => __( 'Chart' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'charts'),
    )
  );
}
add_action('init', 'register_custom_posts');

// Include stylesheets / javascript
function jquery_footer_load() {
  if( !is_admin()) {
    wp_deregister_script('jquery');
    wp_register_script('jquery', '/wp-includes/js/jquery/jquery.js', FALSE, '1.11.0', TRUE);
    wp_enqueue_script('jquery');
  }
}
add_action('wp_enqueue_scripts', 'jquery_footer_load');

// Async load
function mozilla_async_scripts($url) {
  if ( strpos( $url, '#asyncload') === false ) {
    return $url;
  } else if (is_admin()) {
    return str_replace( '#asyncload', '', $url );
  } else {
    return str_replace( '#asyncload', '', $url )."' async='async";
  }
}
add_filter( 'clean_url', 'mozilla_async_scripts', 11, 1 );

function theme_scripts() {
  wp_enqueue_style( 'theme-fonts', 'https://fonts.googleapis.com/css?family=Arvo|Fira+Sans:300,400,400i,500,700');
  wp_enqueue_style( 'theme-styles', get_template_directory_uri() . '/css/app.css');
  wp_enqueue_script( 'theme-script', get_template_directory_uri() . '/js/app.js', array('jquery'), '', true);

  if (is_page_template('page_section.php') || is_singular('stories')) {
    wp_enqueue_script( 'hypothesis', 'https://hypothes.is/embed.js#asyncload', '', '', true);
  }

  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}
add_action( 'wp_enqueue_scripts', 'theme_scripts');

function add_custom_font() {
  wp_enqueue_style( 'custom-fonts', 'https://fonts.googleapis.com/css?family=Arvo|Fira+Sans:300,400,400i,500,700' );
}
add_action( 'admin_enqueue_scripts', 'add_custom_font' );


/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Sixteen 1.0
 */
function javascript_detection() {
  echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'javascript_detection', 0 );

// Add custom classes to WYSIWYG editor
function my_mce_buttons_2( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );

function my_mce_before_init( $settings ) {
    $style_formats = array(
        array(
        'title' => 'Large Text',
        'selector' => 'p',
        'classes' => 'large-text'
        )
    );

    $settings['style_formats'] = json_encode( $style_formats );
    return $settings;
}
add_filter( 'tiny_mce_before_init', 'my_mce_before_init' );

function add_my_editor_style() {
  add_editor_style();
}
add_action( 'admin_init', 'add_my_editor_style' );

// Get social links
function get_facebook_share_url($page_url) {
  $encoded_url = urlencode($page_url);
  $share_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;

  return $share_url;
}

function get_twitter_share_url($page_url, $text) {
  $encoded_text = urlencode($text);
  $encoded_url = urlencode($page_url);
  $share_url = 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_text;

  return $share_url;
}

function stringToId($str) {
  $strLower = strtolower($str);
  $id = preg_replace("/[\s_]/", "-", $strLower);
  return $id;
}

?>