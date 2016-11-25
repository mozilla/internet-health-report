import $ from 'jquery';
window.$ = $;

const sidebarNavInit = () => {
  const $link = $(`.js-sidebar-link`);

  $link.on(`click`, (e) => {
    e.preventDefault();

    $link.removeClass(`is-active`);
    $(e.currentTarget).addClass(`is-active`);
  });
};

export { sidebarNavInit };
