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

    ?>
    <div class="font-size-controls">
      <div  id="hierarchyfont-">
        <i class="fa fa-minus-circle"></i>
      </div>
      <div id="hierarchyfont+">
        <i class="fa fa-plus-circle"></i>
      </div>
      <div id="hierarchyfont">
        <i class="fa fa-crosshairs"></i>
      </div>
    </div>
    <div class="tf-tree tf-gap-lg" id="main-hierarchy">
    <?php
    if( $att['full'] == 'true' )  treeFull(); //full
    else tree(); //just cats  ?>
  </div>
  <?php
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
                    //get_unity_members($term_child->term_id);
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
    'tax_query' => array(
  'relation' => 'AND',
  array(
    'taxonomy' => 'position_tax',
    'field' => 'term_id',
    'terms' => $id,
    'include_children' => false,
    'operator' => 'IN'
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
    'tax_query' => array(
  'relation' => 'AND',
  array(
    'taxonomy' => 'position_tax',
    'field' => 'term_id',
    'terms' => $id,
    'include_children' => false,
    'operator' => 'IN'
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



  //add js in the footer
  add_action( 'wp_footer', 'cc85_js_code_to_hierarchy' );
  function cc85_js_code_to_hierarchy(){
    ?>

    <script>

      //minus font size
      document.getElementById("hierarchyfont-").addEventListener("click", function(){
        var el = document.getElementById('main-hierarchy')
        var style = window.getComputedStyle(el, null).getPropertyValue('font-size')
        var fontSize = parseFloat(style) - 1;
        document.getElementById("main-hierarchy").style.fontSize = fontSize+'px'
      });

      //plus font size
      document.getElementById("hierarchyfont+").addEventListener("click", function(){
        var el = document.getElementById('main-hierarchy')
        var style = window.getComputedStyle(el, null).getPropertyValue('font-size')
        var fontSize = parseFloat(style) + 1;
        document.getElementById("main-hierarchy").style.fontSize = fontSize+'px'
      });

      //return to default font size
      document.getElementById("hierarchyfont").addEventListener("click", function(){
      document.getElementById("main-hierarchy").style.fontSize = '12px'
      });

      //fit screen
      var hierarchyEl = document.getElementById("main-hierarchy");
      var hierarchyWidth = window.getComputedStyle(hierarchyEl, null).getPropertyValue('width')
      // console.log(hierarchyWidth);
      let child = document.getElementById("main-hierarchy").firstElementChild
      // console.log(child);
      let childWidth = window.getComputedStyle(child, null).getPropertyValue('width')

      let percent = (parseFloat(hierarchyWidth) / parseFloat(childWidth))
      console.log(hierarchyWidth,childWidth, percent);
      // child.style.width = hierarchyWidth+'px'
      // child.style.transform = "scale("+percent+")";

  </script>

    <?php
  }
 ?>
