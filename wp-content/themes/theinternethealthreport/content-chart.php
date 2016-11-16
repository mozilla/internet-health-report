<?php
  $ID = get_the_ID();
  $permalink = get_permalink();
  $type = get_field('type', $ID);
  $data_url = get_field('data', $ID);
  $percentage = get_field('percentage_data', $ID);
  $units = get_field('data_units', $ID);
  $margin_left = get_field('data_margin_left', $ID);
  $world_shape = get_field('data_world_shape', $ID);
?>
<div class="data">
  <div class="data__header">
    <a href="<?php echo get_facebook_share_url($permalink); ?>" target="_blank" class="data__link js-social-share">Share</a>
    <a href="<?php echo get_twitter_share_url($permalink); ?>" target="_blank" class="data__link js-social-share">Tweet</a>
    <button class="data__link js-embed-toggle" data-embed="<?php echo $permalink; ?>">Embed</button>

    <div class="data__embed js-embed">
      <p>Copy & paste code</p>
      <textarea class="data__textarea" name="" id="" cols="30" rows="10"></textarea>
    </div>
  </div>

  <div id="<?php echo 'chart-' . $ID; ?>"
    class="chart <?php echo $type; ?>"
    <?php if ($percentage) { echo 'data-percentage="true"'; } ?>
    <?php if ($units) { echo 'data-units="' . $units . '"'; } ?>
    <?php if ($margin_left) { echo 'data-margin-left="' . $margin_left . '"'; } ?>
    <?php if ($world_shape) { echo 'data-world-shape-url="' . $world_shape . '"'; } ?>
    data-url="<?php echo $data_url; ?>"></div>

  <?php if (have_rows('sources')) : ?>
    <div class="data__sources">
    <p>Sources</p>

    <?php while (have_rows('sources')) : the_row(); ?>
      <a class="data__source" href="<?php the_sub_field('url'); ?>"><?php the_sub_field('name'); ?></a>
    <?php endwhile; ?>

    </div>
  <?php endif; ?>
</div>