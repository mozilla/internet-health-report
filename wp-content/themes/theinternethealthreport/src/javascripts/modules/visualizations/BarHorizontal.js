/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import '../../plugins/noframework.waypoints';
window.$ = $;

class BarHorizontal {
  constructor(el, dataUrl, marginLeft = 40, maxDataValue = 0, xAxisTitle, yAxisTitle) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.xAxisTitle = xAxisTitle;
    this.yAxisTitle = yAxisTitle;
    this.maxDataValue = maxDataValue;
    this.margin = {top: 20, right: 30, bottom: 80, left: marginLeft, leftLabel: 30};
    this.barHeight = 90;
    this.classes = {
      barHorizontalSvg: `bar-horizontal__svg`,
      barHorizontalData: `bar-horizontal__data`,
      barHorizontalValue: `bar-horizontal__value`,
      xAxis: `x-axis`,
      xAxisTitle: `x-axis-title`,
      yAxis: `y-axis`,
      yAxisTitle: `y-axis-title`,
      gridY: `grid-y`,
      gridX: `grid-x`,
    };
    this.svg = d3.select(this.el)
      .append(`svg`)
        .attr(`class`, this.classes.barHorizontalSvg);
    this.svgData = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left + this.margin.leftLabel + 6},${this.margin.top})`);
  }

  setSizes(transition = false) {
    this.width = $(this.el).width();
    this.height = this.chartHeight;
    this.innerWidth = this.width - this.margin.left - this.margin.leftLabel - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;

    if (this.maxDataValue > 0) {
      this.xDomainMax = this.maxDataValue;
    } else {
      this.xDomainMax = d3.max(this.data, d => d[this.dataKeys[1]]);
    }

    this.x = d3.scaleLinear()
      .domain([0, this.xDomainMax])
      .rangeRound([0, this.innerWidth]);

    this.y = d3.scaleBand()
      .domain(this.data.map(d => d[this.dataKeys[0]]))
      .rangeRound([0, this.innerHeight])
      .padding(0.4);

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.svg.selectAll(`.${this.classes.barHorizontalData}`)
      .attr(`x`, 0)
      .attr(`y`, d => this.y(d[this.dataKeys[0]]))
      .attr(`width`, d => this.x(d[this.dataKeys[1]]))
      .attr(`height`, this.y.bandwidth());

    this.svg.selectAll(`.${this.classes.barHorizontalValue}`)
      .attr(`x`, d => this.x(d[this.dataKeys[1]]) - 8)
      .attr(`y`, d => this.y(d[this.dataKeys[0]]) + (this.barHeight * 0.311));

    this.setAxes();

    if (transition) {
      this.animateChart();
    }
  }

  animateChart() {
    const x = this.x;
    const valueKey = this.dataKeys[1];

    $(this.el).addClass(`is-active`);

    this.svg.selectAll(`.${this.classes.barHorizontalData}`)
      .attr(`width`, 0)
      .style(`opacity`, 1);

    this.svg.selectAll(`.${this.classes.barHorizontalData}`)
      .transition()
        .delay((d, i) => constants.chartFadeIn + (i * 150))
        .ease(d3.easeExpOut)
        .duration(750)
        .attr(`width`, (d) => {
          return x(d[valueKey]);
        });

    this.svgData.selectAll(`.${this.classes.barHorizontalValue}`)
      .transition()
        .delay((d, i) => constants.chartFadeIn + (i * 150) + 750)
        .ease(d3.easeExpOut)
        .duration(300)
        .style(`opacity`, 1);
  }

  setAxes() {
    if (this.el === `#chart-188`) {
      this.axisBottom = constants.getWindowWidth() < constants.breakpointM ? d3.axisBottom(this.x).ticks(5) : d3.axisBottom(this.x).ticks(10);
    } else {
      this.axisBottom = constants.getWindowWidth() < constants.breakpointM ? d3.axisBottom(this.x).ticks(4) : d3.axisBottom(this.x);
    }

    this.svg.select(`.${this.classes.xAxis}`)
      .attr(`transform`, `translate(${this.margin.left + this.margin.leftLabel + 6}, ${this.innerHeight + this.margin.top})`)
      .call(this.axisBottom
        .tickFormat(this.tickFormat));

    this.svg.select(`.${this.classes.yAxis}`)
      .attr(`transform`, `translate(${this.margin.left + this.margin.leftLabel + 6}, ${this.margin.top})`)
      .call(d3.axisLeft(this.y))
      .selectAll(`text`)
        .call(this.wrap, this.margin.left);

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

    console.log(this.dataUrl);
    d3.tsv(this.dataUrl, (error, data) => {
      if (error) {
        console.log(data);
        console.log(`d3.tsv error`);
        throw error;
      }

      this.data = data;
      this.dataKeys = constants.getDataKeys(this.data);
      this.data.forEach(this.type.bind(this));

      // append x-axis
      this.svg.append(`g`)
        .attr(`class`, this.classes.xAxis);

      // append y-axis
      this.svg.append(`g`)
        .attr(`class`, this.classes.yAxis);

      // append y-axis gridlines
      this.svgData.append(`g`)
        .attr(`class`, this.classes.gridY);

      // append x-axis gridlines
      this.svgData.append(`g`)
        .attr(`class`, this.classes.gridX);

      // add data bars
      this.svgData.selectAll(`.${this.classes.barHorizontalData}`)
        .data(this.data)
        .enter().append(`rect`)
        .attr(`class`, this.classes.barHorizontalData)
        .style(`opacity`, 0);

      // add data values to bars
      this.svgData.selectAll(`.${this.classes.barHorizontalValue}`)
        .data(this.data)
        .enter().append(`text`)
        .attr(`class`, this.classes.barHorizontalValue)
        .text(d => {
          const value = d[this.dataKeys[1]];
          let formattedValue;

          if (value >= 1000000000) {
            formattedValue = `${value / 1000000000}b`;
          } else if (value >= 1000000) {
            formattedValue = `${value / 1000000}m`;
          } else {
            formattedValue = value;
          }

          return formattedValue;
        })
        .attr(`text-anchor`, `end`)
        .style(`opacity`, 0);

      // set chart height based on data length
      this.chartHeight = this.data.length * this.barHeight;

      // Add titles to the axes
      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.xAxisTitle)
        .text(this.xAxisTitle);

      this.svg.append(`text`)
        .attr(`text-anchor`, `middle`)
        .attr(`class`, this.classes.yAxisTitle)
        .text(this.yAxisTitle);

      this.setTickFormat();
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

  setTickFormat() {
    let dataValues = this.data.map((obj) => {
      return obj[this.dataKeys[1]];
    });

    const minDataValue = Math.min(...dataValues);

    if (minDataValue >= 1000000) {
      this.tickFormat = d3.formatPrefix(`.0`, 1e6);
    } else {
      this.tickFormat = d3.formatPrefix(`.0`, 10);
    }
  }

  resize() {
    this.setSizes();
  }

  type(d) {
    d[this.dataKeys[1]] = +d[this.dataKeys[1]];
    return d;
  }

  wrap(text, width) {
    text.each(function() {
      const textEl = d3.select(this);
      const words = textEl.text().split(/\s+/).reverse();
      const lineHeight = 1.4;
      const y = textEl.attr(`y`);
      const dy = parseFloat(textEl.attr(`dy`));
      const yAxisOffset = 10;
      let word;
      let textHeight;
      let line = [];
      let lineLength = 0;
      let lineNumber = 0;
      let tspan = textEl.text(null)
        .append(`tspan`)
        .attr(`x`, 0)
        .attr(`y`, y)
        .attr(`dy`, dy + `em`);

      while (words.length > 0) {
        word = words.pop();
        line.push(word);
        tspan.text(line.join(` `));

        if (tspan.node().getComputedTextLength() > width - yAxisOffset) {
          lineLength++;
          line.pop();
          tspan.text(line.join(` `));
          line = [word];
          tspan = textEl
            .append(`tspan`)
            .attr(`x`, 0)
            .attr(`y`, y)
            .attr(`dy`, ++lineNumber * lineHeight + dy + `em`)
            .text(word);
        }
      }

      textHeight = textEl.node().getBBox().height;
      textEl.attr(`transform`, `translate(${-yAxisOffset},${(textHeight / (lineLength + 1)) * (lineLength * -0.5)})`);
    });
  }
}

const loadBarHorizontalCharts = () => {
  const $barHorizontal = $(`.js-bar-horizontal`);

  $barHorizontal.each((index) => {
    const $this = $barHorizontal.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);
    const marginLeft = $this.data(`margin-left`);
    const maxDataValue = $this.data(`percentage`);
    const xAxisTitle = $this.data(`x-axis-title`);
    const yAxisTitle = $this.data(`y-axis-title`);

    new BarHorizontal(`#${id}`, url, marginLeft, maxDataValue, xAxisTitle, yAxisTitle).render();
  });
};

export { loadBarHorizontalCharts };
