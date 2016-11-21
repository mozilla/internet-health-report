import $ from 'jquery';
window.$ = $;

const w = window;
const d = document;
const e = d.documentElement;
const g = d.getElementsByTagName(`body`)[0];

export let headerNavOffsetTop = $(`.js-header-nav`).length ? $(`.js-header-nav`).offset().top : 0;
export let isShareFixed = true;
export const colorRangeBlack = [`#333333`, `#9a9a9a`];
export const colorRange = [`#f7bda7`, `#ee8596`, `#e05487`, `#a83e90`, `#712998`, `#542277`, `#391550`, `#300f45`];
export const breakpointM = 768;
export const chartFadeIn = 300;

export const getWindowWidth = () => {
  const windowWidth = w.innerWidth||e.clientWidth||g.clientWidth;

  return windowWidth;
};

export const getWindowHeight = () => {
  const windowHeight = w.innerHeight||e.clientHeight||g.clientHeight;

  return windowHeight;
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
