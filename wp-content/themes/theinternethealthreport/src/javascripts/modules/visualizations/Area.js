/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import '../../plugins/noframework.waypoints';
window.$ = $;

class Area {
  constructor(el, dataUrl, dataIsPercent, xAxisTitle, yAxisTitle) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.dataIsPercent = dataIsPercent;
    this.xAxisTitle = xAxisTitle;
    this.yAxisTitle = yAxisTitle;
    this.parseDate = d3.timeParse(`%d-%b-%y`);
    this.margin = {top: 20, right: 15, bottom: 54, left: 60};
    this.classes = {
      svg: `area__svg`,
      layer: `area__layer`,
      xAxis: `x-axis`,
      xAxisTitle: `x-axis-title`,
      yAxis: `y-axis`,
      yAxisTitle: `y-axis-title`,
      plot: `area__plot`,
      plotOuter: `area__plot-outer`,
      gridY: `area__grid-y`,
      gridX: `area__grid-x`,
    };
    this.svg = d3.select(this.el)
      .append(`svg`)
        .attr(`class`, this.classes.svg);
    this.g = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left},${this.margin.top})`);
  }

  setSizes(transition = false) {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 300 : Math.ceil(this.width * 0.46);
    this.innerWidth = this.width - this.margin.left - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;
    this.plotRadius = constants.getWindowWidth() < constants.breakpointM ? 3 : 4.5;
    this.plotRadiusOuter = constants.getWindowWidth() < constants.breakpointM ? 5 : 10;

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.yMax = this.dataIsPercent ? 100 : d3.max(this.data, d => d[this.dataKeys[1]]);

    this.x = d3.scaleTime()
      .range([0, this.innerWidth])
      .domain(d3.extent(this.data, d => d[this.dataKeys[0]]));
    this.y = d3.scaleLinear()
      .range([this.innerHeight, 0])
      .domain([0, this.yMax]);

    this.area = d3.area()
      .x(d => this.x(d[this.dataKeys[0]]))
      .y0(this.innerHeight)
      .y1(d => this.y(d[this.dataKeys[1]]));

    this.svg.selectAll(`.${this.classes.plot}`)
      .attr(`r`, this.plotRadius)
      .attr(`cx`, d => this.x(d[this.dataKeys[0]]))
      .attr(`cy`, d => this.y(d[this.dataKeys[1]]));

    this.svg.selectAll(`.${this.classes.plotOuter}`)
      .attr(`r`, this.plotRadiusOuter)
      .attr(`cx`, d => this.x(d[this.dataKeys[0]]))
      .attr(`cy`, d => this.y(d[this.dataKeys[1]]));

    if (constants.getWindowWidth() >= constants.breakpointL) {
      this.axisBottom = d3.axisBottom(this.x).ticks(this.data.length);
    } else if (constants.getWindowWidth() >= constants.breakpointM) {
      this.axisBottom = d3.axisBottom(this.x).ticks(this.data.length / 2);
    } else {
      this.axisBottom = d3.axisBottom(this.x).ticks(4);
    }
    this.setAxes();

    if (transition) {
      this.animateChart();
    } else {
      this.svg.select(`.${this.classes.layer}`)
        .attr(`d`, this.area);
    }
  }

  animateChart() {
    $(this.el).addClass(`is-active`);

    this.svg.select(`.${this.classes.layer}`)
      .transition()
      .duration(800)
      .delay(constants.chartFadeIn)
      .ease(d3.easeExpOut)
      .attrTween(`d`, () => {
        const interpolator = d3.interpolateArray(this.startData, this.data);

        return (t) => {
          return this.area(interpolator(t));
        };
      });

    this.svg.selectAll(`.${this.classes.plot}`)
      .transition()
      .duration(300)
      .delay(800)
      .ease(d3.easeCubicIn)
      .style(`opacity`, 1);

    this.svg.selectAll(`.${this.classes.plotOuter}`)
      .transition()
      .duration(300)
      .delay(950)
      .ease(d3.easeCubicIn)
      .style(`opacity`, 1);
  }

  setAxes() {
    // Add x-axis
    this.svg.select(`.${this.classes.xAxis}`)
      .attr(`transform`, `translate(0,${this.innerHeight})`)
      .call(this.axisBottom);

    // Add y-axis
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

  render() {
    d3.tsv(this.dataUrl, (error, data) => {
      if (error) {
        throw error;
      }
      const self = this;

      this.data = data;
      this.dataKeys = constants.getDataKeys(this.data);
      this.data.forEach(d => {
        d[this.dataKeys[0]] = self.parseDate(d[this.dataKeys[0]]);
        d[this.dataKeys[1]] = +d[this.dataKeys[1]];
      });

      this.startData = this.data.map((datum) => {
        const startDatum = {};

        startDatum[this.dataKeys[0]] = datum[this.dataKeys[0]];
        startDatum[this.dataKeys[1]] = 0;

        return startDatum;
      });

      this.g.append(`g`)
        .attr(`class`, this.classes.xAxis);

      this.g.append(`g`)
        .attr(`class`, this.classes.yAxis);

      this.g.append(`g`)
        .attr(`class`, this.classes.gridY);

      this.g.append(`g`)
        .attr(`class`, this.classes.gridX);

      this.g.append(`path`)
        .datum(this.data)
        .attr(`class`, this.classes.layer);

      this.g.selectAll(this.classes.plot)
        .data(this.data)
        .enter().append(`circle`)
        .attr(`class`, this.classes.plot)
        .style(`opacity`, 0);

      this.g.selectAll(this.classes.plotOuter)
        .data(this.data)
        .enter().append(`circle`)
        .attr(`class`, this.classes.plotOuter)
        .style(`opacity`, 0);

      // Add titles to the axes
      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.xAxisTitle)
        .text(this.xAxisTitle);

      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.yAxisTitle)
        .text(this.yAxisTitle);

      // Set waypoint to run chart load animation
      const waypoint = new Waypoint({
        element: document.getElementById(this.el.substr(1)),
        handler: () => {
          this.setSizes(true);
          waypoint.destroy();
        },
        offset: `40%`,
      });
    });

    $(window).on(`resize`, this.resize.bind(this));
  }

  resize() {
    this.setSizes();
  }
}

var loadAreaCharts = () => {
  const $area = $(`.js-area`);

  $area.each((index) => {
    const $this = $area.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);
    const isPercent = $this.data(`percentage`);
    const xAxisTitle = $this.data(`x-axis-title`);
    const yAxisTitle = $this.data(`y-axis-title`);

    new Area(`#${id}`, url, isPercent, xAxisTitle, yAxisTitle).render();
  });
};

export { loadAreaCharts };
