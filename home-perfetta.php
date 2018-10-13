<?php
/* functions.php
---------------------------------------------------------------------------- */

/*  Register sidebars
/* ------------------------------------ */
if ( ! function_exists( 'isbase_sidebars' ) ) {

  function isbase_sidebars()	{

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
add_action( 'widgets_init', 'isbase_sidebars' );

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
add_action( 'customize_controls_enqueue_scripts', 'md_customizer_style');

/* END functions.php
---------------------------------------------------------------------------- */

/* home-page.php
---------------------------------------------------------------------------- */

?>

<?php
/**
* Template for displaying Slider with Post
*
* @package isbase
*/
/*

Template Name: Home Page

*/
?>
<?php get_header(); ?>

<?php if (have_posts()) :?><?php while(have_posts()) : the_post(); ?>

  <?php
  /* Image */
  $image_url =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), '' );
  ?>
  <div class="cover" style="background: url(<?php echo  $image_url[0]; ?>) center center; background-size: cover;">
    <div class="cover__caption">
        <?php the_content();?>
    </div>
  </div>

<?php endwhile; ?>
<?php else : ?>

  <p><?php esc_html_e('Sorry, no posts matched your criteria.', 'isbase'); ?></p>

<?php endif; ?>


<?php /*  Intro */
$raw_widgets = md_get_raw_widgets_data('home_intro');
?>

<div class="container intro">

  <?php foreach ( $raw_widgets as $widget ) { ?>

    <div class="intro__item <?php echo $widget->type;  ?>" id="<?php echo $widget->unique_id; ?>">
      <h3><?php echo $widget->title; ?></h3>
      <p><?php echo $widget->text; ?></p>
    </div>

  <?php } ?>

</div>


<div class="container focuses">

  <?php
  /* Focus */
  $raw_widgets = md_get_raw_widgets_data('home_focuses');

  foreach ( $raw_widgets as $widget ) { ?>

    <div class="focus <?php echo $widget->type; ?>" id="<?php echo $widget->unique_id; ?>">
      <?php if ($widget->title) : ?>
        <h3><?php echo $widget->title; ?></h3>
      <?php endif; ?>

      <?php if ($widget->text) : ?>
        <p><?php echo $widget->text; ?></p>
      <?php endif; ?>

      <?php if ($widget->url) : ?>
        <img src="<?php echo $widget->url; ?>" alt="" class="img-res">
      <?php endif; ?>

      <?php if ($widget->caption) : ?>
        <p><?php echo $widget->caption; ?></p>
      <?php endif; ?>

    </div>

  <?php } /* End Focus */ ?>

</div>

<?php
/* Outro */
$raw_widgets = md_get_raw_widgets_data('home_outro');
?>

<div class="container outro">

  <?php foreach ( $raw_widgets as $widget ) { ?>

    <div class="outro__item <?php echo $widget->type; ?>">

      <?php if ($widget->url) : ?>
        <img src="<?php echo $widget->url; ?>" alt="<?php echo $widget->title; ?>" >
      <?php endif; ?>

      <h3><?php echo $widget->title; ?></h3>
      <p><?php echo $widget->text; ?></p>

      <?php if ($widget->caption) : ?>
        <p><?php echo $widget->caption; ?></p>
      <?php endif;  ?>

      <?php
      /* Gallery */
      if ($widget->ids) : ?>
      <div class="gallery-widget">

        <?php foreach ( $widget->ids as $id ) { ?>
          <div class="gallery-widget__item">
            <?php
            $image_url =  wp_get_attachment_image_src( $id, 'thumbnail' );
            $image_data = get_posts(array('p' => $id, 'post_type' => 'attachment'))
            ?>
            <img src="<?php echo $image_url[0]; ?>">
            <?php if($image_data) :?>
              <br/><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>
              <p><?php echo $image_data[0]->post_excerpt; ?></p>
            <?php endif; ?>
          </div>
        <?php } ?>

      </div>
    <?php endif; /* End Gallery */ ?>

  </div>

<?php } /* End Outro */ ?>

</div>


<?php /* Activate Live Preview Widgets if customizer open */

if ( is_customize_preview() ) {
  apply_filters( 'dynamic_sidebar_has_widgets', true , 'home_pre_intro' );
  apply_filters( 'dynamic_sidebar_has_widgets', true , 'home_intro' );
  apply_filters( 'dynamic_sidebar_has_widgets', true , 'home_focuses' );
  apply_filters( 'dynamic_sidebar_has_widgets', true , 'home_outro' );

} ?>

<?php get_footer(); ?>

<?php

/* END home-page.php
---------------------------------------------------------------------------- */

/* END style.css
---------------------------------------------------------------------------- */
?>

<style>
.intro{
  display: flex;
}

.intro__item{
  flex-basis:0;flex-grow: 1;
  padding:0 20px 0 25px;
  border-left: 7px solid #ddd;
  margin: 40px 0;
}

.intro__item h3,
.intro__item p{margin-bottom: 0;}

/* Image widget */
.image-card{ height: 100%; justify-content: center;align-items: center;display: flex;}
.image-desc{margin: 0;color:#fff; text-transform: uppercase;font-weight: bold;text-align: center;}
.image-text{padding-top: 5px;}

.widget_media_image img{width:100%;}
.home .widget_media_image h3{display: none;}

/* Focus */
.focuses{display: flex;flex-flow: row wrap;}
.focus{ padding:60px 30px;width: 50%;}
.focus h3{ font-size: 46px;}

/* Gallery */
.home .widget_media_gallery{width: 100%;}
.home .widget_media_gallery{text-align: center;}
.home .widget_media_gallery h3{text-align: center;margin-bottom: 45px;}


.testimonials,
.gallery-widget{display: flex;justify-content: space-between;}
.testimonial img,
.gallery-widget__item img{border-radius: 9999px; width: 100%; max-width: 150px; margin-bottom: 20px;}
.testimonial,
.gallery-widget__item {text-align: center;position: relative;}
.gallery-widget__item:after {
    position: absolute;
    content: '\201C';
    font-size: 130px;
    color: #f65046;
    top: 37px;
    left: 40px;
    font-family: georgia;
}

.outro__item.widget_text a{ display: block;background: #f65046;padding: 20px; color:#fff; text-transform: uppercase;border-radius: 4px; font-size: 18px;font-weight: bold;margin: 40px 0 150px 0;}

.gallery-widget__item {padding: 0 20px;}

.outro{display:flex;flex-wrap: wrap;}
.outro__item{padding: 40px 40px 0 40px;text-align: center;width: 100%;}
.outro__item h3{font-size: 46px;}
.outro__item.widget_media_image{padding: 20px; text-align: center;}
.outro__item.widget_media_image h3{font-size: 26px;margin-bottom: 0;margin-top: 20px;}

.outro .widget_media_image{width:33.33%;}

</style>
