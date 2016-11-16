/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import '../../plugins/noframework.waypoints';
window.$ = $;

class StackedArea {
  constructor(el, dataUrl) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.margin = {top: 20, right: 20, bottom: 30, left: 40};
    this.classes = [`stacked-area__svg`, `stacked-area__layer`, `x-axis`, `y-axis`];
    this.svg = d3.select(this.el)
      .append(`svg`)
        .attr(`class`, this.classes[0]);
    this.g = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left},${this.margin.top})`);
    this.z = d3.scaleOrdinal()
      .range(constants.colorRange);
    this.parseDate = d3.timeParse(`%Y %b %d`);
    this.stack = d3.stack();
  }

  setSizes(transition = false) {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 300 : Math.ceil(this.width * 0.52);
    this.innerWidth = this.width - this.margin.left - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;
    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.x = d3.scaleTime()
      .range([0, this.innerWidth]);

    this.y = d3.scaleLinear()
      .range([this.innerHeight, 0]);

    this.x.domain(d3.extent(this.data, d => d.date));
    this.z.domain(this.keys);

    this.area = d3.area()
      .x(d => this.x(d.data.date))
      .y0(d => this.y(d[0]))
      .y1(d => this.y(d[1]));

    this.layer.selectAll(`text`)
      .attr(`x`, this.innerWidth - 6)
      .attr(`y`, d => this.y((d[d.length - 1][0] + d[d.length - 1][1]) / 2));

    this.setAxes();

    if (transition) {
      this.animateChart();
    } else {
      this.layer.selectAll(`path`)
        .attr(`d`, this.area);
    }
  }

  animateChart() {
    $(this.el).addClass(`is-active`);

    this.layer.selectAll(`path`)
      .attr(`d`, this.area);

    // this.layer.selectAll(`path`)
    //   .data(this.stack(this.data))
    //   .transition()
    //   .duration(800)
    //   .ease(d3.easeExpOut)
    //   .attr(`d`, this.area);
  }

  setAxes() {
    this.svg.select(`.${this.classes[2]}`)
      .attr(`transform`, `translate(0,${this.innerHeight})`)
      .call(d3.axisBottom(this.x));

    this.svg.select(`.${this.classes[3]}`)
      .call(d3.axisLeft(this.y).ticks(10, `%`));
  }

  render() {
    d3.tsv(this.dataUrl, this.type.bind(this), (error, data) => {
      if (error) {
        throw error;
      }

      this.data = data;
      this.keys = this.data.columns.slice(1);

      this.startData = this.data.map((datum) => {
        const startDatum = {};

        startDatum.date = datum.date;

        this.keys.forEach((el) => {
          startDatum[el] = 0;
        });

        return startDatum;
      });

      this.stack.keys(this.keys);

      this.layer = this.g.selectAll(`.${this.classes[1]}`)
        .data(this.stack(this.data))
        .enter().append(`g`)
          .attr(`class`, this.classes[1]);

      this.layer.append(`path`)
        .style(`fill`, d => this.z(d.key));

      this.layer.filter(d => d[d.length - 1][1] - d[d.length - 1][0] > 0.01)
        .append(`text`)
          .attr(`dy`, `.35em`)
          .style(`font`, `10px sans-serif`)
          .style(`text-anchor`, `end`)
          .style(`fill`, `#fff`)
          .text(d => d.key);

      // render x-axis
      this.g.append(`g`)
        .attr(`class`, this.classes[2]);

      // render y-axis
      this.g.append(`g`)
        .attr(`class`, this.classes[3]);

      const waypoint = new Waypoint({
        element: document.getElementById(this.el.substr(1)),
        handler: () => {
          this.setSizes(true);
          waypoint.destroy();
        },
        offset: `50%`,
      });
    });

    $(window).on(`resize`, this.resize.bind(this));
  }

  resize() {
    this.setSizes();
  }

  type(d, j, columns) {
    d.date = this.parseDate(d.date);
    for (let i = 1, n = columns.length; i < n; i++) {
      d[columns[i]] = d[columns[i]] / 100;
    }
    return d;
  }
}

const loadStackedAreaCharts = () => {
  const $stackedArea = $(`.js-stacked-area`);

  $stackedArea.each((index) => {
    const $this = $stackedArea.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);

    new StackedArea(`#${id}`, url).render();
  });
};

export { loadStackedAreaCharts };
