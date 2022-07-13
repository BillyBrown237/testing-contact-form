<?php
/**
 * 
 * Plugin Name:       testing-custom-form
 * Plugin URI:        
 * Description:       Take input through a form and store it in a custom post type
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Billy
 * Author URI:        
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// Our custom post type function

  function my_custom_post_product() {
    $labels = array(
      'name'               => _x( 'Products', 'post type general name' ),
      'singular_name'      => _x( 'Product', 'post type singular name' ),
      'add_new'            => _x( 'Add New', 'book' ),
      'add_new_item'       => __( 'Add New Product' ),
      'edit_item'          => __( 'Edit Product' ),
      'new_item'           => __( 'New Product' ),
      'all_items'          => __( 'All Products' ),
      'view_item'          => __( 'View Product' ),
      'search_items'       => __( 'Search Products' ),
      'not_found'          => __( 'No products found' ),
      'not_found_in_trash' => __( 'No products found in the Trash' ),
      'menu_name'          => 'Products'
    );
    $args = array(
      'labels'        => $labels,
      'description'   => 'Holds our products and product specific data',
      'public'        => true,
      'supports'      => array( 'title', ),
      'has_archive'   => true,
    );
    register_post_type( 'product', $args ); 
  }
  add_action( 'init', 'my_custom_post_product' );

//Meta adding meta box
add_action( 'add_meta_boxes', 'product_info_box' );
function product_info_box() {
    add_meta_box(
    'product_info',__(
    'Product Information', 'myplugin_textdomain'),
    'product_info_content',
    'product',
    'advanced',
    'high',
    );
}

function product_info_content($post){
    wp_nonce_field( basename( __FILE__ ), 'product_info_content_nonce' );
    $produce_stored_meta = get_post_meta( $post->ID );
    ?>
    <style>
        p{display:flex;
        flex-direction: column;}
        .product-meta{width: 100%;}
    </style>
    <p>
        <label for="meta-name" class="product-row-title"><?php _e( 'Product Name', 'product-textdomain' )?></label>
        <input class="product-meta" type="text" name="meta-name" id="meta-name" value="<?php if ( isset ( $produce_stored_meta['meta-name'] ) ) echo $produce_stored_meta['meta-name'][0]; ?>" />
    </p>

    <p>
        <label for="meta-text" class="product-row-title"><?php _e( 'Manufactured Date', 'product-textdomain' )?></label>
        <input class="product-meta" type="date" name="meta-date" id="meta-date" value="<?php if ( isset ( $produce_stored_meta['meta-date'] ) ) echo $produce_stored_meta['meta-date'][0]; ?>" />
    </p>

    <p>
        <label for="meta-description" class="product-row-title"><?php _e( 'Product Description', 'product-textdomain' )?></label>
        <input class="product-meta" type="text" name="meta-description" id="meta-description" value="<?php if ( isset ( $produce_stored_meta['meta-description'] ) ) echo $produce_stored_meta['meta-description'][0]; ?>" />
    </p>
 
    <?php
}

/**
 * Saves the custom meta input
 */
function product_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'product_info_content_nonce' ] ) && wp_verify_nonce( $_POST[ 'product_info_content_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
    // saves name
    if( isset( $_POST[ 'meta-name' ] ) ) {
        update_post_meta( $post_id, 'meta-name', sanitize_text_field( $_POST[ 'meta-name' ] ) );
    }

    //saves date
    if( isset( $_POST[ 'meta-date' ] ) ) {
        update_post_meta( $post_id, 'meta-date', sanitize_text_field( $_POST[ 'meta-date' ] ) );
    }

    //saves description
    if( isset( $_POST[ 'meta-description' ] ) ) {
        update_post_meta( $post_id, 'meta-description', sanitize_text_field( $_POST[ 'meta-description' ] ) );
    }

 
}
add_action( 'save_post', 'product_meta_save' );


// function product_add_custom_post_types($query) {
//     if ( is_home() && $query->is_main_query() ) {
//         $query->set( 'post_type', array( 'product', ) );
//     }
//     return $query;
// }
// add_action('pre_get_posts', 'product_add_custom_post_type');
/* Filter the post class hook with our custom post class function. */
// $args = array(
//     'post_type' => 'product',
//     'meta-key' => 'meta-name',
//     'meta-key' => 'meta-date',
//     'meta-key' => 'meta-description',
    
// );
//   $products = new WP_Query( $args );
//   if( $products->have_posts() ) {
//     while( $products->have_posts() ) {
//       $products->the_post();
//       ?>
//         <h1><?php the_title() ?></h1>
//         </div>
//       <?php
//     }
//   }
//   else {
//     echo 'Oh ohm no products!';
//   }


  //////////////////////////////////////Form

  function html_form_code() {
    echo '<form action="testing-contact-form.php" method="post">';
    echo '<p>';
    echo 'Product Name (required) <br />';
    echo '<input type="text" name="meta-name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["meta-name"] ) ? esc_attr( $_POST["meta-name"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p>';
    echo 'Your Email (required) <br />';
    echo '<input type="date" name="meta-date" value="' . ( isset( $_POST["meta-date"] ) ? esc_attr( $_POST["meta-date"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p>';
    echo 'Subject (required) <br />';
    echo '<input type="text" name="cf-subject" pattern="[a-zA-Z ]+" value="' . ( isset( $_POST["meta-description"] ) ? esc_attr( $_POST["meta-description"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<input name="submit" type="submit" />';
    echo '</form>';
}
function cf_shortcode() {
    ob_start();
    html_form_code();
    return ob_get_clean();
}
add_shortcode( 'sitepoint_contact_form', 'cf_shortcode' );