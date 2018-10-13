
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
