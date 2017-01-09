/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import '../../plugins/noframework.waypoints';
window.$ = $;

class StackedArea {
  constructor(el, dataUrl, xAxisTitle, yAxisTitle) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.xAxisTitle = xAxisTitle;
    this.yAxisTitle = yAxisTitle;
    this.margin = {top: 20, right: 20, bottom: 54, left: 60};
    this.classes = {
      stackedAreaSvg : `stacked-area__svg`,
      stackedAreaLayer : `stacked-area__layer`,
      stackedBackground: `stacked-area__background`,
      xAxis : `x-axis`,
      xAxisTitle: `x-axis-title`,
      yAxis : `y-axis`,
      yAxisTitle: `y-axis-title`,
    };
    this.legendClasses = {
      legend : `legend`,
      legendMultiline : `legend--stacked`,
      legendItem : `legend__item`,
      legendKey : `legend__key`,
      legendName : `legend__name`,
    };
    this.svg = d3.select(this.el)
      .append(`svg`)
        .attr(`class`, this.classes.stackedAreaSvg);
    this.g = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left},${this.margin.top})`);
    this.z = d3.scaleOrdinal()
      .range(constants.colorRangeArea);
    // this.parseDate = d3.timeParse(`%Y %b %d`);
    this.parseDate = d3.timeParse(`%d-%b-%y`);
    this.stack = d3.stack();
  }

  setSizes(transition = false) {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 400 : Math.ceil(this.width * 0.52);
    this.innerWidth = this.width - this.margin.left - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;
    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.svg.select(`.${this.classes.stackedBackground}`)
      .attr(`width`, this.innerWidth)
      .attr(`height`, this.innerHeight);

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
  }

  setAxes() {
    this.svg.select(`.${this.classes.xAxis}`)
      .attr(`transform`, `translate(0,${this.innerHeight})`)
      .call(d3.axisBottom(this.x));

    this.svg.select(`.${this.classes.yAxis}`)
      .call(d3.axisLeft(this.y).ticks(10, `%`));

    // Set x-axis title
    this.svg.select(`.${this.classes.xAxisTitle}`)
      .attr(`transform`, `translate(${this.width / 2}, ${this.height - 12})`);

    // Set y-axis title
    this.svg.select(`.${this.classes.yAxisTitle}`)
      .attr(`transform`, `translate(12, ${this.height / 2}) rotate(-90)`);
  }

  render() {
    if (!this.dataUrl) {
      return false;
    }

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

      this.backgroundLayer = this.g.append(`rect`)
        .attr(`class`, this.classes.stackedBackground)
        .style(`fill`, `rgb(255,255,255)`);

      this.layer = this.g.selectAll(`.${this.classes.stackedAreaLayer}`)
        .data(this.stack(this.data))
        .enter().append(`g`)
          .attr(`class`, this.classes.stackedAreaLayer);

      this.layer.append(`path`)
        .style(`fill`, d => this.z(d.key));

      this.layer.filter(d => d[d.length - 1][1] - d[d.length - 1][0] > 0.01);

      // render x-axis
      this.g.append(`g`)
        .attr(`class`, this.classes.xAxis);

      // render y-axis
      this.g.append(`g`)
        .attr(`class`, this.classes.yAxis);

      // Add titles to the axes
      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.xAxisTitle)
        .text(this.xAxisTitle);

      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.yAxisTitle)
        .text(this.yAxisTitle);

      this.renderLegend();

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

  renderLegend() {
    const legend = d3.select(this.el).insert(`ul`, `:first-child`)
      .attr(`class`, `${this.legendClasses.legend} ${this.legendClasses.legendMultiline}`);

    const legendItems = legend.selectAll(`li`)
      .data(this.keys)
      .enter().append(`li`)
      .attr(`class`, this.legendClasses.legendItem);

    legendItems.append(`span`)
      .attr(`class`, this.legendClasses.legendKey)
      .style(`background-color`, d => this.z(d));

    legendItems.append(`span`)
      .attr(`class`, this.legendClasses.legendName)
      .text((d, i) => this.keys[i]);
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
    const xAxisTitle = $this.data(`x-axis-title`);
    const yAxisTitle = $this.data(`y-axis-title`);

    new StackedArea(`#${id}`, url, xAxisTitle, yAxisTitle).render();
  });
};

export { loadStackedAreaCharts };
