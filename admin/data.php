<?php
// post type
function add_positions_post_type() {
  $args = array(
    'public' => true,
    'label'  => 'Positions',
    'labels' => array('add_new' => 'Add new position'),
    'description'        => __( 'Positions', 'Positions' ),
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => true,
    'menu_position'      => null,
    'supports'           => array( 'title', 'editor', 'hierarchical','page-attributes'),
    'menu_icon'          => 'dashicons-networking',

  );
  register_post_type( 'positions', $args );
}

add_action( 'init', 'add_positions_post_type' );

//tax

add_action( 'init', 'create_position_tax' );

function create_position_tax() {
  register_taxonomy(
    'position_tax',
    array('positions'),
    array(
      'show_in_nav_menus' => true,
      'label' => __( 'Position Category' ),
      // 'rewrite' => array('slug'=>'discover'),
      'hierarchical' => true,
      'show_admin_column' =>true
    )
  );
}

function metabox_to_test_reg() {
  add_meta_box(
    'data_meta_box', // $id
    'Data', // $title
    'callback_to_section_data', // $callback
    'positions', // $screen
    'normal', // $context
    'high' // $priority
  );
}

global $fields;
$fields = array('email','ext','phone');

add_action( 'add_meta_boxes', 'metabox_to_test_reg' );

function callback_to_section_data($post){

wp_nonce_field( 'mi_meta_box_nonce', 'meta_box_nonce' );

?>
  <table>
    <?php global $fields; foreach ($fields as $key => $value): ?>
      <tr>
        <td><b><?php echo $value ?></b>:</td>
        <td> <input type="text" name="<?php echo $value ?>" value="<?php echo get_post_meta($post->ID,$value, true); ?>"></td>
      </tr>
    <?php endforeach; ?>
  </table>


<?php

}





/*
**** Save meta box content ****
*/
function twp_save_meta_box( $post_id ) {
	// Comprobamos si es auto guardado
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	// Comprobamos el valor nonce creado en twp_mi_display_callback()
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'mi_meta_box_nonce' ) ) return;
	// Comprobamos si el usuario actual no puede editar el post
	if( !current_user_can( 'edit_post' ) ) return;


	// Guardamos...
  global $fields;
  foreach ($fields as $key => $value) {
    if( isset( $_POST[$value] ) )
  	update_post_meta( $post_id, $value, $_POST[$value] );
  }


}
add_action( 'save_post', 'twp_save_meta_box' );

?>
