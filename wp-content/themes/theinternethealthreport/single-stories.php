<?php /* Template Name: Article */ ?>
<?php get_header(); ?>
<?php
  $title = get_the_title();
  $subtitle = get_field('subtitle');
  $permalink = get_permalink();
?>

<article class="article story wrapper">
  <?php if (have_rows('story')) : while (have_rows('story')) : the_row(); ?>

    <?php if (get_row_layout() == 'story_text') : ?>

      <div class="article__copy">
        <?php the_sub_field('text'); ?>
      </div>

    <?php elseif (get_row_layout() == 'story_image') : ?>
      <?php
        $image = get_sub_field('image');
        $image_url = $image['url'];
        $image_caption = $image['caption'];
      ?>

      <figure class="article__figure">
        <img src="<?php echo $image_url ?>" alt="">
        <?php if ($image_caption) : ?>
          <figcaption class="article__figcaption"><?php echo $image_caption; ?></figcaption>
        <?php endif; ?>
      </figure>

    <?php endif; ?>

  <?php endwhile; endif; ?>

  <div class="share js-share">
    <a href="<?php echo get_facebook_share_url($permalink); ?>" target="_blank" class="share__btn js-social-share"><span class="share__text">Share</span><span class="share__icon"><?php get_template_part('assets/icons/icon', 'facebook.svg'); ?></span></a><!--

    --><a href="<?php echo get_twitter_share_url($permalink, $title); ?>" target="_blank" class="share__btn js-social-share"><span class="share__text">Tweet</span><span class="share__icon"><?php get_template_part('assets/icons/icon', 'twitter.svg'); ?></span></a>

    <!-- <a href="mailto:?subject=<?php echo esc_url($title); ?>&body=<?php echo esc_url($permalink); ?>" class="share__btn btn btn--outline">Email</a> -->
  </div>
</article>

<?php get_template_part('content', 'section-nav'); ?>

<?php get_footer(); ?>