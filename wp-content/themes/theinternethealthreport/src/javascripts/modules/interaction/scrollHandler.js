import $ from 'jquery';
window.$ = $;
import * as _ from 'lodash';
import { fixNav } from './fixNav';

const scrollEvents = () => {
  const scrollTop = $(window).scrollTop();

  fixNav(scrollTop);
};

const scrollHandler = () => {
  window.addEventListener(`scroll`, _.throttle(scrollEvents, 50));
};

export { scrollHandler };
