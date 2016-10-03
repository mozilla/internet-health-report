/* global TweenLite Power3 */
import $ from 'jquery';
import 'gsap/src/uncompressed/plugins/ScrollToPlugin';
window.$ = $;

const scrollToEl = (e) => {
  const $this = $(e.currentTarget);
  const id = $this.attr(`href`);
  const offsetY = $(`.header__menu`).height() + $(`.submenu-wrapper`).height();

  TweenLite.to(window, 0.75, { scrollTo:{y:id, offsetY:offsetY}, ease:Power3.easeInOut });

  return false;
};

const smoothScrollInit = () => {
  const $scrollTo = $(`.js-scroll-to`);

  $scrollTo.each((index) => {
    const $this = $scrollTo.eq(index);

    $this.on(`click`, scrollToEl);
  });
};

export { smoothScrollInit };
