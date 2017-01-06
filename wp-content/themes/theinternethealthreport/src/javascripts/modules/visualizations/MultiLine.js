/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import '../../plugins/noframework.waypoints';
window.$ = $;

class MultiLine {
  constructor(el, dataUrl, xAxisTitle, yAxisTitle) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.xAxisTitle = xAxisTitle;
    this.yAxisTitle = yAxisTitle;
    this.parseDate = d3.timeParse(`%d-%b-%y`);
    this.margin = {top: 20, right: 20, bottom: 54, left: 60};
    this.classes = {
      multilineContainer: `multiline__container`,
      multilineSvg: `multiline__svg`,
      multilineData: `multiline__data`,
      multilineDataset: `multiline__dataset`,
      xAxis: `x-axis`,
      xAxisTitle: `x-axis-title`,
      yAxis: `y-axis`,
      yAxisTitle: `y-axis-title`,
      gridY: `grid-y`,
      gridX: `grid-x`,
    };
    this.legendClasses = [`legend`, `legend--multiline`, `legend__item`, `legend__key`, `legend__name`];
    this.svg = d3.select(this.el)
      .append(`div`)
        .attr(`class`, this.classes.multilineContainer)
      .append(`svg`)
        .attr(`class`, this.classes.multilineSvg);
    this.g = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left},${this.margin.top})`);
  }

  setSizes(transition = false) {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 400 : Math.ceil(this.width * 0.52);
    this.innerWidth = this.width - this.margin.left - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.x = d3.scaleTime()
      .range([0, this.innerWidth]);
    this.y = d3.scaleLinear()
      .range([this.innerHeight, 0]);
    this.z = d3.scaleOrdinal()
      .range(constants.colorRange);

    this.line = d3.line()
      // .curve(d3.curveBasis)
      .x(d => this.x(d.date))
      .y(d => this.y(d.dataValue));

    this.x.domain(d3.extent(this.data, d => d.date));
    this.y.domain([
      d3.min(this.dataColumns, (c) => d3.min(c.values, d => d.dataValue)),
      d3.max(this.dataColumns, (c) => d3.max(c.values, d => d.dataValue))
    ]);
    this.z.domain(this.dataColumns.map((c) => c.id));

    this.svg.selectAll(`.${this.classes.multilineData}`)
      .attr(`d`, d => this.line(d.values))
      .style(`stroke`, d => this.z(d.id));

    if (transition) {
      this.renderLegend();
      this.animateChart();
    }

    this.setAxes();
  }

  setAxes() {
    this.svg.select(`.${this.classes.xAxis}`)
      .attr(`transform`, `translate(0,${this.innerHeight})`)
      .call(d3.axisBottom(this.x).tickFormat(d3.timeFormat(`%Y`)));

    this.svg.select(`.${this.classes.yAxis}`)
      .call(d3.axisLeft(this.y));

    // Add x-axis grid lines
    this.svg.select(`.${this.classes.gridY}`)
      .attr(`transform`, `translate(0, ${this.innerHeight})`)
      .call(d3.axisBottom(this.x).ticks(this.data.length).tickSize(-this.innerHeight).tickFormat(``));

    // Add y-axis grid lines
    this.svg.select(`.${this.classes.gridX}`)
      .call(d3.axisLeft(this.y).tickSize(-this.innerWidth).tickFormat(``));

    // Set x-axis title
    this.svg.select(`.${this.classes.xAxisTitle}`)
      .attr(`transform`, `translate(${this.width / 2}, ${this.height - 12})`);

    // Set y-axis title
    this.svg.select(`.${this.classes.yAxisTitle}`)
      .attr(`transform`, `translate(12, ${this.height / 2}) rotate(-90)`);
  }

  animateChart() {
    this.svg.selectAll(`.${this.classes.multilineData}`)
      .style(`opacity`, `0`);

    $(this.el).addClass(`is-active`);

    const paths = this.svg.selectAll(`.${this.classes.multilineData}`);
    const pathsLength = paths.size();

    for (let i = 0; i < pathsLength; i++) {
      const pathLength = paths
        .filter((d, index) => index === i)
        .node().getTotalLength();

      paths
        .filter((d, index) => index === i)
        .attr(`stroke-dasharray`, `${pathLength} ${pathLength}`)
        .attr(`stroke-dashoffset`, pathLength)
        .style(`opacity`, `1`)
        .transition()
          .duration(500)
          .delay(i * 100)
          .ease(d3.easePolyInOut)
          .attr(`stroke-dashoffset`, 0)
          .on(`end`, () => {
            paths.filter((d, index) => index === i)
              .attr(`stroke-dasharray`, `none`);
          });
    }
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
      this.dataColumns = data.columns.slice(1).map((id) => {
        return {
          id: id,
          values: data.map(d => {
            return {
              date: d.date,
              dataValue: d[id]
            };
          })
        };
      });

      // append x-axis
      this.g.append(`g`)
        .attr(`class`, this.classes.xAxis);

      // append y-axis
      this.g.append(`g`)
        .attr(`class`, this.classes.yAxis);

      // append grid lines
      this.g.append(`g`)
        .attr(`class`, this.classes.gridY);

      this.g.append(`g`)
        .attr(`class`, this.classes.gridX);

      // Add titles to the axes
      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.xAxisTitle)
        .text(this.xAxisTitle);

      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.yAxisTitle)
        .text(this.yAxisTitle);

      // append data
      this.g.selectAll(`.${this.classes.multilineDataset}`)
        .data(this.dataColumns)
        .enter().append(`g`)
          .attr(`class`, this.classes.multilineDataset)
        .append(`path`)
          .attr(`class`, this.classes.multilineData);

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
    const legend = d3.select(this.el).insert(`ul`)
      .lower()
      .attr(`class`, `${this.legendClasses[0]} ${this.legendClasses[1]}`);

    const legendItems = legend.selectAll(`li`)
      .data(this.dataColumns)
      .enter().append(`li`)
      .attr(`class`, this.legendClasses[2]);

    legendItems.append(`span`)
      .attr(`class`, this.legendClasses[3])
      .style(`background-color`, d => this.z(d.id));

    legendItems.append(`span`)
      .attr(`class`, this.legendClasses[4])
      .text((d, i) => this.dataColumns[i][`id`]);
  }

  resize() {
    this.setSizes();
  }

  type(d, _, columns) {
    d.date = this.parseDate(d.date);
    for (let i = 1, n = columns.length, c; i < n; i++) {
      d[c = columns[i]] = +d[c];
    }
    return d;
  }
}

const loadMultiLineCharts = () => {
  const $line = $(`.js-multiline`);

  $line.each((index) => {
    const $this = $line.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);
    const xAxisTitle = $this.data(`x-axis-title`);
    const yAxisTitle = $this.data(`y-axis-title`);

    new MultiLine(`#${id}`, url, xAxisTitle, yAxisTitle).render();
  });
};

export { loadMultiLineCharts };
