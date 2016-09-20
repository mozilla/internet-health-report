const w = window;
const d = document;
const e = d.documentElement;
const g = d.getElementsByTagName(`body`)[0];

export const homeURL = `http://localhost:3000`;
export const colorRange = [`#f7bda7`, `#ee8596`, `#e05487`, `#a83e90`, `#712998`, `#542277`, `#391550`];
export const breakpointM = 640;
export const getWindowWidth = () => {
  const windowWidth = w.innerWidth||e.clientWidth||g.clientWidth;

  return windowWidth;
};
