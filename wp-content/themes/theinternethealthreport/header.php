<?php get_template_part('content', 'head'); ?>
  <header class="header">
    <div class="wrapper">
      <a class="header__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <h1 class="header__title"><?php bloginfo( 'name' ); ?></h1>
        <p class="header__subtitle"><?php bloginfo( 'description'); ?></p>
      </a>
    </div>

    <nav class="header__nav js-header-nav">
      <div class="wrapper">
        <div class="header__menu">
          <?php wp_nav_menu( array(
            'theme_location' => 'main',
            'container' => false
          )); ?>
        </div>
      </div>
    </nav>
  </header>