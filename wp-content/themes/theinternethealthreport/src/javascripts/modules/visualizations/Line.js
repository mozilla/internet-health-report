/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import '../../plugins/noframework.waypoints';
window.$ = $;

class Line {
  constructor(el, dataUrl, xAxisTitle, yAxisTitle) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.xAxisTitle = xAxisTitle;
    this.yAxisTitle = yAxisTitle;
    this.parseDate = d3.timeParse(`%d-%b-%y`);
    this.margin = {top: 20, right: 20, bottom: 54, left: 60};
    this.animationDuration = 1000;
    this.classes = {
      svg: `line__svg`,
      data: `line__data`,
      xAxis: `x-axis`,
      xAxisTitle: `x-axis-title`,
      yAxis: `y-axis`,
      yAxisTitle: `y-axis-title`,
      plot: `plot`,
      plotOuter: `plot-outer`,
      gridY: `grid-y`,
      gridX: `grid-x`,
      tooltip: `tooltip line__tooltip`,
    };
    this.svg = d3.select(this.el).append(`svg`)
      .attr(`class`, this.classes.svg);
    this.svgData = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left},${this.margin.top})`);
    this.tooltip = d3.select(this.el)
      .append(`div`)
      .attr(`class`, this.classes.tooltip);
  }

  setSizes(transition = false) {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 400 : Math.ceil(this.width * 0.52);
    this.innerWidth = this.width - this.margin.left - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;
    this.plotRadius = constants.getWindowWidth() < constants.breakpointM ? 3 : 4.5;
    this.plotRadiusOuter = constants.getWindowWidth() < constants.breakpointM ? 5 : 10;

    this.x = d3.scaleTime()
      .range([0, this.innerWidth]);

    this.y = d3.scaleLinear()
      .range([this.innerHeight, 0]);

    this.line = d3.line()
      .x(d => this.x(d[this.dataKeys[0]]))
      .y(d => this.y(d[this.dataKeys[1]]));

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.x.domain([this.minDate, this.maxDate]);
    this.y.domain([d3.min(this.data, d => d[this.dataKeys[1]]) - 1, d3.max(this.data, d => d[this.dataKeys[1]])]);

    this.svg.select(`.${this.classes.data}`)
      .attr(`d`, this.line);

    // set data
    this.svg.selectAll(`.${this.classes.plot}`)
      .attr(`r`, this.plotRadius)
      .attr(`cx`, d => this.x(d[this.dataKeys[0]]))
      .attr(`cy`, d => this.y(d[this.dataKeys[1]]));

    this.svg.selectAll(`.${this.classes.plotOuter}`)
      .attr(`r`, this.plotRadiusOuter)
      .attr(`cx`, d => this.x(d[this.dataKeys[0]]))
      .attr(`cy`, d => this.y(d[this.dataKeys[1]]));

    if (transition) {
      this.animateChart();
    }

    this.setAxes();
    this.addTooltipEvents();
  }

  addTooltipEvents() {
    this.svg.selectAll(`.${this.classes.plot}`)
      .on(`mouseover`, d => {
        this.tooltip
          .html(`${d[this.dataKeys[1]]}`)
          .classed(`is-active`, true);
      })
      .on(`mousemove`, () => {
        this.tooltip
          .style(`top`, `${d3.event.pageY - $(this.el).offset().top}px`)
          .style(`left`, `${d3.event.pageX - $(this.el).offset().left}px`);
      })
      .on(`mouseout`, () => {
        this.tooltip
          .classed(`is-active`, false);
      });
  }

  setAxes() {
    this.axisBottom = constants.getWindowWidth() < constants.breakpointM ? d3.axisBottom(this.x).ticks(4).tickFormat(d3.timeFormat(`%b`)) : d3.axisBottom(this.x).tickFormat(d3.timeFormat(`%b`));

    this.svg.select(`.${this.classes.xAxis}`)
      .attr(`transform`, `translate(0,${this.innerHeight})`)
      .call(this.axisBottom);

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
    this.pathLength = this.svg.select(`.${this.classes.data}`).node().getTotalLength();
    $(this.el).addClass(`is-active`);

    this.svg.select(`.${this.classes.data}`)
      .attr(`stroke-dasharray`, `${this.pathLength} ${this.pathLength}`)
      .attr(`stroke-dashoffset`, this.pathLength)
      .transition()
        .duration(this.animationDuration)
        .ease(d3.easePolyInOut)
        .attr(`stroke-dashoffset`, 0)
        .on(`end`, () => {
          this.svg.select(`.${this.classes.data}`)
            .attr(`stroke-dasharray`, `none`);
        });

    this.svg.selectAll(`.${this.classes.plot}`)
      .transition()
      .duration(500)
      .delay((d, i) => {
        return i * 50;
      })
      .ease(d3.easeElasticIn)
      .style(`opacity`, `1`);

    this.svg.selectAll(`.${this.classes.plotOuter}`)
      .transition()
      .duration(300)
      .delay(950)
      .ease(d3.easeCubicIn)
      .style(`opacity`, `1`);
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

      this.minDate = d3.min(this.data, d => d[this.dataKeys[0]].getTime());
      this.maxDate = d3.max(this.data, d => d[this.dataKeys[0]].getTime());
      // this.xPad = (this.maxDate - this.minDate) * 0.05;

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

      this.svgData.append(`path`)
        .datum(this.data)
        .attr(`class`, this.classes.data);

      // append scatterplot plots
      this.svgData.selectAll(this.classes.plot)
        .data(this.data)
        .enter().append(`circle`)
          .attr(`class`, this.classes.plot)
          .attr(`r`, 4.5)
          .style(`opacity`, `0`);

      this.svgData.selectAll(this.classes.plotOuter)
        .data(this.data)
        .enter().append(`circle`)
        .attr(`class`, this.classes.plotOuter)
        .style(`opacity`, `0`);

      // Add titles to the axes
      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.xAxisTitle)
        .text(this.xAxisTitle);

      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.yAxisTitle)
        .text(this.yAxisTitle);

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

  type(d) {
    d[this.dataKeys[0]] = this.parseDate(d[this.dataKeys[0]]);
    d[this.dataKeys[1]] = +d[this.dataKeys[1]];
    return d;
  }
}

const loadLineCharts = () => {
  const $line = $(`.js-line`);

  $line.each((index) => {
    const $this = $line.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);
    const xAxisTitle = $this.data(`x-axis-title`);
    const yAxisTitle = $this.data(`y-axis-title`);

    new Line(`#${id}`, url, xAxisTitle, yAxisTitle).render();
  });
};

export { loadLineCharts };
