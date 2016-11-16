<?php /* Template Name: Section */ ?>
<?php get_header(); ?>

<?php
  if (have_rows('subsections')) :
?>

<div class="subsection">

<?php
    while (have_rows('subsections')) : the_row();
      if (get_row_layout() == 'subsection_title') :
?>

  <div class="subsection__header">
    <div class="wrapper">
      <h3><?php the_sub_field('title'); ?></h3>
    </div>
  </div>

<?php
      elseif (get_row_layout() == 'subsection_text') :
?>

  <div class="subsection__main wrapper">
    <div class="subsection__text-block">
      <p class="subsection__text-strong"><?php the_sub_field('bold_text'); ?></p>

      <div class="subsection__text">
        <?php the_sub_field('text'); ?>
      </div>
    </div>
  </div>

<?php
      elseif (get_row_layout() == 'subsection_chart') :
        $chart_object = get_sub_field('chart');
?>

<?php
  if ($chart_object) :
    $post = $chart_object;
    setup_postdata($post);
?>
  <div class="subsection__main wrapper">
    <?php get_template_part('content', 'chart'); ?>
  </div>
  <?php wp_reset_postdata(); ?>
<?php endif; ?>

<?php
      endif;

    endwhile;
?>

</div>

<?php
  endif;
?>

<?php get_footer(); ?>