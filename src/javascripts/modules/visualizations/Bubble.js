import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
window.$ = $;

class Bubble {
  constructor(el, dataUrl) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.classes = [`bubble__svg`, `bubble__layer`, `bubble__node`];
    this.color = d3.scaleOrdinal()
      .range(constants.colorRange);
  }

  render() {
    d3.csv(this.dataUrl, (error, data) => {
      if (error) {
        throw error;
      }

      this.formatData(data);

      this.drawChart();

      $(window).on(`resize`, this.resize.bind(this));
    });
  }

  drawChart() {
    this.width = $(this.el).width();
    this.svg = d3.select(this.el)
      .append(`svg`)
        .attr(`class`, this.classes[0]);
    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.width);

    this.bubble = d3.pack(this.data)
      .size([this.width, this.width])
      .padding(1.5);

    this.nodes = d3.hierarchy(this.data)
      .sum((d) => d[this.dataKeys[1]]);

    this.node = this.svg.selectAll(`.${this.classes[2]}`)
      .data(this.bubble(this.nodes).descendants())
      .enter()
      .filter((d) => !d.children)
      .append(`g`)
      .attr(`class`, this.classes[2])
      .attr(`transform`, (d) => `translate(${d.x},${d.y})`);

    this.node.append(`circle`)
      .attr(`r`, (d) => d.r)
      .style(`fill`, (d) => this.color(d[this.dataKeys[1]]));

    this.node.append(`text`)
      .attr(`dy`, `.3em`)
      .style(`text-anchor`, `middle`)
      .style(`font-size`, constants.getWindowWidth() < constants.breakpointM ? `12px` : `16px`)
      .text((d) => d.data[this.dataKeys[0]]);
  }

  resize() {
    $(this.el).empty();
    this.drawChart();
  }

  formatData(data) {
    let formattedData = { children: [] };

    this.setDataKeys(data);

    data.forEach((d) => {
      d[this.dataKeys[1]] = parseInt(d[this.dataKeys[1]], 10);
      formattedData.children.push(d);
    });

    this.data = formattedData;
  }

  setDataKeys(data) {
    this.dataKeys = [];

    for (let prop in data[0]) {
      if ({}.hasOwnProperty.call(data[0], prop)) {
        this.dataKeys.push(prop);
      }
    }

    this.dataTitle = this.dataKeys[0];
    this.dataKey = this.dataKeys[1];
  }
}

var loadBubbleCharts = () => {
  const $bubble = $(`.js-bubble`);

  $bubble.each((index) => {
    const $this = $bubble.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);

    new Bubble(`#${id}`, url).render();
  });
};

export { loadBubbleCharts };
