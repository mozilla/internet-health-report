<?php get_header(); ?>

<div class="wrapper">
  <div class="intro">
    <p class="intro__text"><?php the_field('intro_text'); ?></p>
    <a class="intro__link" href="<?php the_field('intro_page_link'); ?>"><?php the_field('intro_page_link_text'); ?> <?php echo get_template_part('assets/icons/icon', 'link.svg'); ?></a>
  </div>
</div>

<?php get_template_part('content', 'section-nav'); ?>

<?php get_footer(); ?>