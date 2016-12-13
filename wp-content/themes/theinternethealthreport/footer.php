<?php
  $front_page_ID = get_option( 'page_on_front' );
?>
<footer class="footer">
  <div class="footer__grid wrapper">
    <div class="footer__col footer__col--first">
      <h4 class="footer__title"><?php the_field('signup_title', $front_page_ID); ?></h4>
      <p class="footer__text"><?php the_field('signup_text', $front_page_ID); ?></p>

      <form class="signup-form" action="/en-US/newsletter/" method="post">
        <div>
          <input id="id_email" class="signup-form__email" name="email" placeholder="<?php the_field('signup_email_placeholder', $front_page_ID); ?>" required="required" type="email">
        </div>

        <div>
          <select aria-required="true" id="id_country" class="signup-form__select" name="country" required="required">
            <option disabled selected value><?php the_field('signup_country_select', $front_page_ID); ?></option>
            <option value="gb">United Kingdom</option>
            <option value="us">United States</option>
          </select>
        </div>

        <div>
          <label for="id_privacy"><input id="id_privacy" class="signup-form__checkbox" name="privacy" required="required" type="checkbox"><span class="signup-form__checkbox-label"><?php the_field('signup_disclaimer', $front_page_ID); ?></span></label>
        </div>

        <div>
          <button type="submit" id="footer_email_submit" class="signup-form__btn"><?php the_field('signup_submit', $front_page_ID); ?></button>
        </div>
      </form>
    </div>

    <div class="footer__col footer__col--last">
      <h4 class="footer__title"><?php the_field('more_title', $front_page_ID); ?></h4>
      <p class="footer__text"><?php the_field('more_text_1', $front_page_ID); ?></p>

      <?php if (have_rows('more_links_1', $front_page_ID)) : ?>
      <div class="footer__links">
        <?php while(have_rows('more_links_1', $front_page_ID)) : the_row(); ?>
          <a class="footer__link" href="<?php the_sub_field('link_url'); ?>"><?php the_sub_field('link_text'); ?> <?php get_template_part('assets/icons/icon', 'link.svg'); ?></a>
        <?php endwhile; ?>
      </div>
      <?php endif; ?>

      <p class="footer__text footer__text--2"><?php the_field('more_text_2', $front_page_ID); ?></p>

      <?php if (have_rows('more_links_2', $front_page_ID)) : ?>
      <div class="footer__links">
        <?php while(have_rows('more_links_2', $front_page_ID)) : the_row(); ?>
          <a class="footer__link" href="<?php the_sub_field('link_url'); ?>"><?php the_sub_field('link_text'); ?> <?php get_template_part('assets/icons/icon', 'link.svg'); ?></a>
        <?php endwhile; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</footer>

<?php get_template_part('content', 'footer'); ?>