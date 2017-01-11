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
    this.classes = {
      container: `donut__svg-container`,
      svg: `donut__svg`,
      g: `donut__g`,
      arc: `donut__arc`,
      layer: `donut__layer`,
      value: `donut__value`,
      tooltip: `tooltip donut__tooltip`,
    };
    this.legendClasses = {
      donut: `legend legend--donut`,
      item: `legend__item`,
      key: `legend__key`,
      name: `legend__name`,
    };
    this.svg = d3.select(this.el)
      .append(`div`)
        .attr(`class`, this.classes.container)
      .append(`svg`)
        .attr(`class`, this.classes.svg);
    this.svgData = this.svg.append(`g`)
      .attr(`class`, this.classes.g);
    this.tooltip = d3.select(this.el)
      .append(`div`)
      .attr(`class`, this.classes.tooltip);
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

    this.svg.selectAll(`.${this.classes.value}`)
      .attr(`transform`, `translate(${this.radius}, ${this.radius})`);

    if (transition) {
      this.animateChart();
    } else {
      this.svg.selectAll(`.${this.classes.arc}`)
        .attr(`d`, this.arc);
    }

    if (!this.isSingleValue) {
      this.addTooltipEvents();
    }
  }

  addTooltipEvents() {
    this.svg.selectAll(`.${this.classes.arc}`)
      .on(`mouseover`, d => {
        this.tooltip
          .html(`<strong>${d.data[this.dataKeys[0]]}</strong>: ${d.data[this.dataKeys[1]]}`)
          .classed(`is-active`, true);
      })
      .on(`mousemove`, () => {
        this.tooltip
          .style(`top`, `${d3.event.pageY - $(this.el).offset().top}px`)
          .style(`left`, `${d3.event.pageX - $(this.el).offset().left}px`);
      })
      .on(`mouseout`, () => {
        this.tooltip
          .classed(`is-active`, false);
      });
  }

  animateChart() {
    const arcAnimationDuration = 800;

    $(this.el).addClass(`is-active`);

    this.svg.selectAll(`.${this.classes.arc}`)
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

    this.svg.selectAll(`.${this.classes.value}`)
      .transition()
      .duration(300)
      .delay(arcAnimationDuration)
      .style(`opacity`, 1);
  }

  render() {
    if (!this.dataUrl) {
      return false;
    }

    d3.tsv(this.dataUrl, (error, data) => {
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
              .range(constants.colorRange);
      }

      this.svgData.selectAll(`g`)
        .data(this.pie(this.data))
        .enter()
        .append(`g`)
          .attr(`class`, this.classes.layer)
        .append(`path`)
          .attr(`class`, this.classes.arc)
          .attr(`fill`, d => this.color(d.data[this.dataKeys[0]]));

      if (!this.isSingleValue) {
        this.renderLegend();
        d3.select(this.el).attr(`class`, `donut--multi`);
      } else {
        d3.select(this.el).attr(`class`, `donut--single`);

        this.svg.append(`text`)
          .attr(`class`, this.classes.value)
          .style(`font-size`, `58px`)
          .style(`font-weight`, `500`)
          .style(`letter-spacing`, `-0.01em`)
          .style(`fill`, `#000000`)
          .style(`opacity`, 0)
          .attr(`text-anchor`, `middle`)
          .attr(`dominant-baseline`, `middle`)
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
    const $legend = d3.select(this.el).append(`ul`)
      .attr(`class`, this.legendClasses.donut);

    const legendItems = $legend.selectAll(`li`)
      .data(this.color.domain())
      .enter()
      .append(`li`)
        .attr(`class`, this.legendClasses.item);

    legendItems.append(`span`)
      .attr(`class`, this.legendClasses.key)
      .style(`background-color`, this.color);

    legendItems.append(`span`)
        .attr(`class`, this.legendClasses.name)
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
