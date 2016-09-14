import * as constants from './constants';
import $ from 'jquery';
import * as d3 from 'd3';
import { TweenLite, CSSPlugin } from 'gsap';
import topojson from 'topojson';
window.$ = $;

const donutData = [
  { label: 'Abulia', count: 10 },
  { label: 'Betelgeuse', count: 20 },
  { label: 'Cantaloupe', count: 30 },
  { label: 'Dijkstra', count: 40 }
];

class Donut {
  constructor(el, data, dataKey) {
    this.el = el;
    this.data = data;
    this.width = $(this.el).width();
    this.radius = Math.min(this.width) * 0.5;
    this.arc = d3.arc()
      .innerRadius((this.radius/10) * 7)
      .outerRadius(this.radius);
    this.pie = d3.pie()
      .value(d => d[dataKey])
      .sort(null);
    this.legendRectSize = 18;
    this.legendSpacing = 4;
    this.legendHeight = this.legendRectSize + this.legendSpacing;
    this.color = d3.scaleOrdinal()
      .range(constants.colorRange);
    this.classes = ['donut__svg', 'donut__g', 'donut__arc', 'donut__text', 'donut__legend'];
  }

  render() {
    const donut = d3.select(this.el)
      .append('svg')
        .attr('width', this.width)
        .attr('height', this.width)
        .attr('class', this.classes[0])
      .append('g')
        .attr('class', this.classes[1])
        .attr('transform', `translate(${this.radius}, ${this.radius})`);

    const g = donut.selectAll('g')
      .data(this.pie(this.data))
      .enter().append('g');

    g.append('path')
      .attr('class', this.classes[2])
      .attr('d', this.arc)
      .attr('fill', (d, i) => this.color(d.data.label));

    g.append('text')
      .attr('class', this.classes[3])
      .attr('transform', d => `translate(${this.arc.centroid(d)})`)
      .attr('dy', '.5em')
      .attr('dx', '-.8em')
      .style('font-size', '12px')
      .text(d => `${d.data.count}%`);

    this.renderLegend();

    $(window).on('resize', this.resize.bind(this));
  }

  renderLegend() {
    const donut = d3.select(this.el);

    this.height = this.width + (this.color.domain().length * this.legendHeight);

    donut.select(`.${this.classes[0]}`)
      .attr('height', this.height);

    const legend = donut.select(`.${this.classes[0]}`).selectAll(`.${this.classes[4]}`)
      .data(this.color.domain())
      .enter()
      .append('g')
        .attr('class', this.classes[4])
        .attr('transform', (d, i) => {
          const horz = 0;
          const vert = this.width + (i * this.legendHeight);
          return 'translate(' + horz + ',' + vert + ')';
        });

    legend.append('rect')
      .attr('width', this.legendRectSize)
      .attr('height', this.legendRectSize)
      .style('fill', this.color)
      .style('stroke', this.color);

    legend.append('text')
      .attr('x', this.legendRectSize + this.legendSpacing)
      .attr('y', this.legendRectSize - this.legendSpacing)
      .style('font-size', '12px')
      .text(function(d) { return d; });
  }

  resize() {
    const donut = d3.select(this.el);

    this.width = $(this.el).width();
    this.height = this.width + (this.color.domain().length * this.legendHeight);
    this.radius = Math.min(this.width) / 2;
    this.arc = d3.arc()
      .innerRadius((this.radius/10) * 7)
      .outerRadius(this.radius);

    donut.select(`.${this.classes[0]}`)
      .attr('width', this.width)
      .attr('height', this.height);

    donut.select(`.${this.classes[1]}`)
      .attr('transform', `translate(${this.radius}, ${this.radius})`);

    donut.selectAll(`.${this.classes[2]}`)
      .attr('d', this.arc);

    donut.selectAll(`.${this.classes[3]}`)
      .attr('transform', d => `translate(${this.arc.centroid(d)})`);

    donut.selectAll(`.${this.classes[4]}`)
      .attr('transform', (d, i) => {
        const horz = 0;
        const vert = this.width + (i * this.legendHeight);
        return 'translate(' + horz + ',' + vert + ')';
      });
  }
}

const loadDonuts = () => {
  const $donuts = $('.js-donut');

  $donuts.each((index) => {
    const $this = $donuts.eq(index);
    const id = $this.attr('id');
    const url = $this.data('url');
    const value = $this.data('value');

    new Donut(`#${id}`, donutData, value).render();
  });
};

export { loadDonuts };
