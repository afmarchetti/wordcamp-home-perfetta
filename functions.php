<?php

/*  Register sidebars
/* ------------------------------------ */
if ( ! function_exists( 'md_sidebars' ) ) {

  function md_sidebars()	{

    register_sidebar(array(
      'name' => esc_html__( 'Home Intro', 'isbase' ),
      'id' => 'home_intro',
      'description' => esc_html__( 'Home intro.', 'isbase' ),
      'before_widget' => '<div id="%1$s" class="intro__item clearfix %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h3>',
      'after_title' => '</h3>'
    ));

    register_sidebar(array(
      'name' => esc_html__( 'Home Focuses', 'isbase' ),
      'id' => 'home_focuses',
      'description' => esc_html__( 'Home focues.', 'isbase' ),
      'before_widget' => '<div id="%1$s" class="focus clearfix %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h3>',
      'after_title' => '</h3>'
    ));

    register_sidebar(array(
      'name' => esc_html__( 'Home Outro', 'isbase' ),
      'id' => 'home_outro',
      'description' => esc_html__( 'Home outro.', 'isbase' ),
      'before_widget' => '<div id="%1$s" class="outro__item clearfix %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h3>',
      'after_title' => '</h3>'
    ));

  }

}
add_action( 'widgets_init', 'md_sidebars' );

// WordPress function to get raw widget data for all of the widgets in a given sidebar
// https://gist.github.com/kingkool68/3418186

function md_get_raw_widgets_data( $sidebar_slug ) {
  global $wp_registered_sidebars, $wp_registered_widgets;

  // Holds the final data to return
  $output = array();

  // Loop over all of the registered sidebars looking for the one with the same name as $sidebar_name
  $sidebar_id = false;
  foreach ( $wp_registered_sidebars as $sidebar ) {

    if ( $sidebar['id'] == $sidebar_slug ) { // condition to grab the sidebar that i want
      // We now have the Sidebar ID, we can stop our loop and continue.
      $sidebar_id = $sidebar['id'];
      break;
    }
  }

  if ( ! $sidebar_id ) {
    // There is no sidebar registered with the name provided.
    return $output;
  }

  // A nested array in the format $sidebar_id => array( 'widget_id-1', 'widget_id-2' ... );
  $sidebars_widgets = wp_get_sidebars_widgets();
  $widget_ids = $sidebars_widgets[ $sidebar_id ];

  if ( ! $widget_ids ) {
    // Without proper widget_ids we can't continue.
    return array();
  }

  // Loop over each widget_id so we can fetch the data out of the wp_options table.
  foreach ( $widget_ids as $id ) {

    // The name of the option in the database is the name of the widget class. (slug of the widget)
    $option_name = $wp_registered_widgets[ $id ]['callback'][0]->option_name;
    $widget_data = get_option( $option_name );

    // Widget data is stored as an associative array. To get the right data we need to get the right key which is stored in $wp_registered_widgets
    $key = $wp_registered_widgets[ $id ]['params'][0]['number'];

    // Retrive single widget data.
    $widget_array = $widget_data[ $key ]; //// --> array with title and content of the single widget

    // Add the widget id and type to the end of the widget array
    $widget_array += array('unique_id' => 'widget-'.$key);
    $widget_array += array('type' => $option_name);

    // Add the widget data on to the end of the output array.
    $output[] = (object) $widget_array;

  }
  return $output;
}

// width customizer
function md_customizer_style() {
	wp_add_inline_style( 'customize-controls', '
	.wp-full-overlay-sidebar { width: 420px }
	.wp-full-overlay.expanded { margin-left: 420px }
	.customize-control-widget_form .widget-top{opacity: 1!important}');
}
add_action( 'customize_controls_enqueue_scripts', 'md_customizer_style'); ?>
