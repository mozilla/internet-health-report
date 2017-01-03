<?php
  $title = get_the_title();
  $permalink = get_permalink();
  $post_type = get_post_type();
?>

<article class="article story wrapper js-article">
  <div class="article__main js-article-main">
    <div class="share js-share">
      <a href="<?php echo get_facebook_share_url($permalink); ?>" target="_blank" class="share__btn js-social-share"><span class="share__text">Share</span><span class="share__icon"><?php get_template_part('assets/icons/icon', 'facebook.svg'); ?></span></a><!--

      --><a href="<?php echo get_twitter_share_url($permalink, $title); ?>" target="_blank" class="share__btn js-social-share"><span class="share__text">Tweet</span><span class="share__icon"><?php get_template_part('assets/icons/icon', 'twitter.svg'); ?></span></a>
    </div>

    <div class="article__metadata">
      <p class="article__date"><?php echo get_the_date('F Y'); ?></p>
      <p class="article__author"><?php the_field('author'); ?></p>
    </div>

    <?php if (have_rows('story')) : while (have_rows('story')) : the_row(); ?>

      <?php if (get_row_layout() == 'story_text') : ?>

        <div class="article__copy wysiwyg">
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
  </div>
</article>

<?php if ($post_type == 'stories') : ?>
  <?php get_template_part('content', 'section-nav'); ?>
<?php else : ?>
  <?php if ( comments_open() || get_comments_number() ) : ?>
    <div class="comments-wrapper wrapper">
      <?php comments_template(); ?>
    </div>
  <?php endif; ?>
<?php endif; ?>