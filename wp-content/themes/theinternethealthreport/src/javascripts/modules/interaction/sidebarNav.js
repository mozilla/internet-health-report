/* global Waypoint */
import * as constants from '../constants';
import '../../plugins/noframework.waypoints';
import $ from 'jquery';
window.$ = $;

const $nav = $(`.js-sidebar-nav`);
const $sidebar = $(`.js-sidebar`);
const $sidebarWrapper = $(`.js-sidebar-wrapper`);
const $sidebarLinks = $(`.js-sidebar-link`);
const fixedClass = `is-fixed`;
let sidebarOffsetTop;

const fixNavUpdate = () => {
  sidebarOffsetTop = $sidebar.offset().top - $(`.js-header-nav-wrapper`).outerHeight();
};

const sidebarWaypointsInit = () => {
  const $waypoints = $(`.js-sidebar-block`);
  const $sidebarActive = $(`.js-sidebar-active`);

  $waypoints.each((index) => {
    const $this = $waypoints.eq(index);
    const prevIndex = index === 0 ? 0 : index - 1;
    const $prev = $waypoints.eq(prevIndex);
    const id = $this.attr(`id`);
    const title = $this.data(`title`);
    const prevTitle = $prev.data(`title`);

    const waypoint = new Waypoint({
      element: document.getElementById(id),
      handler: (direction) => {
        $sidebarLinks.removeClass(`is-active`);

        if (direction === `down`) {
          $sidebarActive.text(title);
          $sidebarLinks.eq(index).addClass(`is-active`);
        } else {
          $sidebarActive.text(prevTitle);
          $sidebarLinks.eq(prevIndex).addClass(`is-active`);
        }
      },
      offset: `50%`,
    });
  });
};

const sidebarClickHandler = () => {
  $sidebarLinks.on(`click`, (e) => {
    e.preventDefault();

    $sidebarLinks.removeClass(`is-active`);
    $(e.currentTarget).addClass(`is-active`);
    $nav.removeClass(`is-open`);
  });
};

const sidebarToggleHandler = () => {
  const $toggle = $(`.js-sidebar-toggle`);

  $toggle.on(`click`, () => {
    $nav.toggleClass(`is-open`);
  });
};

const fixNav = (scrollTop) => {
  if (scrollTop > sidebarOffsetTop && !$sidebarWrapper.hasClass(fixedClass)) {
    $sidebarWrapper.addClass(fixedClass);
  } else if (scrollTop <= sidebarOffsetTop && $sidebarWrapper.hasClass(fixedClass)) {
    $sidebarWrapper.removeClass(fixedClass);
  }
};

const sidebarNavInit = () => {
  fixNavUpdate();
  sidebarWaypointsInit();
  sidebarClickHandler();
  sidebarToggleHandler();

  constants.$window.on(`scroll`, () => {
    const scrollTop = constants.$window.scrollTop();

    fixNav(scrollTop);
  });

  constants.$window.on(`resize`, () => {
    fixNavUpdate();
  });
};

export { sidebarNavInit };
