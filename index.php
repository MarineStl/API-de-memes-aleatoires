<?php
/**
* Plugin Name: Meme-plugin
* Plugin URI: https://www.your-site.com/
* Description: API memes aléatoire
* Version: 0.1
* Author: Marine, Mehdi, Aurélien, Grégory
* Author URI: https://www.your-site.com/
**/

//// Create Memes CPT
function memes_post_type() {
    register_post_type( 'memes',
        array(
            'labels' => array(
                'name' => __( 'Memes' ),
                'singular_name' => __( 'Meme' )
            ),
            'public' => true,
            'show_in_rest' => true,
        'supports' => array('title', 'thumbnail'),
        'has_archive' => true,
        'rewrite'   => array( 'slug' => 'my-home-memes' ),
            'menu_position' => 5,
        'menu_icon' => 'dashicons-food',
        // 'taxonomies' => array('cuisines', 'post_tag') // this is IMPORTANT
        )
    );
}
add_action( 'init', 'memes_post_type' );


/**
 * Grab latest post title by an author!
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest, * or null if none.
 */
function get_meme() {
  $post = get_posts([
    'post_type' => 'memes',
    'orderby' => 'rand'
  ]);

  $post = $post[0];

  if ( empty( $post ) ) {
    return null;
  }

  $thumbnail = get_the_post_thumbnail_url($post->ID);

  return [
    'title' => $post->post_title,
    'image' => $thumbnail
  ];
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'meme/v1', '/random', array(
    'methods' => 'GET',
    'callback' => 'get_meme',
  ) );
} );

function shortcode_meme(){
    $image = wp_remote_get('http://localhost:10014/wp-json/meme/v1/random');
    $image = $image['body'];
    $image = json_decode($image)->image;

    return '<img src="' . $image . '" />';
}
add_shortcode('meme', 'shortcode_meme');
