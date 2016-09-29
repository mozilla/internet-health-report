import $ from 'jquery';
window.$ = $;

const w = window;
const d = document;
const e = d.documentElement;
const g = d.getElementsByTagName(`body`)[0];

export let headerNavOffsetTop = $(`.js-header-nav`).offset().top;
export const colorRange = [`#f7bda7`, `#ee8596`, `#e05487`, `#a83e90`, `#712998`, `#542277`, `#391550`];
export const breakpointM = 768;

export const getWindowWidth = () => {
  const windowWidth = w.innerWidth||e.clientWidth||g.clientWidth;

  return windowWidth;
};

export const getDataKeys = (data) => {
  let dataKeys = [];

  for (let prop in data[0]) {
    if ({}.hasOwnProperty.call(data[0], prop)) {
      dataKeys.push(prop);
    }
  }

  return dataKeys;
};

export const getOrigin = () => {
  const protocol = window.location.protocol;
  const hostname = window.location.hostname;
  const port = window.location.port === `3000` ? `:3000` : ``;
  const origin = window.location.origin || `${protocol}//${hostname}${port}`;

  return origin;
};
