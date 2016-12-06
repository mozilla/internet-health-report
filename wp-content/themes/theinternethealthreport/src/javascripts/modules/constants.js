import $ from 'jquery';
window.$ = $;

const w = window;
const d = document;
const e = d.documentElement;
const g = d.getElementsByTagName(`body`)[0];

export let isShareFixed = true;
export const $window = $(w);
export const colorRangeBlack = [`#333333`, `#9a9a9a`];
export const colorRangeDonut = [`#f0c72f`, `#797979`];
export const colorRange = [`#f0c72f`, `#6f6fb0`, `#b2b2fc`, `#f0e2af`, `#f19fc4`, `#adfd35`, `#2dfffe`, `#feeb34`, `#f88ffd`, `#85d2f3`];
export const breakpointM = 768;
export const breakpointL = 1024;
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
