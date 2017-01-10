/* global TweenLite Power2 */
import $ from 'jquery';
window.$ = $;

const showEmbedModal = (e) => {
  const $this = $(e.currentTarget);
  const $embed = $this.siblings(`.js-embed`);
  const activeClass = `is-active`;

  if (!$this.data(`ready`)) {
    const embedURL = $this.data(`embed`);
    const chartHeight = $this.parents(`.data`).outerHeight();
    const iframeString = `<iframe width="100%" height="${chartHeight}" src="${embedURL}" frameborder="0"></iframe>`;

    $embed.find(`.data__textarea`).text(iframeString);
    $this.data(`ready`, true);
  }

  if ($embed.hasClass(activeClass)) {
    TweenLite.to($embed, 0.2, { autoAlpha: 0, ease: Power2.easeInOut });
    $embed.removeClass(activeClass);
  } else {
    TweenLite.to($embed, 0.2, { autoAlpha: 1, ease: Power2.easeInOut });
    $embed.addClass(activeClass);
  }
};

const embedInit = () => {
  const $embed = $(`.js-embed-toggle`);

  $embed.on(`click`, showEmbedModal);
};

export { embedInit };
