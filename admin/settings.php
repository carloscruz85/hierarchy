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
    'capability_type'     => array('cc85_hierarchy_manager','cc85_hierarchy_managers'),
    'map_meta_cap'        => true,

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
      'show_admin_column' =>true,
      'capabilities' => array(
        'manage_terms' => 'edit_cc85_hierarchy_managers',
        'edit_terms'    => 'edit_cc85_hierarchy_managers',
        'delete_terms'  => 'edit_cc85_hierarchy_managers',
        'assign_terms'  => 'edit_cc85_hierarchy_managers'
      )
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
    <!-- <tr>
      <td><b>boss?</b></td>
      <td><input type="checkbox" name="boss" value="true" <?php if(get_post_meta($post->ID,'boss', true) == 'true') echo 'checked' ?>></td>
    </tr> -->
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
          <label for="main_unity_check"><?php _e('Main Unity'); ?></label>
      </th>
      <td>
          <input type="checkbox" name="main_unity_check" id="main_unity_check" value="true">
          <br />
          <span class="description"><?php _e('It\'s a main unity?'); ?></span>
      </td>
  </tr>

  <tr class="form-field">
      <th scope="row" valign="top">
          <label for="boss_unity"><?php _e('Boss Unity'); ?></label>
      </th>
      <td>
          <input type="checkbox" name="boss_unity" id="boss_unity" value="true">
          <br />
          <span class="description"><?php _e('It\'s a boss unity?'); ?></span>
      </td>
  </tr>
  <?php

}


add_action( 'position_tax_edit_form_fields', 'misha_edit_term_fields', 10, 2 );

function misha_edit_term_fields( $term, $taxonomy ) {

	$value = get_term_meta( $term->term_id, 'main_unity_check', true );

?>
<tr class="form-field">
    <th scope="row" valign="top">
        <label for="main"><?php _e('Main Unity'); ?></label>
    </th>
    <td>
        <input type="checkbox" name="main_unity_check" id="main_unity_check" value="true" <?php if($value == 'true') echo "checked"; ?>>
        <br />
        <span class="description"><?php _e('It\'s a main unity?'); ?></span>
    </td>
</tr>

<?php 	$bossUnity = get_term_meta( $term->term_id, 'boss_unity', true ); ?>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="boss_unity"><?php _e('Boss Unity'); ?></label>
    </th>
    <td>
        <input type="checkbox" name="boss_unity" id="boss_unity" value="true" <?php if($bossUnity == 'true') echo "checked"; ?>>
        <br />
        <span class="description"><?php _e('It\'s a boss unity?'); ?></span>
    </td>
</tr>
<?php

}

add_action( 'created_position_tax', 'misha_save_term_fields' );
add_action( 'edited_position_tax', 'misha_save_term_fields' );

function misha_save_term_fields( $term_id ) {

	update_term_meta(
		$term_id,
		'main_unity_check',
		sanitize_text_field( $_POST[ 'main_unity_check' ] )
	);

  update_term_meta(
    $term_id,
    'boss_unity',
    sanitize_text_field( $_POST[ 'boss_unity' ] )
  );

}

//add column to tax_position list

// these filters will only affect custom column, the default column will not be affected
// filter: manage_edit-{$taxonomy}_columns
function custom_column_header( $columns ){
    $columns['header_name'] = 'is Main unity';
    $columns['boos_column'] = 'is Boss';
    return $columns;
}
add_filter( "manage_edit-position_tax_columns", 'custom_column_header', 10);



// parm order: value_to_display, $column_name, $tag->term_id
// filter: manage_{$taxonomy}_custom_column
function custom_column_content( $value, $column_name, $tax_id ){
    // var_dump( $column_name );
    // var_dump( $value );
    // var_dump( $tax_id );

    // for multiple custom column, you may consider using the column name to distinguish

    // although If clause is working, Switch is a more generic and well structured approach for multiple columns
    // if ($column_name === 'header_name') {
        // echo '1234';
    // }
    switch( $column_name ) {
          case 'header_name':
               // your code here
               $value = '';
               if(get_term_meta( $tax_id, 'main_unity_check', true )) $value = 'Is Main';

          break;

          case 'boos_column':
               // your code here
               $value = '';
               if(get_term_meta( $tax_id, 'boss_unity', true )) $value = 'Is Boss';
          break;

          // ... similarly for more columns
          default:
          break;
    }

    return $value; // this is the display value
}
add_action( "manage_position_tax_custom_column", 'custom_column_content', 10, 3);

// adding custom role to manage positions

function cc85_hierarchy_add_position_management_role() {
 add_role('cc85_hierarchy_manager',
            'Hierarchy Manager',
            array(
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                'publish_posts' => false,
                'upload_files' => false,
            )
        );
   }
   add_action( 'init', 'cc85_hierarchy_add_position_management_role' );
   // remove_role('cc85_hierarchy_manager');
   //adding capabilites to hierarchy managers
   add_action('admin_init','cc85_hierarchy_add_role_caps',999);
function cc85_hierarchy_add_role_caps() {

  // Add the roles you'd like to administer the custom post types
  $roles = array('cc85_hierarchy_manager','editor','administrator');

  // Loop through each role and assign capabilities
  foreach($roles as $the_role) {

       $role = get_role($the_role);

       $role->add_cap( 'read' );
              $role->add_cap( 'read_cc85_hierarchy_manager');
              $role->add_cap( 'read_private_cc85_hierarchy_managers' );
              $role->add_cap( 'edit_cc85_hierarchy_manager' );
              $role->add_cap( 'edit_cc85_hierarchy_managers' );
              $role->add_cap( 'edit_others_cc85_hierarchy_managers' );
              $role->add_cap( 'edit_published_cc85_hierarchy_managers' );
              $role->add_cap( 'publish_cc85_hierarchy_managers' );
              $role->add_cap( 'delete_others_cc85_hierarchy_managers' );
              $role->add_cap( 'delete_private_cc85_hierarchy_managers' );
              $role->add_cap( 'delete_published_cc85_hierarchy_manager' );

  }
}
?>
