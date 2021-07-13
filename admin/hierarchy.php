<?php
//header
//add gojs script to heaeder
add_action( 'init', 'add_gojs_to_header' );
function add_gojs_to_header() {

 wp_register_script( 'cc85-gojs', 'https://unpkg.com/gojs/release/go-debug.js', array(), '1.0', 'all');
}
  add_action('wp_enqueue_scripts', 'enqueue_gojs_script');

  function enqueue_gojs_script(){
  wp_enqueue_script( 'cc85-gojs' );
  }

  //add shortcode

  add_shortcode( 'cc85_hierarchy', 'cc85_hierarchy_code' );

  function cc85_hierarchy_code(){
    if ( is_admin()){
    	return;
    }
    ?>
    <div id="myDiagramDiv" style="width:100%; height:100vh; background-color: #DAE4E4;">

    </div>
    <?php

    //add js in the footer
    add_action( 'wp_footer', 'cc85_js_code_to_hierarchy' );
    function cc85_js_code_to_hierarchy(){
      ?>

      <script>
      $( document ).ready(function() {
        //////safe////
        var $ = go.GraphObject.make;

        var myDiagram =
          $(go.Diagram, "myDiagramDiv",
            {
              "undoManager.isEnabled": true,
              layout: $(go.TreeLayout,
                        { angle: 90, layerSpacing: 35 })
            });

        myDiagram.nodeTemplate =
          $(go.Node, "Horizontal",
            { background: "#dedede" },
            $(go.TextBlock, "Default Text",
              { margin: 10, stroke: "white", font: "10px sans-serif" },
              new go.Binding("text", "name"))
          );

        // define a Link template that routes orthogonally, with no arrowhead
        myDiagram.linkTemplate =
          $(go.Link,
            { routing: go.Link.Orthogonal, corner: 5 },
            $(go.Shape, // the link's path shape
              { strokeWidth: 3, stroke: "#555" })
          );

        // it's best to declare all templates before assigning the model
        myDiagram.model = new go.TreeModel(
          [
            { key: "1",              name: "Don Meow"  },
            { key: "2", parent: "1", name: "Demeter"   },
            { key: "3", parent: "1", name: "Copricat"  },
            { key: "4", parent: "3", name: "Jellylorum" },
            { key: "5", parent: "3", name: "Alonzo"    },
            { key: "6", parent: "2", name: "Munkustrap" }
          ]);
//////safe////

    });


      </script>

      <?php
    }
  }
 ?>
