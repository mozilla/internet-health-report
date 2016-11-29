import $ from 'jquery';
window.$ = $;
// import * as _ from 'lodash';
import { fixShare } from './fixShare';

const scrollEvents = () => {
  const scrollTop = $(window).scrollTop();
  const $share = $(`.js-share`);

  if ($share.length) {
    fixShare(scrollTop);
  }
};

const scrollHandler = () => {
  $(window).on(`scroll`, scrollEvents);
};

export { scrollHandler };
