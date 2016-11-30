import * as constants from '../constants';
import $ from 'jquery';
window.$ = $;

const $share = $(`.js-share`);
const $article = $(`.js-article-main`);
const fixedBottomClass = `is-fixed-bottom`;
let shareOffsetBottom;

const fixShareUpdate = () => {
  shareOffsetBottom = $article.offset().top + $article.outerHeight() - 500 - $share.outerHeight();
};

const fixShare = (scrollTop) => {
  if (constants.getWindowWidth() >= constants.breakpointM) {
    if (scrollTop > shareOffsetBottom && !$share.hasClass(fixedBottomClass)) {
      $share.addClass(fixedBottomClass);
    } else if (scrollTop <= shareOffsetBottom && $share.hasClass(fixedBottomClass)) {
      $share.removeClass(fixedBottomClass);
    }
  }
};

const shareNavInit = () => {
  if (!$share.length) {
    return false;
  }

  fixShareUpdate();

  constants.$window.on(`scroll`, () => {
    const scrollTop = constants.$window.scrollTop();

    fixShare(scrollTop);
  });

  constants.$window.on(`resize`, () => {
    fixShareUpdate();
  });
};

export { shareNavInit };
