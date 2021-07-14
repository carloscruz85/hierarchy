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

// A callback function to add a custom field to our "presenters" taxonomy
function position_tax_taxonomy_custom_fields($tag) {
    // x($tag);
   // Check for existing taxonomy meta for the term you're editing
    $t_id = $tag->term_id; // Get the ID of the term you're editing
    $term_meta = get_option( "taxonomy_term_$t_id" ); // Do the check

    // x($term_meta);
    // $term_meta = array(
    //   'main' => 'true'
    // );
    // update_option( "taxonomy_term_$t_id", $term_meta );
?>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="main"><?php _e('Main Unity'); ?></label>
    </th>
    <td>
        <input type="checkbox" name="term_meta[main]" id="term_meta[main]" value="true" <?php if( $term_meta['main'] == 'true' ) echo 'checked' ?>>
        <br />
        <span class="description"><?php _e('It\'s a main unity?'); ?></span>
    </td>
</tr>

<?php
}

// A callback function to save our extra taxonomy field(s)
function save_position_tax_custom_fields( $term_id ) {
    $t_id = $term_id;
    if ( isset( $_POST['term_meta'] ) ) {
        $term_meta = get_option( "taxonomy_term_$t_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
            foreach ( $cat_keys as $key ){
            if ( isset( $_POST['term_meta'][$key] ) ){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option( "taxonomy_term_$t_id", $term_meta );
    }else{
      $term_meta = array(
        'main' => 'false'
      );
      update_option( "taxonomy_term_$t_id", $term_meta );
    }
}

// Add the fields to the "presenters" taxonomy, using our callback function
add_action( 'position_tax_edit_form_fields', 'position_tax_taxonomy_custom_fields', 10, 2 );

// Save the changes made on the "presenters" taxonomy, using our callback function
add_action( 'edited_position_tax', 'save_position_tax_custom_fields', 10, 2 );

?>
