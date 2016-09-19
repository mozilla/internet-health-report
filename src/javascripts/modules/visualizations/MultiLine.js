import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
window.$ = $;

class MultiLine {
  constructor(el, dataUrl) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.parseDate = d3.timeParse('%Y%m%d');
    this.margin = {top: 20, right: 20, bottom: 30, left: 40};
    this.classes = ['multiline__svg', 'multiline__layer', 'multiline__dataset'];
    this.svg = d3.select(this.el)
      .append('svg')
        .attr('class', this.classes[0]);
    this.g = this.svg.append('g')
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
    this.z = d3.scaleOrdinal(d3.schemeCategory10);
    this.line = d3.line()
      .curve(d3.curveBasis)
      .x((d) => this.x(d.date))
      .y((d) => this.y(d.temperature));
    this.svg
      .attr('width', this.width)
      .attr('height', this.height);
  }

  render() {
    this.setSizes();

    d3.tsv(this.dataUrl, this.type.bind(this), (error, data) => {
      if (error) throw error;

      this.data = data;
      this.dataColumns = data.columns.slice(1).map((id) => {
        return {
          id: id,
          values: data.map((d) => {
            return {
              date: d.date,
              temperature: d[id]
            }
          })
        }
      });

      this.x.domain(d3.extent(this.data, (d) => d.date));
      this.y.domain([
        d3.min(this.dataColumns, (c) => d3.min(c.values, (d) => d.temperature)),
        d3.max(this.dataColumns, (c) => d3.max(c.values, (d) => d.temperature))
      ]);
      this.z.domain(this.dataColumns.map((c) => c.id));

      this.g.append('g')
        .attr('class', 'axis axis--x')
        .attr('transform', `translate(0,${this.innerHeight})`)
        .call(d3.axisBottom(this.x));

      this.g.append('g')
        .attr('class', 'axis axis--y')
        .call(d3.axisLeft(this.y));

      const city = this.g.selectAll(`.${this.classes[2]}`)
        .data(this.dataColumns)
        .enter().append('g')
          .attr('class', this.classes[2]);

      city.append('path')
        .attr('class', this.classes[1])
        .attr('d', (d) => this.line(d.values))
        .style('stroke', (d) => this.z(d.id));
    });

    $(window).on('resize', this.resize.bind(this));
  }

  resize() {
    this.setSizes();

    this.x.domain(d3.extent(this.data, (d) => d.date));
    this.y.domain([
      d3.min(this.dataColumns, (c) => d3.min(c.values, (d) => d.temperature)),
      d3.max(this.dataColumns, (c) => d3.max(c.values, (d) => d.temperature))
    ]);
    this.z.domain(this.dataColumns.map((c) => c.id));

    this.svg.select('.axis--x')
      .attr('transform', `translate(0,${this.innerHeight})`)
      .call(d3.axisBottom(this.x));

    this.svg.select('.axis--y')
      .call(d3.axisLeft(this.y));

    this.svg.selectAll(`.${this.classes[1]}`)
      .attr('d', (d) => this.line(d.values));
  }

  type(d, _, columns) {
    d.date = this.parseDate(d.date);
    for (let i = 1, n = columns.length, c; i < n; i++) {
      d[c = columns[i]] = +d[c];
    }
    return d;
  }
};

const loadMultiLineCharts = () => {
  const $line = $('.js-multiline');

  $line.each((index) => {
    const $this = $line.eq(index);
    const id = $this.attr('id');
    const url = $this.data('url');

    new MultiLine(`#${id}`, url).render();
  });
};

export { loadMultiLineCharts };
