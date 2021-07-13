<?php
//header
//add treeflex to header
add_action( 'init', 'add_tree_flex_to_header' );
function add_tree_flex_to_header() {
 wp_register_style( 'cc85-treeflex', 'https://unpkg.com/treeflex/dist/css/treeflex.css', array(), '1.0', 'all');
}
  // use the registered jquery and style above
  add_action('wp_enqueue_scripts', 'enqueue_style_treeflex');

  function enqueue_style_treeflex(){
  wp_enqueue_style( 'cc85-treeflex' );

  }

  // shortcode
  add_shortcode( 'cc85_hierarchy', 'cc85_hierarchy_function' );

  function cc85_hierarchy_function($att){
    // x($att);
    if ( is_admin()){
      return;
    }
    if( $att['full'] == 'true' ){ //full

    }
    else{ //just cats


      ?>
      <div class="tf-tree tf-gap-lg">

            <?php tree() ?>

</div>

      <?php
    }
  }

function tree() {
$taxName = "position_tax";
$terms = get_terms($taxName, array('parent' => 0, 'fields' => 'ids'));
subtree($terms, 0, $taxName);
}
function subtree($children_ids, $parrent_id, $taxName) {

    if ( !empty($children_ids) ){
        echo '<ul>';
            foreach($children_ids as $term_child_id) {
                $term_child = get_term_by('id', $term_child_id, $taxName);
                if ( $term_child->parent == $parrent_id) {
                    echo '<li><span class="tf-nc">' . $term_child->name . '</span>';
                    $term_children = get_term_children($term_child_id, $taxName);
                    subtree($term_children, $term_child_id, $taxName);
                    echo '</li>';
                }
            }
        echo '</ul>';
    }
}

 ?>
