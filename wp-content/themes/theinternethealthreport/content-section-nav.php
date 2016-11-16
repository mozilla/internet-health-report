<?php
  $section_args = array(
    'post_type' => 'page',
    'post__not_in' => array(5, 7, 65),
    'order' => ASC
  );
  $section_query = new WP_Query($section_args);
?>

<?php if ( $section_query->have_posts() ) : ?>
  <div class="wrapper">

  <?php
    while ( $section_query->have_posts() ) : $section_query->the_post();
    $thumbnail_id = get_post_thumbnail_id();
    $img_src = wp_get_attachment_image_src($thumbnail_id, 'large');
    $stories_objects = get_field('related_stories');
  ?>

  <div class="table u-space--l">
    <div class="table__cell">
      <a href="<?php echo get_permalink(); ?>" class="table__link">
        <div class="media media--medium">
          <div class="media__figure">
            <img src="<?php echo $img_src[0]; ?>" alt="">
          </div>

          <div class="media__body">
            <h4><?php the_title(); ?></h4>
            <h3><?php the_field('section_subtitle'); ?></h3>
            <p class="media__link media__link--fixed"><?php the_field('link_cta'); ?></p>
          </div>
        </div>
      </a>
    </div>

    <?php if ($stories_objects) : ?>
      <div class="table__cell table__cell--last">
        <h4><?php the_field('stories_title'); ?></h4>

        <?php foreach($stories_objects as $post) : ?>
          <?php setup_postdata($post); ?>
          <a class="media__link" href="<?php echo get_permalink(); ?>"><?php echo the_title(); ?></a>
        <?php endforeach; ?>
        <?php wp_reset_postdata(); ?>
      </div>
    <?php endif; ?>
  </div>

  <?php endwhile; ?>

  </div>
<?php endif; ?>