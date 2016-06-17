<?php

/**
  * The main template file for the related content section
  *
  * @since 1.0.0
  * @package America_Related_Content
  */
?>

<?php foreach( $related_posts as $rp ) : ?>
  <div class="related-content">
    <div class="related-post">
      <?php echo get_the_post_thumbnail( $rp-> ID, 'medium_large' ); ?>
      <?php printf( '<h3 class="related-entry-title"><a href="%s" rel="bookmark">%s</a></h3>', esc_url( get_permalink( $rp->ID ) ), get_the_title( $rp->ID ) ); ?>
    </div>
  </div>
<?php endforeach; ?>

<?php var_dump( $related_posts ); ?>
