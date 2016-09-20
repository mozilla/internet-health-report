import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import { TweenLite } from 'gsap';
import topojson from 'topojson';
window.$ = $;

class Choropleth {
  constructor(el, dataUrl, dataValueKey, title) {
    this.el = el;
    this.aspectRatio = 0.6663;
    this.width = $(this.el).width();
    this.height = Math.ceil(this.aspectRatio * this.width);
    this.mapWidth = this.width;
    this.shapeUrl = `${constants.homeURL}/data/world-shape-data.json`;
    this.dataUrl = dataUrl;
    this.dataValueKey = dataValueKey;
    this.title = title;
    this.svg = undefined;
    this.shapeData = undefined;
    this.vizData = undefined;
  }

  render() {
    this.svg = d3.select(this.el).append(`svg`)
      .attr(`width`, `100%`)
      .attr(`height`, this.height)
      .attr(`class`, `choropleth__svg`)
      .append(`g`);

    this.loadData();
    $(window).on(`resize`, this.resizeChoropleth.bind(this));
  }

  resizeChoropleth() {
    window.requestAnimationFrame(() => {
      const chart = $(this.el).find(`g`);

      this.width = $(this.el).width();
      this.height = Math.ceil(this.aspectRatio * this.width);

      TweenLite.set(chart, { scale: this.width / this.mapWidth });
      d3.select(`.choropleth__svg`).attr(`height`, this.height);
    });
  }

  loadData() {
    d3.queue()
      .defer(d3.json, this.shapeUrl)
      .defer(d3.csv, this.dataUrl)
      .await(this.drawMap.bind(this));
  }

  drawMap(error, shapeData, vizData) {
    if (error) {
      return console.error(error);
    }

    this.shapeData = shapeData;
    this.vizData = vizData;

    const countries = topojson.feature(this.shapeData, this.shapeData.objects[`countries`]);
    const projection = d3.geoMercator()
      .fitSize([this.width, this.height], countries);
    const path = d3.geoPath()
      .projection(projection);

    countries.features.forEach((country) => {
      this.vizData.forEach((data) => {
        if (country.id === data.country) {
          country[this.dataValueKey] = Number(data[this.dataValueKey]);
        }
      });
    });

    this.svg.selectAll(`.choropleth__item`)
      .data(countries.features)
      .enter().append(`path`)
      .attr(`class`, `choropleth__item`)
      .attr(`id`, (d) => d.id, true)
      .attr(`d`, path);

    this.setData();
    this.drawLegend();
  }

  drawLegend() {
    const dataRange = this.getDataRange();
    const min = dataRange[0];
    const max = dataRange[1];
    const legendString = `<div class="legend">` +
      `<p class="legend__value">` + Math.floor((min / 1) * 100) + `%</p>` +
      `<div class="legend__scale"></div>` +
      `<p class="legend__value">` + Math.floor((max / 1) * 100) + `%</p>` +
    `</div>`;

    $(this.el).append(legendString);
  }

  setData() {
    const dataRange = this.getDataRange();

    d3.selectAll(`.choropleth__item`)
      .attr(`fill-opacity`, (d) => {
        return this.getOpacity(d[this.dataValueKey], dataRange);
      });
  }

  getDataRange() {
    const dataArray = this.vizData.map((object) => object[this.dataValueKey]);
    const min = Math.min(...dataArray);
    const max = Math.max(...dataArray);

    return [min, max];
  }

  getOpacity(value, valueRange) {
    const opacity = d3.scaleLinear()
      .domain([valueRange[0], valueRange[1]])
      .range([0.1, 1]);

    return opacity(value);
  }
}

const loadChoropleths = () => {
  const $choropleth = $(`.js-choropleth`);

  $choropleth.each((index) => {
    const $this = $choropleth.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);
    const value = $this.data(`value`);
    const title = $this.data(`title`);

    new Choropleth(`#${id}`, url, value, title).render();
  });
};

export { loadChoropleths };
