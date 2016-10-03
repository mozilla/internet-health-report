import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
window.$ = $;

class Area {
  constructor(el, dataUrl, dataIsPercent) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.dataIsPercent = dataIsPercent;
    this.parseDate = d3.timeParse(`%d-%b-%y`);
    this.margin = {top: 20, right: 15, bottom: 30, left: 30};
    this.classes = [`area__svg`, `area__layer`, `x-axis`, `y-axis`];
    this.svg = d3.select(this.el)
      .append(`svg`)
        .attr(`class`, this.classes[0]);
    this.g = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left},${this.margin.top})`);
  }

  setSizes() {
    this.width = $(this.el).width();
    this.height = constants.getWindowWidth() < constants.breakpointM ? 300 : Math.ceil(this.width * 0.52);
    this.innerWidth = this.width - this.margin.left - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.yMax = this.dataIsPercent ? 100 : d3.max(this.data, (d) => d[this.dataKeys[1]]);

    this.x = d3.scaleTime()
      .range([0, this.innerWidth])
      .domain(d3.extent(this.data, (d) => d[this.dataKeys[0]]));
    this.y = d3.scaleLinear()
      .range([this.innerHeight, 0])
      .domain([0, this.yMax]);

    this.axisBottom = constants.getWindowWidth() < constants.breakpointM ? d3.axisBottom(this.x).ticks(4) : d3.axisBottom(this.x);

    this.area = d3.area()
      .x((d) => this.x(d[this.dataKeys[0]]))
      .y0(this.innerHeight)
      .y1((d) => this.y(d[this.dataKeys[1]]));

    this.svg.select(`.${this.classes[1]}`)
      .attr(`d`, this.area);

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
      const self = this;

      this.data = data;
      this.dataKeys = constants.getDataKeys(this.data);
      this.data.forEach((d) => {
        d[this.dataKeys[0]] = self.parseDate(d[this.dataKeys[0]]);
        d[this.dataKeys[1]] = +d[this.dataKeys[1]];
      });

      this.g.append(`path`)
        .datum(this.data)
        .attr(`class`, this.classes[1]);

      this.g.append(`g`)
        .attr(`class`, this.classes[2]);

      this.g.append(`g`)
        .attr(`class`, this.classes[3]);

      this.setSizes();
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

    new Area(`#${id}`, url, isPercent).render();
  });
};

export { loadAreaCharts };
