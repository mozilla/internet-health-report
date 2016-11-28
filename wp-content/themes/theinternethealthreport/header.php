<?php get_template_part('content', 'head'); ?>
  <header class="header">
    <div class="header__top wrapper">
      <a class="header__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <span class="mozilla-logo"><?php get_template_part('assets/images/mozilla', 'logo.svg'); ?></span>
        <span class="site-logo"><?php get_template_part('assets/images/site', 'logo.svg'); ?></span>
      </a>
    </div>

    <nav class="header__nav js-header-nav">
      <div class="header__wrapper">
        <div class="header__menu">
          <?php wp_nav_menu( array(
            'theme_location' => 'main',
            'container' => false
          )); ?>
        </div>
      </div>
    </nav>

    <div class="header__main">
      <div class="wrapper">

        <?php if (is_front_page()) : ?>

          <h1 class="header__title"><?php the_title(); ?></h1>
          <p class="header__subtitle"><?php the_field('page_subtitle'); ?></p>

        <?php elseif (is_page_template('page_section.php')) : ?>

          <h1 class="header__title"><?php the_field('page_subtitle'); ?></h1>

        <?php elseif (is_page_template('single-stories.php') || is_singular('stories')) : ?>

          <h1 class="header__title"><?php the_title(); ?></h1>
          <p class="header__subtitle"><?php the_field('page_subtitle'); ?></p>

        <?php endif; ?>

      </div>
    </div>
  </header>