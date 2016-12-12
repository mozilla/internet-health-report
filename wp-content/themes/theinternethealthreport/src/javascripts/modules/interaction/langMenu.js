import $ from 'jquery';
window.$ = $;

const langMenuInit = () => {
  const $langMenu = $(`.js-lang-menu`);

  $langMenu.on(`click`, (e) => {
    e.preventDefault();

    $(e.currentTarget).toggleClass(`is-open`);
  });
};

export { langMenuInit };
