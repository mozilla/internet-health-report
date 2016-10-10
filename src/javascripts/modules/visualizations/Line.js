import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
window.$ = $;

class Line {
  constructor(el, dataUrl) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.parseDate = d3.timeParse(`%d-%b-%y`);
    this.margin = {top: 20, right: 20, bottom: 30, left: 40};
    this.classes = [`line__svg`, `line__data`, `x-axis`, `y-axis`, `line__dot`];
    this.svg = d3.select(this.el).append(`svg`)
      .attr(`class`, this.classes[0]);
    this.svgData = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left},${this.margin.top})`);
  }

  setSizes() {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 300 : Math.ceil(this.width * 0.52);
    this.innerWidth = this.width - this.margin.left - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;

    this.x = d3.scaleTime()
      .range([0, this.innerWidth]);

    this.y = d3.scaleLinear()
      .range([this.innerHeight, 0]);

    this.line = d3.line()
      // .curve(d3.curveBasis)
      .x((d) => this.x(d[this.dataKeys[0]]))
      .y((d) => this.y(d[this.dataKeys[1]]));

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.x.domain([this.minDate - this.xPad, this.maxDate + this.xPad]);
    this.y.domain([d3.min(this.data, (d) => d[this.dataKeys[1]]) - 1, d3.max(this.data, (d) => d[this.dataKeys[1]])]);

    this.svg.select(`.${this.classes[1]}`)
      .attr(`d`, this.line);

    this.svg.selectAll(`.${this.classes[4]}`)
      .attr(`cx`, d => this.x(d[this.dataKeys[0]]))
      .attr(`cy`, d => this.y(d[this.dataKeys[1]]));

    this.axisBottom = constants.getWindowWidth() < constants.breakpointM ? d3.axisBottom(this.x).ticks(4).tickFormat(d3.timeFormat(`%b`)) : d3.axisBottom(this.x).tickFormat(d3.timeFormat(`%b`));

    this.svg.select(`.${this.classes[2]}`)
      .attr(`transform`, `translate(0,${this.innerHeight})`)
      .call(this.axisBottom);

    this.svg.select(`.${this.classes[3]}`)
      .call(d3.axisLeft(this.y));
  }

  render() {
    d3.tsv(this.dataUrl, (error, data) => {
      if (error) {
        throw error;
      }

      this.data = data;
      this.dataKeys = constants.getDataKeys(this.data);

      this.data.forEach(this.type.bind(this));

      this.minDate = d3.min(this.data, d => d[this.dataKeys[0]].getTime());
      this.maxDate = d3.max(this.data, d => d[this.dataKeys[0]].getTime());
      this.xPad = (this.maxDate - this.minDate) * 0.05;

      this.svgData.append(`path`)
        .datum(this.data)
        .attr(`class`, this.classes[1]);

      // append scatterplot dots
      this.svgData.selectAll(this.classes[4])
        .data(this.data)
        .enter().append(`circle`)
          .attr(`class`, this.classes[4])
          .attr(`r`, 4.5);

      // append x-axis
      this.svgData.append(`g`)
        .attr(`class`, this.classes[2]);

      // append y-axis
      this.svgData.append(`g`)
        .attr(`class`, this.classes[3]);

      this.setSizes();
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

    new Line(`#${id}`, url).render();
  });
};

export { loadLineCharts };
