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
    'supports'           => array( 'title'),
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

  // register_taxonomy(
  //   'position_type_tax',
  //   array('positions'),
  //   array(
  //     'show_in_nav_menus' => true,
  //     'label' => __( 'Position Type' ),
  //     // 'rewrite' => array('slug'=>'discover'),
  //     'hierarchical' => true,
  //     'show_admin_column' =>true
  //   )
  // );
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
    <tr>
      <td><b>boss?</b></td>
      <td><input type="checkbox" name="boss" value="true" <?php if(get_post_meta($post->ID,'boss', true) == 'true') echo 'checked' ?>></td>
    </tr>
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

  //boss
  if( isset( $_POST['boss'] ) ){
    update_post_meta( $post_id, 'boss', $_POST['boss'] );
  }
  else
    update_post_meta( $post_id, 'boss', 'false' );



}
add_action( 'save_post', 'twp_save_meta_box' );


//adding custom field in taxonomy


add_action( 'position_tax_add_form_fields', 'position_tax_add_term_fields' );

function position_tax_add_term_fields( $taxonomy ) {
  ?>
  <tr class="form-field">
      <th scope="row" valign="top">
          <label for="main"><?php _e('Main Unity'); ?></label>
      </th>
      <td>
          <input type="checkbox" name="misha-text" id="misha-text" value="true">
          <br />
          <span class="description"><?php _e('It\'s a main unity?'); ?></span>
      </td>
  </tr>
  <?php

}


add_action( 'position_tax_edit_form_fields', 'misha_edit_term_fields', 10, 2 );

function misha_edit_term_fields( $term, $taxonomy ) {

	$value = get_term_meta( $term->term_id, 'misha-text', true );

?>
<tr class="form-field">
    <th scope="row" valign="top">
        <label for="main"><?php _e('Main Unity'); ?></label>
    </th>
    <td>
        <input type="checkbox" name="misha-text" id="misha-text" value="true" <?php if($value == 'true') echo "checked"; ?>>
        <br />
        <span class="description"><?php _e('It\'s a main unity?'); ?></span>
    </td>
</tr>
<?php

}

add_action( 'created_position_tax', 'misha_save_term_fields' );
add_action( 'edited_position_tax', 'misha_save_term_fields' );

function misha_save_term_fields( $term_id ) {

	update_term_meta(
		$term_id,
		'misha-text',
		sanitize_text_field( $_POST[ 'misha-text' ] )
	);

}
?>
