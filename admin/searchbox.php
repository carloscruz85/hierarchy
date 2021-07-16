<?php
//header
//add icons to header and style
add_action( 'init', 'add_icons_to_header' );
function add_icons_to_header() {


// wp_register_style( 'new_style', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
 wp_register_style( 'cc85-awesome-icons', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '1.0', 'all');
}
  // use the registered jquery and style above
  add_action('wp_enqueue_scripts', 'enqueue_style');

  function enqueue_style(){
  // wp_enqueue_style( 'new_style' );
  wp_enqueue_style( 'cc85-awesome-icons' );
  }

// shortcode
add_shortcode( 'cc85_search_box', 'cc85_search_box_function' );

function cc85_search_box_function(){
  if ( is_admin()){
    return;
  }
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
    array_push($positions,
      array(
        'name'        => get_the_title(),
        'ext'         => get_post_meta($id,'ext',true),
        'phone'       => get_post_meta($id,'phone',true),
        'email'       => get_post_meta($id,'email', true),
        'position'    => $position[0]->name,
        'main_unity'  => get_main_unity($position[0])
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
    <input type="text" name="" value="" placeholder="Buscar" id="cc85-positions-input" class="cc85searchinput">
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

    //custom format
    const format_data_contact = (contact) => {

      let code = `<p><i class="fa fa-user"></i> ${contact.name}</p>`;

      let phone = ''
      if( contact.phone.length != 0 ) phone = `<p><i class="fa fa-phone"></i> ${contact.phone}</p>`

      let ext = ''
      if( contact.ext.length != 0) ext = `<p><i class="fa fa-phone-square"></i> ${contact.ext}</p>`

      let position = ''
      if( contact.position != null || contact.main_unity != null  ) position = `<p><i class="fa fa-vcard"></i> ${contact.position} / ${contact.main_unity}</p>`

      let mail = ``
      if( contact.email.length != 0) mail = `<p><i class="fa fa-envelope"></i> ${contact.email}</p>`



      return `<div class="cc85-position-card">${code} ${position} ${ext} ${phone}  ${mail}</div>`;
    }

    //slugify
    const string_to_slug = (str) => {
    str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();

    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to   = "aaaaeeeeiiiioooouuuunc------";
    for (var i=0, l=from.length ; i<l ; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

    return str;
  }

    //trigger

    data.map( (contact, i) => {
      let p = document.createElement("p")
      p.innerHTML = format_data_contact(contact);
      log.append(p)
    })

    //add listener

    input.addEventListener('input', updateValue);

    function updateValue(e) {
      log.innerHTML = '';
      data.filter( contact => {
        if(
          string_to_slug(contact.name).search(string_to_slug(input.value)) !== -1 ){
          return contact
        }
      } ).map( (contact, i) => {
        let p = document.createElement("p")
        p.innerHTML = format_data_contact(contact);
        log.append(p)
      })
    }



    </script>

    <?php
  }
}

function get_main_unity($term){
  $value = get_term_meta( $term->term_id, 'main_unity_check', true );
  if( $term->parent == 0 ) return $term->name;
  if( $value == 'true' ) return $term->name;
  return get_main_unity(get_term($term->parent, 'position_tax'));
}


 ?>
