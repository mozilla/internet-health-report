import $ from 'jquery';
window.$ = $;

const scrollToCurrentMenuItem = () => {
  const $menu = $(`.menu`);
  const $items = $(`.menu-item`);

  $items.each(i => {
    const $this = $items.eq(i);

    if ($this.hasClass(`current-menu-item`) || $this.hasClass(`current-menu-parent`)) {
      const positionLeft = $this.position().left;

      $menu.scrollLeft(positionLeft);
    }
  });
};

export { scrollToCurrentMenuItem };
