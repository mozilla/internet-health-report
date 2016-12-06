/* global Waypoint */
import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
import '../../plugins/noframework.waypoints';
window.$ = $;

class Donut {
  constructor(el, dataUrl) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.legendRectSize = 18;
    this.legendSpacing = 4;
    this.legendHeight = this.legendRectSize + this.legendSpacing;
    this.classes = [`donut__svg-container`,`donut__svg`, `donut__g`, `donut__arc`, `donut__text`, `donut__layer`, `donut__value`];
    this.legendClasses = [`legend legend--donut`, `legend__item`, `legend__key`, `legend__name`];
    this.svg = d3.select(this.el)
      .append(`div`)
        .attr(`class`, this.classes[0])
      .append(`svg`)
        .attr(`class`, this.classes[1]);
    this.svgData = this.svg.append(`g`)
      .attr(`class`, this.classes[2]);
  }

  setSizes(transition = false) {
    this.width = $(this.el).width();
    this.radius = Math.min(this.width) * 0.5;

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.width);

    if (this.isSingleValue) {
      this.arc = d3.arc()
        .innerRadius(this.radius * 0.9)
        .outerRadius(this.radius);
    } else {
      this.arc = d3.arc()
        .innerRadius(this.radius * 0.6)
        .outerRadius(this.radius);
    }

    this.svgData
      .attr(`transform`, `translate(${this.radius}, ${this.radius})`);

    this.svg.selectAll(`.${this.classes[6]}`)
      .attr(`transform`, `translate(${this.radius}, ${this.radius})`);

    if (transition) {
      this.animateChart();
    } else {
      this.svg.selectAll(`.${this.classes[3]}`)
        .attr(`d`, this.arc);
    }
  }

  animateChart() {
    const arcAnimationDuration = 800;

    $(this.el).addClass(`is-active`);

    this.svg.selectAll(`.${this.classes[3]}`)
      .transition()
        .duration(arcAnimationDuration)
        .ease(d3.easeCubicOut)
        .attrTween(`d`, (d) => {
          const i = d3.interpolate(d.startAngle, d.endAngle);

          return (t) => {
            d.endAngle = i(t);
            return this.arc(d);
          };
        });

    this.svg.selectAll(`.${this.classes[6]}`)
      .transition()
      .duration(300)
      .delay(arcAnimationDuration)
      .style(`opacity`, 1);
  }

  render() {
    d3.csv(this.dataUrl, (error, data) => {
      if (error) {
        throw error;
      }

      this.data = data;
      this.dataKeys = constants.getDataKeys(this.data);

      this.data.forEach(this.type.bind(this));

      this.isSingleValue = this.data.length === 1;

      this.pie = d3.pie()
        .value(d => d[this.dataKeys[1]])
        .sort(null);

      if (this.isSingleValue) {
        const value = this.data[0][this.dataKeys[1]];
        const newData = {};

        newData[this.dataKeys[0]] = `other`;
        newData[this.dataKeys[1]] = 100 - value;
        this.data.push(newData);
        this.color = d3.scaleOrdinal()
              .range(constants.colorRangeDonut);
      } else {
        this.color = d3.scaleOrdinal()
              .range(constants.colorRangeDonutMulti);
      }

      this.svgData.selectAll(`g`)
        .data(this.pie(this.data))
        .enter()
        .append(`g`)
          .attr(`class`, this.classes[5])
        .append(`path`)
          .attr(`class`, this.classes[3])
          .attr(`fill`, d => this.color(d.data[this.dataKeys[0]]));

      if (!this.isSingleValue) {
        this.renderLegend();
        d3.select(this.el).attr(`class`, `donut--multi`);
      } else {
        d3.select(this.el).attr(`class`, `donut--single`);

        this.svg.append(`text`)
          .attr(`class`, this.classes[6])
          .style(`font-size`, `58px`)
          .style(`font-weight`, `500`)
          .style(`letter-spacing`, `-0.01em`)
          .style(`fill`, `#000000`)
          .style(`opacity`, 0)
          .attr(`text-anchor`, `middle`)
          .attr(`alignment-baseline`, `central`)
          .text(`${this.data[0][this.dataKeys[1]]}%`);
      }

      const waypoint = new Waypoint({
        element: document.getElementById(this.el.substr(1)),
        handler: () => {
          this.setSizes(true);
          waypoint.destroy();
        },
        offset: `50%`,
      });

      $(window).on(`resize`, this.resize.bind(this));
    });
  }

  type(d) {
    d[this.dataKeys[1]] = +d[this.dataKeys[1]];
    return d;
  }

  renderLegend() {
    const $legend = d3.select(this.el).insert(`ul`, `:first-child`)
      .attr(`class`, this.legendClasses[0]);

    const legendItems = $legend.selectAll(`li`)
      .data(this.color.domain())
      .enter()
      .append(`li`)
        .attr(`class`, this.legendClasses[1]);

    legendItems.append(`span`)
      .attr(`class`, this.legendClasses[2])
      .style(`background-color`, this.color);

    legendItems.append(`span`)
        .attr(`class`, this.legendClasses[3])
        .text(d => d);
  }

  resize() {
    this.setSizes();
  }
}

const loadDonuts = () => {
  const $donuts = $(`.js-donut`);

  $donuts.each((index) => {
    const $this = $donuts.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);

    new Donut(`#${id}`, url).render();
  });
};

export { loadDonuts };
