<?php /* Template Name: Section */ ?>
<?php
  get_header();
  $page_permalink = get_permalink();
?>

<?php
  if (have_rows('subsections')) :
    $overview_title = get_field('overview_title');
    $healthy_title = get_field('healthy_title');
    $unhealthy_title = get_field('unhealthy_title');
    $prognosis_title = get_field('prognosis_title');
    $stories_title = get_field('stories_title');
    $data_title = get_field('data_title');
?>
  <div class="section">
    <div class="section__wrapper wrapper js-section-wrapper">

      <div class="section__menu js-sidebar">
        <div class="sidebar-wrapper js-sidebar-wrapper">
          <button class="sidebar__active js-sidebar-active"><?php the_field('overview_title'); ?></button>

          <ul class="sidebar js-sidebar-nav">
            <li class="sidebar__item"><a href="#<?php echo stringToId(get_field('overview_title')); ?>" class="sidebar__link js-sidebar-link js-scroll-to is-active"><?php the_field('overview_title'); ?></a></li>
            <li class="sidebar__item"><a href="#<?php echo stringToId(get_field('healthy_title')); ?>" class="sidebar__link js-sidebar-link js-scroll-to"><?php the_field('healthy_title'); ?></a></li>
            <li class="sidebar__item"><a href="#<?php echo stringToId(get_field('unhealthy_title')); ?>" class="sidebar__link js-sidebar-link js-scroll-to"><?php the_field('unhealthy_title'); ?></a></li>
            <li class="sidebar__item"><a href="#<?php echo stringToId(get_field('prognosis_title')); ?>" class="sidebar__link js-sidebar-link js-scroll-to"><?php the_field('prognosis_title'); ?></a></li>
            <li class="sidebar__item"><a href="#<?php echo stringToId(get_field('stories_title')); ?>" class="sidebar__link js-sidebar-link js-scroll-to"><?php the_field('stories_title'); ?></a></li>
            <li class="sidebar__item"><a href="#<?php echo stringToId(get_field('data_title')); ?>" class="sidebar__link js-sidebar-link js-scroll-to"><?php the_field('data_title'); ?></a></li>
          </ul>

          <button class="sidebar__toggle js-sidebar-toggle">Toggle navigation</button>
        </div>
      </div>

      <div class="section__header">
        <div id="<?php echo stringToId($overview_title); ?>" class="section__block js-sidebar-block" data-title="<?php echo $overview_title; ?>">
          <div class="wysiwyg">
            <?php the_field('section_overview'); ?>
          </div>
          <a class="btn js-scroll-to" href="#<?php echo stringToId(get_field('data_title')); ?>"><?php the_field('skip_to_data_button_text'); ?></a>
        </div>
        <div id="<?php echo stringToId($healthy_title); ?>" class="section__block wysiwyg js-sidebar-block" data-title="<?php echo $healthy_title;; ?>">
          <h3 class="section__subtitle"><?php echo $healthy_title; ?></h3>
          <?php the_field('section_healthy'); ?>
        </div>
        <div id="<?php echo stringToId($unhealthy_title); ?>" class="section__block wysiwyg js-sidebar-block" data-title="<?php echo $unhealthy_title;; ?>">
          <h3 class="section__subtitle"><?php echo $unhealthy_title; ?></h3>
          <?php the_field('section_unhealthy'); ?>
        </div>
        <div id="<?php echo stringToId($prognosis_title); ?>" class="section__block wysiwyg js-sidebar-block" data-title="<?php echo $prognosis_title;; ?>">
          <h3 class="section__subtitle"><?php echo $prognosis_title; ?></h3>
          <?php the_field('section_prognosis'); ?>
        </div>
      </div>

    </div>
  </div>

  <?php
    $featured_stories = get_field('related_stories');

    if ($featured_stories) :
  ?>
  <div id="<?php echo stringToId($stories_title); ?>" class="js-sidebar-block" data-title="<?php echo $stories_title; ?>">
    <div class="section__divider">
      <div class="wrapper">
        <h2 class="section__title"><?php echo $stories_title; ?></h2>
      </div>
    </div>

    <div class="card-wrapper card-wrapper--dark">
      <div class="wrapper">
        <?php foreach($featured_stories as $post) : ?>
          <?php
            setup_postdata($post);
            $thumbnail = get_the_post_thumbnail_url($post, 'large');
          ?>
          <div class="card card--block">
            <?php if ($thumbnail) : ?>
              <div class="card__thumb" style="background-image: url(<?php echo $thumbnail; ?>)"></div>
            <?php endif; ?>
            <div class="card__cell card__cell--feature card__cell--last">
              <a class="card__subtitle" href="<?php echo get_permalink(); ?>"><?php echo the_title(); ?></a>
              <p class="card__excerpt"><?php the_field('page_subtitle'); ?></p>
              <a class="card__cta" href="<?php echo get_permalink(); ?>"><?php the_field('story_cta'); ?> <?php get_template_part('assets/icons/icon', 'arrow-right.svg'); ?></a>
            </div>
          </div>
        <?php endforeach; ?>
        <?php wp_reset_postdata(); ?></div>
    </div>
  </div>
  <?php endif; ?>

  <div id="<?php echo stringToId($data_title); ?>" class="section__divider js-sidebar-block" data-title="<?php echo $data_title; ?>">
    <div class="wrapper">
      <h2 class="section__title"><?php echo $data_title; ?></h2>
    </div>
  </div>

  <div class="subsection">

  <?php
    $subsection_index = 0;
    while (have_rows('subsections')) : the_row();
      if (get_row_layout() == 'subsection_title') :
        $subsection_title = get_sub_field('title');
        $subsection_ID = strtolower(preg_replace('#[ -]+#', '-', $subsection_title));
  ?>

  <h3 id="<?php echo $subsection_ID; ?>" class="subsection__header"><?php echo $subsection_title; ?></h3>

  <?php
    elseif (get_row_layout() == 'subsection_chart') :
      $chart_title = get_sub_field('title');
      $chart_id = strtolower(preg_replace('#[ -]+#', '-', $chart_title));
      $chart_subtitle = get_sub_field('subtitle');
      $chart_text = get_sub_field('text');
  ?>

  <?php if ($chart_title) : ?>
    <h4 id="<?php echo $chart_id; ?>" class="subsection__chart-title"><?php echo $chart_title; ?></h4>
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
      <?php /* get_template_part('content', 'chart'); */ ?>
      <?php include(locate_template('content-chart.php')); ?>
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

<?php
  endif;
?>

<?php if ( comments_open() || get_comments_number() ) : ?>
  <div class="comments-wrapper wrapper">
    <?php comments_template(); ?>
  </div>
<?php endif; ?>

<?php get_footer(); ?>