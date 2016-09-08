import $ from 'jquery';
import * as d3 from 'd3';
import { TweenLite, CSSPlugin } from 'gsap';
import topojson from 'topojson';
window.$ = $;

class Choropleth {
  constructor(el, shapeUrl, dataUrl, dataObjectsKey, dataValueKey) {
    this.el = el;
    this.width = $(this.el).width();
    this.height = Math.ceil(0.4823 * this.width);
    this.mapWidth = this.width;
    this.shapeUrl = shapeUrl;
    this.dataUrl = dataUrl;
    this.dataObjectsKey = dataObjectsKey;
    this.dataValueKey = dataValueKey;
    this.svg = undefined;
    this.shapeData = undefined;
    this.vizData = undefined;
  }

  init() {
    this.svg = d3.select(this.el).append('svg')
      .attr('width', '100%')
      .attr('height', this.height)
      .attr('class', 'choropleth')
      .append('g');

    this.loadData();
    d3.select(window).on('resize', this.resizeChoropleth.bind(this));
  }

  resizeChoropleth() {
    window.requestAnimationFrame(() => {
      const chart = $(this.el).find('g');
      this.width = $(this.el).width();
      this.height = Math.ceil(0.4823 * this.width);

      TweenLite.set(chart, { scale: this.width / this.mapWidth });
      d3.select('.choropleth').attr('height', this.height);
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

    const countries = topojson.feature(this.shapeData, this.shapeData.objects[this.dataObjectsKey]);
    const projection = d3.geoEquirectangular()
      .fitSize([this.width, this.height], countries);
    const path = d3.geoPath().projection(projection);

    countries.features.forEach((country) => {
      this.vizData.forEach((data) => {
        if (country.id === data.country) {
          country[this.dataValueKey] = Number(data[this.dataValueKey]);
        }
      });
    });

    this.svg.selectAll('.choropleth__item')
      .data(countries.features)
      .enter().append('path')
      .attr('class', 'choropleth__item')
      .attr('id', (d) => d.id, true)
      .attr('d', path);

    this.setData();
  }

  setData() {
    const dataRange = this.getDataRange();

    d3.selectAll('.choropleth__item')
      .attr('fill-opacity', (d) => {
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

const setChoropleths = () => {
  const penetrationChoropleth = new Choropleth('.map', 'data/world-shape-data.json', 'data/internet-penetration.csv', 'countries', 'penetration');
  penetrationChoropleth.init();
};

export { setChoropleths };
