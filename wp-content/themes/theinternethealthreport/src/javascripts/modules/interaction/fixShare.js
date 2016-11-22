import * as constants from '../constants';
import $ from 'jquery';
window.$ = $;

const fixShare = (scrollTop) => {
  const $article = $(`.js-article`);
  const $share = $(`.js-share`);
  const fixedOffsetTop = 500;
  const shareViewportOffsetBottom = constants.getWindowHeight() - fixedOffsetTop - $share.outerHeight();
  const articleBottom = $article.offset().top + $article.outerHeight();

  $share.removeClass(`is-hidden`);

  if (scrollTop >= articleBottom - constants.getWindowHeight() + shareViewportOffsetBottom) {
    if (constants.isShareFixed) {
      constants.isShareFixed = false;
      $share.css({
        position: `absolute`,
        top: `${articleBottom - $share.outerHeight()}px`,
      });
    }
  } else if (!constants.isShareFixed) {
    constants.isShareFixed = true;
    $share.attr(`style`, ``);
  }
};

export { fixShare };
