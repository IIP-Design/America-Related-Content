<?php

/**
  * The main template file for the related content section
  *
  * @since 1.0.0
  * @package America_Related_Content
  */
?>

<section class="related-content">
  <h2 class="related-content-title">
    <?php while ( have_rows( 'america_related_content' ) ) : the_row();
      echo get_sub_field( 'related_content_block_title' );
    endwhile; ?>
  </h2>
  <div class="related-post-container">
  <?php foreach( $related_posts as $related_post ) : ?>
    <div class="related-post">
      <?php printf( '<a href="%s" rel="bookmark">%s</a>', esc_url( get_permalink( $related_post->ID ) ), get_the_post_thumbnail( $related_post-> ID, 'related_thumb' ) ); ?>
      <?php printf( '<h3 class="related-entry-title"><a href="%s" rel="bookmark">%s</a></h3>', esc_url( get_permalink( $related_post->ID ) ), get_the_title( $related_post->ID ) ); ?>
    </div>
  <?php endforeach; ?>
  </div>

</section>

<?php // var_dump( $related_posts ); ?>
