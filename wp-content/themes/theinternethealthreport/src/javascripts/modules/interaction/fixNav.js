import * as constants from '../constants';
import $ from 'jquery';
window.$ = $;

const fixedHeaderNavClass = `is-fixed`;

const fixNav = (scrollTop) => {
  const $headerNav = $(`.js-header-nav`);

  if (scrollTop > constants.headerNavOffsetTop && !$headerNav.hasClass(fixedHeaderNavClass)) {
    $headerNav.addClass(fixedHeaderNavClass);
  } else if (scrollTop <= constants.headerNavOffsetTop && $headerNav.hasClass(fixedHeaderNavClass)) {
    $headerNav.removeClass(fixedHeaderNavClass);
  }
};

export { fixNav };
