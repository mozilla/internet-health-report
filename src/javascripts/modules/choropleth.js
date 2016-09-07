import * as d3 from 'd3';
import topojson from 'topojson';

class Choropleth {
  constructor(el, width, height, shapeUrl, dataUrl) {
    this.el = el;
    this.width = width;
    this.height = 0.9599 * this.width;
    this.shapeUrl = shapeUrl;
    this.dataUrl = dataUrl;
  }

  init() {
    this.svg = d3.select(this.el).append('svg')
      .attr('width', this.width)
      .attr('height', this.height)
      .append('g');

    this.loadData();
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

    const countries = topojson.feature(this.shapeData, this.shapeData.objects.countries).features;
    const projection = d3.geoMercator()
      .scale(120)
      .translate([this.width / 2, this.height / 2]);
    const path = d3.geoPath().projection(projection);

    countries.forEach((country) => {
      this.vizData.forEach((data) => {
        if (country.id === data.country) {
          country['penetration'] = Number(data.penetration);
        }
      });
    });

    this.svg.selectAll('.choropleth__item')
      .data(countries)
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
        return this.getOpacity(d.penetration, dataRange);
      });
  }

  getDataRange() {
    const dataArray = this.vizData.map((object) => object.penetration);
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
  const penetrationChoropleth = new Choropleth('#map', 800, 450, 'data/world-shape-data.json', 'data/internet-penetration.csv');
  penetrationChoropleth.init();
};

export { setChoropleths };
