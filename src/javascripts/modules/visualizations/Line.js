import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
window.$ = $;

class Line {
  constructor(el, dataUrl) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.parseDate = d3.timeParse('%d-%b-%y');
    this.margin = {top: 20, right: 20, bottom: 30, left: 40};
    this.classes = ['line__svg', 'line__data', 'x-axis', 'y-axis'];
    this.svg = d3.select(this.el)
      .append('svg')
        .attr('class', this.classes[0]);
    this.svgData = this.svg.append('g')
      .attr('transform', `translate(${this.margin.left},${this.margin.top})`);
  }

  setSizes() {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 400 : Math.ceil(this.width * 0.52);
    this.innerWidth = this.width - this.margin.left - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;

    this.x = d3.scaleTime()
      .range([0, this.innerWidth]);

    this.y = d3.scaleLinear()
      .range([this.innerHeight, 0]);

    this.line = d3.line()
      .curve(d3.curveBasis)
      .x((d) => this.x(d.date))
      .y((d) => this.y(d.close));

    this.svg
      .attr('width', this.width)
      .attr('height', this.height);

    this.x.domain(d3.extent(this.data, (d) => d.date));
    this.y.domain([0, d3.max(this.data, (d) => d.close)]);

    this.svg.select(`.${this.classes[1]}`)
      .attr('d', this.line);

    this.svg.select(`.${this.classes[2]}`)
      .attr('transform', `translate(0,${this.innerHeight})`)
      .call(d3.axisBottom(this.x));

    this.svg.select(`.${this.classes[3]}`)
      .call(d3.axisLeft(this.y));
  }

  render() {
    d3.tsv(this.dataUrl, this.type.bind(this), (error, data) => {
      if (error) throw error;

      this.data = data;

      this.svgData.append('path')
        .datum(this.data)
        .attr('class', this.classes[1]);

      // append x-axis
      this.svgData.append('g')
        .attr('class', this.classes[2]);

      // append y-axis
      this.svgData.append('g')
        .attr('class', this.classes[3]);

      this.setSizes();
    });

    $(window).on('resize', this.resize.bind(this));
  }

  resize() {
    this.setSizes();
  }

  type(d) {
    d.date = this.parseDate(d.date);
    d.close = +d.close;
    return d;
  }
};

const loadLineCharts = () => {
  const $line = $('.js-line');

  $line.each((index) => {
    const $this = $line.eq(index);
    const id = $this.attr('id');
    const url = $this.data('url');

    new Line(`#${id}`, url).render();
  });
};

export { loadLineCharts };
