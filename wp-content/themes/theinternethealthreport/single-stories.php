<?php /* Template Name: Article */ ?>
<?php get_header(); ?>
<?php
  $title = get_the_title();
  $subtitle = get_field('subtitle');
  $permalink = get_permalink();
?>

<article class="article story wrapper">
  <div class="article__copy">
    <div class="story__header">
      <h1><?php echo $title; ?></h1>

      <?php if ($subtitle) : ?>
        <p><?php echo $subtitle; ?></p>
      <?php endif; ?>

      <div class="story__share share">
        <a href="<?php echo get_facebook_share_url($permalink); ?>" target="_blank" class="share__btn btn btn--outline js-social-share">Share</a>
        <a href="<?php echo get_twitter_share_url($permalink, $title); ?>" target="_blank" class="share__btn btn btn--outline js-social-share">Tweet</a>
        <a href="mailto:?subject=<?php echo $title; ?>&body=<?php echo $permalink; ?>" class="share__btn btn btn--outline">Email</a>
      </div>
    </div>
  </div>

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

  <div class="article__copy">
    <div class="article__share share">
      <a href="<?php echo get_facebook_share_url($permalink); ?>" target="_blank" class="share__btn btn btn--outline js-social-share">Share</a>
      <a href="<?php echo get_twitter_share_url($permalink, $title); ?>" target="_blank" class="share__btn btn btn--outline js-social-share">Tweet</a>
      <a href="mailto:?subject=<?php echo esc_url($title); ?>&body=<?php echo esc_url($permalink); ?>" class="share__btn btn btn--outline">Email</a>
    </div>
  </div>
</article>

<?php get_template_part('content', 'section-nav'); ?>

<?php get_footer(); ?>