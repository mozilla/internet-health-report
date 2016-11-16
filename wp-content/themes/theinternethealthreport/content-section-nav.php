<?php
  $section_args = array(
    'post_type' => 'page',
    'order' => ASC,
    'meta_query' => array(
      array(
        'key' => '_wp_page_template',
        'value' => 'page_section.php'
      )
    )
  );
  $section_query = new WP_Query($section_args);
?>

<?php if ( $section_query->have_posts() ) : ?>
  <div class="card-wrapper wrapper">

  <?php
    while ( $section_query->have_posts() ) : $section_query->the_post();
    $thumbnail_id = get_post_thumbnail_id();
    $img_src = wp_get_attachment_image_src($thumbnail_id, 'large');
    $stories_objects = get_field('related_stories');
  ?>

  <div class="card">
    <div class="card__cell">
      <a href="<?php echo get_permalink(); ?>" class="card__link-wrapper">
        <h4 class="card__subtitle"><?php the_title(); ?></h4>
        <h3 class="card__title"><?php the_field('section_subtitle'); ?></h3>
      </a>
    </div>

    <?php if ($stories_objects) : ?>
      <div class="card__cell card__cell--last">
        <h4 class="card__featured"><?php the_field('stories_title'); ?></h4>

        <?php foreach($stories_objects as $post) : ?>
          <?php setup_postdata($post); ?>
          <a class="card__link" href="<?php echo get_permalink(); ?>"><?php echo the_title(); ?></a>
        <?php endforeach; ?>
        <?php wp_reset_postdata(); ?>
      </div>
    <?php endif; ?>
  </div>

  <?php endwhile; ?>

  </div>
<?php endif; ?>