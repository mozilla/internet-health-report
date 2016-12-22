/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import '../../plugins/noframework.waypoints';
window.$ = $;

class Bar {
  constructor(el, dataUrl, xAxisTitle, yAxisTitle, marginLeft = 10, marginBottom = 40) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.xAxisTitle = xAxisTitle;
    this.yAxisTitle = yAxisTitle;
    this.marginLeft = marginLeft;
    this.marginBottom = marginBottom;
    this.margin = {top: 20, right: 20, bottom: this.marginBottom, bottomTitle: 35, left: this.marginLeft, leftTitle: 40};
    this.classes = {
      barStackedSvg: `bar-stacked__svg`,
      barStackedData: `bar-stacked__data`,
      barStackedRect: `bar-stacked__rect`,
      xAxis: `x-axis`,
      xAxisTitle: `x-axis-title`,
      yAxis: `y-axis`,
      yAxisTitle: `y-axis-title`,
      gridY: `grid-y`,
      gridX: `grid-x`,
    };
    this.legendClasses = {
      legend: `legend`,
      legendMultiline: `legend--stacked`,
      legendItem: `legend__item`,
      legendKey: `legend__key`,
      legendName: `legend__name`,
    };
    this.stack = d3.stack();
    this.z = d3.scaleOrdinal()
      .range(constants.colorRange);
    this.svg = d3.select(this.el)
      .append(`svg`)
        .attr(`class`, this.classes.barStackedSvg);
    this.svgData = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left + this.margin.leftTitle},${this.margin.top})`);
  }

  setSizes(transition = false) {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 400 : Math.ceil(this.width * 0.52);
    this.innerWidth = this.width - this.margin.left - this.margin.leftTitle - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom - this.margin.bottomTitle;

    this.x = d3.scaleBand()
      .rangeRound([0, this.innerWidth])
      .padding(0.4)
      .align(0.5);

    this.y = d3.scaleLinear()
      .rangeRound([this.innerHeight, 0]);

    this.x.domain(this.data.map(d => d.Country));
    this.y.domain([0, d3.max(this.data, d => d.total)]).nice();
    this.z.domain(this.data.columns.slice(1));

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.svgData.selectAll(`.${this.classes.barStackedData}`)
      .attr(`fill`, d => this.z(d.key));

    this.svgData.selectAll(`.${this.classes.barStackedData} rect`)
        .attr(`x`, d => this.x(d.data.Country))
        .attr(`y`, d => this.y(d[1]) + (this.y(d[0]) - this.y(d[1])))
        .attr(`width`, this.x.bandwidth());

    this.setAxes();

    if (transition) {
      this.animateChart();
    } else if ($(this.el).hasClass(`is-active`)) {
      this.svg.selectAll(`.${this.classes.barStackedData} rect`)
        .attr(`y`, d => this.y(d[1]))
        .attr(`height`, d => this.y(d[0]) - this.y(d[1]));
    }
  }

  animateChart() {
    $(this.el).addClass(`is-active`);

    const dataColLength = this.data.length;
    const transitionDuration = 300;
    const dataTypesLength = this.data.columns.slice(1).length;

    this.svg.selectAll(`.${this.classes.barStackedData} rect`)
      .transition()
      .duration(transitionDuration)
      .ease(d3.easePolyOut)
      .delay((d, i) => {
        let index;
        let delayDuration;

        for (index = dataTypesLength - 1; index >= 0; index--) {
          if (i >= dataColLength * index) {
            delayDuration = constants.chartFadeIn + (transitionDuration * index);
            break;
          }
        }

        return delayDuration;
      })
      .attr(`y`, d => this.y(d[1]))
      .attr(`height`, d => this.y(d[0]) - this.y(d[1]));
  }

  setAxes() {
    this.svg.select(`.${this.classes.xAxis}`)
      .attr(`transform`, `translate(0,${this.innerHeight})`)
      .call(d3.axisBottom(this.x))
        .selectAll(`text`)
        .attr(`transform`, `rotate(-45)`)
        .attr(`x`, -5)
        .attr(`y`, 6)
        .style(`text-anchor`, `end`);

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
    if (!this.dataUrl) {
      return false;
    }

    d3.tsv(this.dataUrl, (error, data) => {
      if (error) {
        throw error;
      }

      this.data = data;
      this.data.forEach(this.type.bind(this));
      this.data.sort((a, b) => b.total - a.total);
      this.keys = this.data.columns.slice(1);

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

      this.svgData.selectAll(`.${this.classes.barStackedData}`)
        .data(this.stack.keys(this.data.columns.slice(1))(this.data))
        .enter().append(`g`)
          .attr(`class`, this.classes.barStackedData)
        .selectAll(`rect`)
        .data(d => d)
        .enter().append(`rect`);

      this.renderLegend();
      this.setSizes();

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

  type(d, i, columns) {
    let t;

    for (i = 1, t = 0; i < columns.columns.length; ++i) {
      t += d[columns.columns[i]] = +d[columns.columns[i]];
    }

    d.total = t;
    return d;
  }
}

const loadBarStackedCharts = () => {
  const $bar = $(`.js-bar-stacked`);

  $bar.each((index) => {
    const $this = $bar.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);
    const xAxisTitle = $this.data(`x-axis-title`);
    const yAxisTitle = $this.data(`y-axis-title`);
    const marginLeft = $this.data(`margin-left`);
    const marginBottom = $this.data(`margin-bottom`);

    new Bar(`#${id}`, url, xAxisTitle, yAxisTitle, marginLeft, marginBottom).render();
  });
};

export { loadBarStackedCharts };
