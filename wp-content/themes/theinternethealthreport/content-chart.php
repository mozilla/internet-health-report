<?php
  $ID = get_the_ID();
  $permalink = get_permalink();
  $type = get_field('type', $ID);
  // Data fields
  $data_url = get_field('data', $ID);
  $data_url_two = get_field('secondary_data', $ID);
  // Area fields
  $percentage = get_field('percentage_data', $ID);
  // Choropleth fields
  $units = get_field('data_units', $ID);
  $legendLabel = get_field('legend_label', $ID);
  $isDataOrdinal = get_field('is_data_ordinal', $ID);
  // Chart fields
  $xAxisTitle = get_field('x-axis_title', $ID);
  $yAxisTitle = get_field('y-axis_title', $ID);
  $margin_left = get_field('data_margin_left', $ID);
  $margin_bottom = get_field('data_margin_bottom', $ID);
  // Donut fields
  $donut_description = get_field('donut_description', $ID);
  $donut_description_two = get_field('secondary_donut_description', $ID);
  // Sources
  $sourcesTitle = get_field('sources_title', $ID);
  // Share fields
  $facebookShareText = get_field('facebook_share_text', $ID);
  $twitterShareText = get_field('twitter_share_text', $ID);
  $embedShareText = get_field('embed_text', $ID);
?>
<div class="data <?php if ($type == 'donut js-donut') { echo 'data--donut'; } ?>">
  <?php if ($type == 'donut js-donut') : ?>
    <div class="donut-col">
  <?php endif; ?>

  <?php
    if ($type == 'choropleth js-choropleth') {
      if ($isDataOrdinal) {
        $choropleth_type = ' choropleth--ordinal';
      } else {
        $choropleth_type = ' choropleth--range';
      }
    } else {
      $choropleth_type = '';
    }
  ?>

  <div id="<?php echo 'chart-' . $ID; ?>"
    class="chart <?php echo $type . $choropleth_type; ?>"
    <?php if ($percentage) { echo 'data-percentage="' . $percentage . '"'; } ?>
    <?php if ($units) { echo 'data-units="' . $units . '"'; } ?>
    <?php if ($margin_left) { echo 'data-margin-left="' . $margin_left . '"'; } ?>
    <?php if ($margin_bottom) { echo 'data-margin-bottom="' . $margin_bottom . '"'; } ?>
    <?php if ($type == 'choropleth js-choropleth') { echo 'data-world-shape-url="' . get_template_directory_uri() . '/js/world-topo.json"'; } ?>
    <?php if ($xAxisTitle) { echo 'data-x-axis-title="' . $xAxisTitle . '"'; } ?>
    <?php if ($yAxisTitle) { echo 'data-y-axis-title="' . $yAxisTitle . '"'; } ?>
    <?php if ($legendLabel) { echo 'data-legend-label="' . $legendLabel . '"'; } ?>
    <?php if ($isDataOrdinal) { echo 'data-ordinal="' . $isDataOrdinal . '"'; } ?>
    data-url="<?php echo $data_url; ?>">
  </div>

  <?php if ($type == 'donut js-donut') : ?>
      <?php if ($donut_description) { echo '<p class="donut__description">' . $donut_description . '</p>'; } ?>
    </div>
  <?php endif; ?>

  <?php if ($data_url_two) : ?>
    <div class="donut-col">
      <div id="<?php echo 'chart-' . $ID . '-two'; ?>"
        class="chart <?php echo $type; ?>"
        data-url="<?php echo $data_url_two; ?>">
      </div>
      <?php if ($donut_description_two) { echo '<p class="donut__description">' . $donut_description_two . '</p>'; } ?>
    </div>
  <?php endif; ?>

  <div class="data__footer">
    <div class="data__share">
      <a href="<?php echo get_facebook_share_url($permalink); ?>" target="_blank" class="data__link js-social-share"><?php get_template_part('assets/icons/icon', 'facebook.svg'); ?> <span class="data__link-text"><?php echo $facebookShareText; ?></span></a>
      <a href="<?php echo get_twitter_share_url($permalink, get_the_title($ID)); ?>" target="_blank" class="data__link js-social-share"><?php get_template_part('assets/icons/icon', 'twitter.svg'); ?> <span class="data__link-text"><?php echo $twitterShareText; ?></span></a>
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