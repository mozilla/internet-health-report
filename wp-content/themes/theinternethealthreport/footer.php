<footer class="footer u-space--l">
  <div class="wrapper">
    <div class="footer__col footer__col--first">
      <h4>Keep me informed</h4>
      <p>Add your email to receive updates about the health of the internet project and other related initiatives from Mozilla.</p>
    </div>
    <div class="footer__col footer__col--last">
      <h4>Share this</h4>
      <a href="<?php echo get_facebook_share_url(site_url()); ?>" target="_blank" class="btn js-social-share">Share</a>
      <a href="<?php echo get_twitter_share_url(site_url()); ?>" target="_blank" class="btn js-social-share">Tweet</a>
      <a href="#" class="btn">Email</a>

      <h4>More information</h4>
      <p>Want to find out more about the health of the internet? Download the full report or get in touch with us if you have any suggestions.</p>
      <a href="#" class="footer__link link">About</a>
      <a href="#" class="footer__link link">Download PDF</a>
      <a href="#" class="footer__link link">Contact Us</a>
    </div>
  </div>
</footer>

<?php get_template_part('content', 'footer'); ?>