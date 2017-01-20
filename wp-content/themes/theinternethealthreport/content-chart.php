<?php
  $ID = get_the_ID();
  $permalink = get_permalink();
  $type = get_field('type', $ID);
  // Data fields
  // $data_url = get_field('data', $ID);
  // $data_url_two = get_field('secondary_data', $ID);

  $data_url_abs = get_field('data', $ID);
  $data_url_two_abs = get_field('secondary_data', $ID);
  $data_url = '/' . strstr($data_url_abs, 'wp-content');
  $data_url_two = '/' . strstr($data_url_two_abs, 'wp-content');
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
  // Custom chart fields
  $users_label = get_field('percentage_of_internet_users_label', $ID);
  $content_label = get_field('percentage_of_internet_content_label', $ID);
  $label_english = get_field('label_english', $ID);
  $label_russian = get_field('label_russian', $ID);
  $label_german = get_field('label_german', $ID);
  $label_japanese = get_field('label_japanese', $ID);
  $label_spanish = get_field('label_spanish', $ID);
  $label_french = get_field('label_french', $ID);
  $label_portugese = get_field('label_portugese', $ID);
  $label_chinese = get_field('label_chinese', $ID);
  $label_arabic = get_field('label_arabic', $ID);
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

  <div id="<?php echo 'chart-' . $ID . rand(); ?>"
    class="chart <?php echo $type . $choropleth_type; ?>"
    <?php if ($percentage) { echo 'data-percentage="' . $percentage . '"'; } ?>
    <?php if ($units) { echo 'data-units="' . $units . '"'; } ?>
    <?php if ($margin_left) { echo 'data-margin-left="' . $margin_left . '"'; } ?>
    <?php if ($margin_bottom) { echo 'data-margin-bottom="' . $margin_bottom . '"'; } ?>
    <?php if ($type == 'choropleth js-choropleth') { echo 'data-world-shape-url="/wp-content/themes/theinternethealthreport/js/world-topo.json"'; } ?>
    <?php if ($xAxisTitle) { echo 'data-x-axis-title="' . $xAxisTitle . '"'; } ?>
    <?php if ($yAxisTitle) { echo 'data-y-axis-title="' . $yAxisTitle . '"'; } ?>
    <?php if ($legendLabel) { echo 'data-legend-label="' . $legendLabel . '"'; } ?>
    <?php if ($isDataOrdinal) { echo 'data-ordinal="' . $isDataOrdinal . '"'; } ?>
    <?php if ($label_english) { echo 'data-label-english="' . $label_english . '"'; } ?>
    <?php if ($label_russian) { echo 'data-label-russian="' . $label_russian . '"'; } ?>
    <?php if ($label_german) { echo 'data-label-german="' . $label_german . '"'; } ?>
    <?php if ($label_japanese) { echo 'data-label-japanese="' . $label_japanese . '"'; } ?>
    <?php if ($label_spanish) { echo 'data-label-spanish="' . $label_spanish . '"'; } ?>
    <?php if ($label_french) { echo 'data-label-french="' . $label_french . '"'; } ?>
    <?php if ($label_portugese) { echo 'data-label-portugese="' . $label_portugese . '"'; } ?>
    <?php if ($label_chinese) { echo 'data-label-chinese="' . $label_chinese . '"'; } ?>
    <?php if ($label_arabic) { echo 'data-label-arabic="' . $label_arabic . '"'; } ?>
    data-url="<?php echo $data_url; ?>">
    <?php if ($type == 'custom js-custom') : ?>
      <p class="custom__label custom__label--first"><?php echo $users_label; ?></p>
      <p class="custom__label custom__label--last"><?php echo $content_label; ?></p>
      <div class="custom__chart">
        <?php get_template_part('assets/images/custom', 'chart-english.svg'); ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($type == 'donut js-donut') : ?>
      <?php if ($donut_description) { echo '<p class="donut__description">' . $donut_description . '</p>'; } ?>
    </div>
  <?php endif; ?>

  <?php if ($type == 'donut js-donut' && strlen($data_url_two) > 1) : ?>
    <div class="donut-col">
      <div id="<?php echo 'chart-' . $ID . rand(); ?>"
        class="chart <?php echo $type; ?>"
        data-url="<?php echo $data_url_two; ?>">
      </div>
      <?php if ($donut_description_two) { echo '<p class="donut__description">' . $donut_description_two . '</p>'; } ?>
    </div>
  <?php endif; ?>

  <div class="data__footer">
    <div class="data__share">
      <a href="<?php echo get_facebook_share_url($page_permalink . '#' . $chart_id); ?>" target="_blank" class="data__link js-social-share"><?php get_template_part('assets/icons/icon', 'facebook.svg'); ?> <span class="data__link-text"><?php echo $facebookShareText; ?></span></a>
      <a href="<?php echo get_twitter_share_url($page_permalink . '#' . $chart_id, get_the_title($ID)); ?>" target="_blank" class="data__link js-social-share"><?php get_template_part('assets/icons/icon', 'twitter.svg'); ?> <span class="data__link-text"><?php echo $twitterShareText; ?></span></a>
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
              $source = '<a class="data__source" target="_blank" href="' . $sourceLink . '">' . $sourceName . '</a>';
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