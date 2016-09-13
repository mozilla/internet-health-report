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
    this.color = d3.scaleOrdinal()
      .range(constants.colorRange);
    this.arc = d3.arc()
      .innerRadius((this.radius/10) * 7)
      .outerRadius(this.radius);
    this.pie = d3.pie()
      .value(d => d[dataKey])
      .sort(null);
    this.classes = ['donut__svg', 'donut__g', 'donut__arc', 'donut__text'];
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
      .style('fill', '#fff')
      .text(d => `${d.data.count}%`);

    $(window).on('resize', this.resize.bind(this));
  }

  resize() {
    const donut = d3.select(this.el);

    this.width = $(this.el).width();
    this.radius = Math.min(this.width) / 2;
    this.arc = d3.arc()
      .innerRadius((this.radius/10) * 7)
      .outerRadius(this.radius);

    donut.select(`.${this.classes[0]}`)
      .attr('width', this.width)
      .attr('height', this.width);

    donut.select(`.${this.classes[1]}`)
      .attr('transform', `translate(${this.radius}, ${this.radius})`);

    donut.selectAll(`.${this.classes[2]}`)
      .attr('d', this.arc);

    donut.selectAll(`.${this.classes[3]}`)
      .attr('transform', d => `translate(${this.arc.centroid(d)})`);
  }
}

const setDonuts = () => {
  new Donut('#donut', donutData, 'count').render();
};

export { setDonuts };
