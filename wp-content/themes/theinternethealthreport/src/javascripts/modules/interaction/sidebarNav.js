/* global Waypoint */
import * as constants from '../constants';
import '../../plugins/noframework.waypoints';
import $ from 'jquery';
window.$ = $;

const $sectionWrapper = $(`.js-section-wrapper`);
const $nav = $(`.js-sidebar-nav`);
const $sidebar = $(`.js-sidebar`);
const $sidebarWrapper = $(`.js-sidebar-wrapper`);
const $sidebarLinks = $(`.js-sidebar-link`);
const $navWrapper = $(`.js-header-nav-wrapper`);
const fixedClass = `is-fixed`;
const fixedBottomClass = `is-fixed-bottom`;
let sidebarOffsetTop;
let sidebarOffsetBottom;

const fixNavUpdate = () => {
  // set scrollTop value for fixing sidebar nav to top
  if (constants.getWindowWidth() >= constants.breakpointM) {
    sidebarOffsetTop = $sidebar.offset().top - $navWrapper.outerHeight() - 30;
  } else {
    sidebarOffsetTop = $sidebar.offset().top - $navWrapper.outerHeight();
    $sidebarWrapper.removeClass(fixedBottomClass);
  }

  // set scrollTop value for fixing sidebar to bottom
  sidebarOffsetBottom = $sectionWrapper.offset().top + $sectionWrapper.outerHeight() - $navWrapper.outerHeight() - 30 - $nav.outerHeight();
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
      offset: `25%`,
    });
  });
};

const sidebarClickHandler = () => {
  $sidebarLinks.on(`click`, (e) => {
    e.preventDefault();

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
  // fix below main navigation
  if (scrollTop > sidebarOffsetTop && !$sidebarWrapper.hasClass(fixedClass)) {
    $sidebarWrapper.addClass(fixedClass);
  } else if (scrollTop <= sidebarOffsetTop && $sidebarWrapper.hasClass(fixedClass)) {
    $sidebarWrapper.removeClass(fixedClass);
  }

  // fix to bottom of intro section
  if (constants.getWindowWidth() >= constants.breakpointM) {
    if (scrollTop > sidebarOffsetBottom && !$sidebarWrapper.hasClass(fixedBottomClass)) {
      $sidebarWrapper.addClass(fixedBottomClass);
    } else if (scrollTop <= sidebarOffsetBottom && $sidebarWrapper.hasClass(fixedBottomClass)) {
      $sidebarWrapper.removeClass(fixedBottomClass);
    }
  }
};

const sidebarNavInit = () => {
  if (!$sidebar.length) {
    return false;
  }

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
