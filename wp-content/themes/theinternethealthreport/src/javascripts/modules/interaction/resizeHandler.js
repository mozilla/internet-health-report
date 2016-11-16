import $ from 'jquery';
window.$ = $;
import * as _ from 'lodash';
import * as constants from '../constants';

const resizeEvents = () => {
  constants.headerNavOffsetTop = $(`.js-header-nav`).length ? $(`.js-header-nav`).offset().top : 0;
};

const resizeHandler = () => {
  window.addEventListener(`resize`, _.throttle(resizeEvents, 50));
};

export { resizeHandler };
