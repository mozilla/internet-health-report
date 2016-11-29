import * as constants from '../constants';
import $ from 'jquery';
window.$ = $;

const $headerNavWrap = $(`.js-header-nav-wrapper`);
const $headerNav = $(`.js-header-nav`);
const fixedClass = `is-fixed`;
let headerNavOffsetTop;

const fixNavUpdate = () => {
  headerNavOffsetTop = $headerNav.offset().top;
};

const fixNav = (scrollTop) => {
  if (scrollTop > headerNavOffsetTop && !$headerNavWrap.hasClass(fixedClass)) {
    $headerNavWrap.addClass(fixedClass);
  } else if (scrollTop <= headerNavOffsetTop && $headerNavWrap.hasClass(fixedClass)) {
    $headerNavWrap.removeClass(fixedClass);
  }
};

const fixNavInit = () => {
  fixNavUpdate();

  constants.$window.on(`scroll`, () => {
    const scrollTop = $(window).scrollTop();

    fixNav(scrollTop);
  });

  constants.$window.on(`resize`, () => {
    fixNavUpdate();
  });
};

export { fixNavInit };
