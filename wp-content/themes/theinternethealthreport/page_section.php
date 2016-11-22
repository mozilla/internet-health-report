<?php /* Template Name: Section */ ?>
<?php get_header(); ?>

<?php
  if (have_rows('subsections')) :
?>
  <div class="section">
    <div class="wrapper">
      <ul class="subsection-menu">
        <?php
          $subsection_index = 0;
          while (have_rows('subsections')) : the_row();
            if (get_row_layout() == 'subsection_title') :
              $subsection_index = $subsection_index + 1;
              $subsection_index_pad = sprintf("%02d", $subsection_index);
              $subsection_title = get_sub_field('title');
              $subsection_ID = strtolower(preg_replace('#[ -]+#', '-', $subsection_title));
        ?>
        <li class="subsection-menu__item">
          <a class="subsection-menu__link" href="<?php echo '#' . $subsection_ID; ?>"><?php echo $subsection_index_pad . ' ' . $subsection_title; ?></a>
        </li>
        <?php
            endif;
          endwhile;
        ?>
      </ul>
    </div>
  </div>

  <div class="subsection wrapper">
    <div class="subsection__wrapper">

    <?php
      $subsection_index = 0;
      while (have_rows('subsections')) : the_row();
        if (get_row_layout() == 'subsection_title') :
          $subsection_index = $subsection_index + 1;
          $subsection_index_pad = sprintf("%02d", $subsection_index);
          $subsection_title = get_sub_field('title');
          $subsection_ID = strtolower(preg_replace('#[ -]+#', '-', $subsection_title));
    ?>

    <h3 id="<?php echo $subsection_ID; ?>" class="subsection__header"><?php echo $subsection_index_pad . ' ' . $subsection_title; ?></h3>

    <?php
      elseif (get_row_layout() == 'subsection_chart') :
        $chart_title = get_sub_field('title');
        $chart_subtitle = get_sub_field('subtitle');
        $chart_text = get_sub_field('text');
    ?>

    <?php if ($chart_title) : ?>
      <h4 class="subsection__chart-title"><?php echo $chart_title; ?></h4>
    <?php endif; ?>

    <?php if ($chart_subtitle) : ?>
      <div class="subsection__row">
        <p class="subsection__subtitle"><?php echo $chart_subtitle; ?></p>

        <div class="subsection__text">
          <?php echo $chart_text; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php
     if (have_rows('charts')) :
      while (have_rows('charts')) : the_row();
        $chart_object = get_sub_field('chart');
    ?>
      <?php
        if ($chart_object) :
          $post = $chart_object;
          setup_postdata($post);
      ?>
      <div class="subsection__chart">
        <?php get_template_part('content', 'chart'); ?>
      </div>
      <?php wp_reset_postdata(); ?>
      <?php endif; ?>
    <?php
      endwhile;
    endif;
    ?>

  <?php
      endif;
    endwhile;
  ?>

  </div>
</div>

<?php
  endif;
?>

<?php
// If comments are open or we have at least one comment, load up the comment template.
if ( comments_open() || get_comments_number() ) {
  comments_template();
}
?>

<?php get_footer(); ?>