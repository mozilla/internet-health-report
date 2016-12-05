<?php
  $ID = get_the_ID();
  $permalink = get_permalink();
  $type = get_field('type', $ID);
  $data_url = get_field('data', $ID);
  $percentage = get_field('percentage_data', $ID);
  $units = get_field('data_units', $ID);
  $margin_left = get_field('data_margin_left', $ID);
  $world_shape = get_field('data_world_shape', $ID);
  $xAxisTitle = get_field('x-axis_title', $ID);
  $yAxisTitle = get_field('y-axis_title', $ID);
  $legendLabel = get_field('legend_label', $ID);
  $sourcesTitle = get_field('sources_title', $ID);
  $facebookShareText = get_field('facebook_share_text', $ID);
  $twitterShareText = get_field('twitter_share_text', $ID);
  $embedShareText = get_field('embed_text', $ID);
?>
<div class="data">
  <div id="<?php echo 'chart-' . $ID; ?>"
    class="chart <?php echo $type; ?>"
    <?php if ($percentage) { echo 'data-percentage="true"'; } ?>
    <?php if ($units) { echo 'data-units="' . $units . '"'; } ?>
    <?php if ($margin_left) { echo 'data-margin-left="' . $margin_left . '"'; } ?>
    <?php if ($world_shape) { echo 'data-world-shape-url="' . $world_shape . '"'; } ?>
    <?php if ($xAxisTitle) { echo 'data-x-axis-title="' . $xAxisTitle . '"'; } ?>
    <?php if ($yAxisTitle) { echo 'data-y-axis-title="' . $yAxisTitle . '"'; } ?>
    <?php if ($legendLabel) { echo 'data-legend-label="' . $legendLabel . '"'; } ?>
    data-url="<?php echo $data_url; ?>"></div>

  <div class="data__footer">
    <div class="data__share">
      <a href="<?php echo get_facebook_share_url($permalink); ?>" target="_blank" class="data__link js-social-share"><?php get_template_part('assets/icons/icon', 'facebook.svg'); ?> <span class="data__link-text"><?php echo $facebookShareText; ?></span></a>
      <a href="<?php echo get_twitter_share_url($permalink); ?>" target="_blank" class="data__link js-social-share"><?php get_template_part('assets/icons/icon', 'twitter.svg'); ?> <span class="data__link-text"><?php echo $twitterShareText; ?></span></a>
      <button class="data__link js-embed-toggle" data-embed="<?php echo $permalink; ?>"><?php get_template_part('assets/icons/icon', 'embed.svg'); ?> <span class="data__link-text"><?php echo $embedShareText; ?></span></button>

      <div class="data__embed js-embed">
        <textarea class="data__textarea" name="" id="" cols="30" rows="10"></textarea>
      </div>
    </div>

    <?php if (have_rows('sources')) : ?>
      <p class="data__sources">
        <?php
          $sourceIndex = 0;
          while (have_rows('sources')) : the_row();
            $sourceName = get_sub_field('name');
            $sourceLink = get_sub_field('url');

            if ($sourceLink) {
              $source = '<a class="data__source" href="' . $sourceLink . '">' . $sourceName . '</a>';
            } else {
              $source = $sourceName;
            }

            if ($sourceIndex > 0) {
              $sourceString = ', ' . $source;
            } else {
              $sourceString = $sourcesTitle . ': ' . $source;
            }

            echo $sourceString;

            $sourceIndex++;
          endwhile;
        ?>
      </p>
    <?php endif; ?>
  </div>
</div>