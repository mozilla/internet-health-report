import $ from 'jquery';
window.$ = $;
// import * as _ from 'lodash';
import { fixNav } from './fixNav';
import { fixShare } from './fixShare';

const scrollEvents = () => {
  const scrollTop = $(window).scrollTop();
  const $share = $(`.js-share`);

  fixNav(scrollTop);

  if ($share.length) {
    fixShare(scrollTop);
  }
};

const scrollHandler = () => {
  $(window).on(`scroll`, scrollEvents);
};

export { scrollHandler };
