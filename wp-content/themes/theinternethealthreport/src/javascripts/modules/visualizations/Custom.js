/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import '../../plugins/noframework.waypoints';
window.$ = $;

class Custom {
  constructor(el) {
    this.el = el;
  }

  render() {
    const waypoint = new Waypoint({
      element: document.getElementById(this.el.substr(1)),
      handler: () => {
        $(this.el).addClass(`is-active`);
        waypoint.destroy();
      },
      offset: `50%`,
    });
  }
}

const loadCustomCharts = () => {
  const $custom = $(`.js-custom`);

  $custom.each((index) => {
    const $this = $custom.eq(index);
    const id = $this.attr(`id`);

    new Custom(`#${id}`).render();
  });
};

export { loadCustomCharts };
