/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import '../../plugins/noframework.waypoints';
window.$ = $;

class Bar {
  constructor(el, dataUrl, xAxisTitle, yAxisTitle, marginLeft = 10, marginBottom = 40, dataMax = false) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.xAxisTitle = xAxisTitle;
    this.yAxisTitle = yAxisTitle;
    this.marginLeft = marginLeft;
    this.marginBottom = marginBottom;
    this.dataMax = dataMax;
    this.waypointInit = false;
    this.margin = {top: 20, right: 20, bottom: this.marginBottom, bottomTitle: 35, left: this.marginLeft, leftTitle: 60};
    this.classes = {
      barSvg: `bar__svg`,
      barData: `bar__data`,
      xAxis: `x-axis`,
      xAxisTitle: `x-axis-title`,
      yAxis: `y-axis`,
      yAxisTitle: `y-axis-title`,
      gridY: `grid-y`,
      gridX: `grid-x`,
    };
    this.svg = d3.select(this.el)
      .append(`svg`)
        .attr(`class`, this.classes.barSvg);
    this.svgData = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left + this.margin.leftTitle}, ${this.margin.top})`);
  }

  setSizes(transition = false, resize = false) {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 400 : Math.ceil(this.width * 0.52);
    this.innerWidth = this.width - this.margin.left - this.margin.leftTitle - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom - this.margin.bottomTitle;

    this.x = d3.scaleBand()
      .rangeRound([0, this.innerWidth])
      .padding(0.2);

    this.y = d3.scaleLinear()
      .rangeRound([this.innerHeight, 0]);

    this.x.domain(this.data.map(d => d[this.dataKeys[0]]));
    this.y.domain([0, this.dataMax ? this.dataMax : d3.max(this.data, d => d[this.dataKeys[1]])]);

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.svg.selectAll(`.${this.classes.barData}`)
      .attr(`x`, d => this.x(d[this.dataKeys[0]]))
      .attr(`y`, this.innerHeight)
      .attr(`width`, this.x.bandwidth());

    this.setAxes();

    if (transition) {
      this.animateChart();
    }

    if (resize && this.waypointInit) {
      this.svg.selectAll(`.${this.classes.barData}`)
        .attr(`y`, d => this.y(d[this.dataKeys[1]]))
        .attr(`height`, d => this.innerHeight - this.y(d[this.dataKeys[1]]));
    }
  }

  animateChart() {
    $(this.el).addClass(`is-active`);

    this.svg.selectAll(`.${this.classes.barData}`)
      .transition()
      .duration(constants.chartFadeIn + 500)
      .delay((d, i) => i * 100)
      .attr(`y`, d => this.y(d[this.dataKeys[1]]))
      .attr(`height`, d => this.innerHeight - this.y(d[this.dataKeys[1]]));
  }

  setAxes() {
    this.svg.select(`.${this.classes.xAxis}`)
      .attr(`transform`, `translate(0,${this.innerHeight})`)
      .call(d3.axisBottom(this.x))
      .selectAll(`text`)
        .attr(`x`, -6)
        .attr(`y`, 6)
        .attr(`text-anchor`, `end`)
        .attr(`transform`, `rotate(-45)`);

    this.svg.select(`.${this.classes.yAxis}`)
      .call(d3.axisLeft(this.y)
        .tickFormat(this.tickFormat));

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

  render() {
    if (!this.dataUrl) {
      return false;
    }

    d3.tsv(this.dataUrl, (error, data) => {
      if (error) {
        throw error;
      }

      this.data = data;
      this.dataKeys = constants.getDataKeys(this.data);
      this.data.forEach(this.type.bind(this));

      // append x-axis
      this.svgData.append(`g`)
        .attr(`class`, this.classes.xAxis);

      // append y-axis
      this.svgData.append(`g`)
        .attr(`class`, this.classes.yAxis);

      this.svgData.append(`g`)
        .attr(`class`, this.classes.gridY);

      this.svgData.append(`g`)
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

      this.svgData.selectAll(`.${this.classes.barData}`)
        .data(this.data)
        .enter().append(`rect`)
        .attr(`class`, this.classes.barData);

      this.setTickFormat();
      this.setSizes();

      const waypoint = new Waypoint({
        element: document.getElementById(this.el.substr(1)),
        handler: () => {
          this.setSizes(true);
          this.waypointInit = true;
          waypoint.destroy();
        },
        offset: `50%`,
      });
    });

    $(window).on(`resize`, this.resize.bind(this));
  }

  setTickFormat() {
    let dataValues = this.data.map((obj) => {
      return obj[this.dataKeys[1]];
    });
    const maxDataValue = Math.max(...dataValues);
    const formatNumber = d3.format(`.1f`);
    const formatBillion = x => `${formatNumber(x / 1e9)}B`;
    const formatMillion = x => `${formatNumber(x / 1e6)}M`;
    const formatThousand = x => `${formatNumber(x / 1e3)}k`;

    this.tickFormat = maxDataValue >= 1e9 ? formatBillion
      : maxDataValue >= 1e6 ? formatMillion
      : maxDataValue >= 1e3 ? formatThousand
      : d3.formatPrefix(`.0`, 10);
  }

  resize() {
    this.setSizes(false, true);
  }

  type(d) {
    d[this.dataKeys[1]] = parseInt(d[this.dataKeys[1]], 10);
    d[this.dataKeys[1]] = +d[this.dataKeys[1]];
    return d;
  }
}

const loadBarCharts = () => {
  const $bar = $(`.js-bar`);

  $bar.each((index) => {
    const $this = $bar.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);
    const xAxisTitle = $this.data(`x-axis-title`);
    const yAxisTitle = $this.data(`y-axis-title`);
    const marginLeft = $this.data(`margin-left`);
    const marginBottom = $this.data(`margin-bottom`);
    const dataMax = $this.data(`percentage`);

    new Bar(`#${id}`, url, xAxisTitle, yAxisTitle, marginLeft, marginBottom, dataMax).render();
  });
};

export { loadBarCharts };
