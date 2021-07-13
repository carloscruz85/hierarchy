<?php
//header
//add icons to header and style
add_action( 'init', 'add_icons_to_header' );
function add_icons_to_header() {


wp_register_style( 'new_style', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
 wp_register_style( 'cc85-awesome-icons', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '1.0', 'all');
}
  // use the registered jquery and style above
  add_action('wp_enqueue_scripts', 'enqueue_style');

  function enqueue_style(){
  wp_enqueue_style( 'new_style' );
  wp_enqueue_style( 'cc85-awesome-icons' );
  }

// shortcode
add_shortcode( 'cc85_search_box', 'cc85_search_box_function' );

function cc85_search_box_function(){

  //get all positions
  $args = array(
    'post_type' => 'positions',
    'post_status' => array('publish'),
    'posts_per_page' => -1,
  );

  global $positions;
  $positions = array();
  $the_query = new WP_Query( $args );
  if ( $the_query->have_posts() ) :
  while ( $the_query->have_posts() ) : $the_query->the_post();
    $id = get_the_ID();
    $position = get_the_terms($id, 'position_tax');
    $position_typr = get_the_terms($id, 'position_type_tax');
    array_push($positions,
      array(
        'name' => get_the_title(),
        'ext' => get_post_meta($id,'ext',true),
        'email' => get_post_meta($id,'email', true),
        'position' => $position[0]->name,
        'position_type' => $position_typr[0]->name

      )
  );
  endwhile;
  endif;
  wp_reset_postdata();
  global $json;
  $json = json_encode($positions);






  //add input to search
  ?>
  <div class="cc85-positions-container">
    <input type="text" name="" value="" id="cc85-positions-input" class="cc85searchinput">
    <div class="" id="resultados">

    </div>
  </div>
  <?php


  //add js in the footer
  add_action( 'wp_footer', 'cc85_js_code_to_positions' );
  function cc85_js_code_to_positions(){
    global $json;
    ?>

    <script>


    const data = <?php echo $json ?>;
    const input = document.querySelector('input.cc85searchinput');
    const log = document.getElementById('resultados');



    input.addEventListener('input', updateValue);

    function updateValue(e) {
      log.innerHTML = '';
      data.filter( contact => {
        if( contact.name.toLowerCase().search(input.value.toLowerCase()) !== -1 ){
          return contact
        }
      } ).map( (contact, i) => {
        let p = document.createElement("p")
        p.innerHTML = `<div class="cc85-position-card">
          <p><i class="fa fa-address-book"></i> ${contact.name}</p>
          <p> ${contact.position_type} / ${contact.position}</p>
          <p><i class="fa fa-phone-square"></i> ${contact.ext}</p>
          <p><i class="fa fa-envelope"></i> ${contact.email}</p>
        </div>`;
        log.append(p)
      })
    }

    </script>

    <?php
  }
}
 ?>
