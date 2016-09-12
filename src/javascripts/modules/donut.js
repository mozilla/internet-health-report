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
    this.height = this.width;
    this.svg = null;
    this.radius = Math.min(this.width, this.height) / 2;
    this.color = d3.scaleOrdinal()
      .range(constants.colorRange);
    this.arc = d3.arc()
      .innerRadius((this.radius/10) * 7)
      .outerRadius(this.radius);
    this.pie = d3.pie()
      .value(d => d[dataKey])
      .sort(null);
  }

  init() {
    this.render();
  }

  render() {
    this.svg = d3.select(this.el)
      .append('svg')
      .attr('width', this.width)
      .attr('height', this.height)
      .attr('class', 'donut__svg')
      .append('g')
      .attr('transform', `translate(${this.width / 2}, ${this.height / 2})`);

    const g = this.svg.selectAll('.donut__section')
      .data(this.pie(this.data))
      .enter().append('g')
        .attr('class', 'donut__section');

    g.append('path')
      .attr('class', 'donut__arc')
      .attr('d', this.arc)
      .attr('fill', (d, i) => this.color(d.data.label));

    g.append('text')
      .attr('transform', d => `translate(${this.arc.centroid(d)})`)
      .attr('dy', '.5em')
      .attr('dx', '-.5em')
      .style('font-size', '12px')
      .style('fill', '#fff')
      .text(d => d.data.count);

  }
}

const setDonuts = () => {
  const testDonut = new Donut('#donut', donutData, 'count');
  testDonut.init();
};

export { setDonuts };
