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
      ?>
      <div class="tf-tree tf-gap-lg">
        <?php treeFull() ?>
      </div>
      <?php
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

//full

function treeFull() {
$taxName = "position_tax";
$terms = get_terms($taxName, array('parent' => 0, 'fields' => 'ids'));
subtreeFull($terms, 0, $taxName);
}
function subtreeFull($children_ids, $parrent_id, $taxName) {

    if ( !empty($children_ids) ){
        echo '<ul>';
            foreach($children_ids as $term_child_id) {
                $term_child = get_term_by('id', $term_child_id, $taxName);
                if ( $term_child->parent == $parrent_id) {
                    echo '<li><span class="tf-nc">' . $term_child->name . get_boss_of_unity($term_child->term_id) .' </span>';
                    $term_children = get_term_children($term_child_id, $taxName);
                    get_unity_members($term_child->term_id);
                    subtreeFull($term_children, $term_child_id, $taxName);

                    echo '</li>';
                }
            }
        echo '</ul>';
    }
}

function get_boss_of_unity($id){

  $args = array(
    'post_type' => 'positions',
    'post_status' => array('publish'),
    'posts_per_page' => 1,
    'tax_query' => array( // (array) - use taxonomy parameters (available with Version 3.1).
  'relation' => 'AND', // (string) - The logical relationship between each inner taxonomy array when there is more than one. Possible values are 'AND', 'OR'. Do not use with a single inner taxonomy array. Default value is 'AND'.
  array(
    'taxonomy' => 'position_tax', // (string) - Taxonomy.
    'field' => 'term_id', // (string) - Select taxonomy term by Possible values are 'term_id', 'name', 'slug' or 'term_taxonomy_id'. Default value is 'term_id'.
    'terms' => $id, // (int/string/array) - Taxonomy term(s).
    'include_children' => false, // (bool) - Whether or not to include children for hierarchical taxonomies. Defaults to true.
    'operator' => 'IN' // (string) - Operator to test. Possible values are 'IN', 'NOT IN', 'AND', 'EXISTS' and 'NOT EXISTS'. Default value is 'IN'.
  )
)
  );

  $var = '';
  $the_query = new WP_Query( $args );
  if ( $the_query->have_posts() ) :
  while ( $the_query->have_posts() ) : $the_query->the_post();
    $var = ' / '.get_the_title();
  endwhile;
  endif;
  wp_reset_postdata();
  return $var;
}

function get_unity_members($id){
  $args = array(
    'post_type' => 'positions',
    'post_status' => array('publish'),
    'posts_per_page' => -1,
    'tax_query' => array( // (array) - use taxonomy parameters (available with Version 3.1).
  'relation' => 'AND', // (string) - The logical relationship between each inner taxonomy array when there is more than one. Possible values are 'AND', 'OR'. Do not use with a single inner taxonomy array. Default value is 'AND'.
  array(
    'taxonomy' => 'position_tax', // (string) - Taxonomy.
    'field' => 'term_id', // (string) - Select taxonomy term by Possible values are 'term_id', 'name', 'slug' or 'term_taxonomy_id'. Default value is 'term_id'.
    'terms' => $id, // (int/string/array) - Taxonomy term(s).
    'include_children' => false, // (bool) - Whether or not to include children for hierarchical taxonomies. Defaults to true.
    'operator' => 'IN' // (string) - Operator to test. Possible values are 'IN', 'NOT IN', 'AND', 'EXISTS' and 'NOT EXISTS'. Default value is 'IN'.
  ),

  array(
    'taxonomy' => 'position_type_tax', // (string) - Taxonomy.
    'field' => 'term_id', // (string) - Select taxonomy term by Possible values are 'term_id', 'name', 'slug' or 'term_taxonomy_id'. Default value is 'term_id'.
    'terms' => $id, // (int/string/array) - Taxonomy term(s).
    'include_children' => false, // (bool) - Whether or not to include children for hierarchical taxonomies. Defaults to true.
    'operator' => 'NOT IN' // (string) - Operator to test. Possible values are 'IN', 'NOT IN', 'AND', 'EXISTS' and 'NOT EXISTS'. Default value is 'IN'.
  )
)
  );

  ?>
  <div class="members">
    <?php
    $the_query = new WP_Query( $args );
    if ( $the_query->have_posts() ) :
    while ( $the_query->have_posts() ) : $the_query->the_post();
      ?>
      <div class="member"> <?php echo get_the_title(); ?> </div>
      <?php
    endwhile;
    endif;
    wp_reset_postdata();
    ?>
    </div>
  <?php
}

 ?>
