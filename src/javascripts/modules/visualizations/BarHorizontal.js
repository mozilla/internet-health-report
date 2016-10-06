import * as constants from '../constants';
import $ from 'jquery';
import * as d3 from 'd3';
window.$ = $;

class BarHorizontal {
  constructor(el, dataUrl, marginLeft = 40) {
    this.el = el;
    this.dataUrl = dataUrl;
    this.margin = {top: 20, right: 20, bottom: 30, left: marginLeft};
    this.classes = [`bar-horizontal__svg`, `bar-horizontal__data`, `x-axis`, `y-axis`];
    this.svg = d3.select(this.el)
      .append(`svg`)
        .attr(`class`, this.classes[0]);
    this.svgData = this.svg.append(`g`)
      .attr(`transform`, `translate(${this.margin.left},${this.margin.top})`);
  }

  setSizes() {
    this.width = $(this.el).width();
    this.height = 300;
    this.innerWidth = this.width - this.margin.left - this.margin.right;
    this.innerHeight = this.height - this.margin.top - this.margin.bottom;

    this.x = d3.scaleLinear()
      .rangeRound([0, this.innerWidth]);

    this.y = d3.scaleBand()
      .rangeRound([0, this.innerHeight])
      .padding(0.2);

    this.svg
      .attr(`width`, this.width)
      .attr(`height`, this.height);

    this.x.domain([0, d3.max(this.data, d => d[this.dataKeys[1]])]);
    this.y.domain(this.data.map(d => d[this.dataKeys[0]]));

    this.svg.selectAll(`.${this.classes[1]}`)
      .attr(`x`, 0)
      .attr(`y`, d => this.y(d[this.dataKeys[0]]))
      .attr(`height`, this.y.bandwidth())
      .attr(`width`, d => this.x(d[this.dataKeys[1]]));

    this.axisBottom = constants.getWindowWidth() < constants.breakpointM ? d3.axisBottom(this.x).ticks(4) : d3.axisBottom(this.x);

    this.svg.select(`.${this.classes[2]}`)
      .attr(`transform`, `translate(0,${this.innerHeight})`)
      .call(this.axisBottom);

    this.svg.select(`.${this.classes[3]}`)
      .call(d3.axisLeft(this.y))
      .selectAll(`text`)
        .call(this.wrap, this.margin.left);
  }

  render() {
    d3.tsv(this.dataUrl, (error, data) => {
      if (error) {
        throw error;
      }

      this.data = data;
      this.dataKeys = constants.getDataKeys(this.data);
      this.data.forEach(this.type.bind(this));

      this.svgData.selectAll(`.${this.classes[1]}`)
        .data(this.data)
        .enter().append(`rect`)
        .attr(`class`, this.classes[1]);

      // append x-axis
      this.svgData.append(`g`)
        .attr(`class`, this.classes[2]);

      // append y-axis
      this.svgData.append(`g`)
        .attr(`class`, this.classes[3]);

      this.setSizes();
    });

    $(window).on(`resize`, this.resize.bind(this));
  }

  resize() {
    this.setSizes();
  }

  type(d) {
    d[this.dataKeys[1]] = +d[this.dataKeys[1]];
    return d;
  }

  wrap(text, width) {
    text.each(function() {
      const textEl = d3.select(this);
      const words = textEl.text().split(/\s+/).reverse();
      const lineHeight = 1.4;
      const y = textEl.attr(`y`);
      const dy = parseFloat(textEl.attr(`dy`));
      const yAxisOffset = 10;
      let word;
      let textHeight;
      let line = [];
      let lineLength = 0;
      let lineNumber = 0;
      let tspan = textEl.text(null)
        .append(`tspan`)
        .attr(`x`, 0)
        .attr(`y`, y)
        .attr(`dy`, dy + `em`);

      while (words.length > 0) {
        word = words.pop();
        line.push(word);
        tspan.text(line.join(` `));

        if (tspan.node().getComputedTextLength() > width - yAxisOffset) {
          lineLength++;
          line.pop();
          tspan.text(line.join(` `));
          line = [word];
          tspan = textEl
            .append(`tspan`)
            .attr(`x`, 0)
            .attr(`y`, y)
            .attr(`dy`, ++lineNumber * lineHeight + dy + `em`)
            .text(word);
        }
      }

      textHeight = textEl.node().getBBox().height;
      textEl.attr(`transform`, `translate(${-yAxisOffset},${(textHeight / (lineLength + 1)) * (lineLength * -0.5)})`);
    });
  }
}

const loadBarHorizontalCharts = () => {
  const $barHorizontal = $(`.js-bar-horizontal`);

  $barHorizontal.each((index) => {
    const $this = $barHorizontal.eq(index);
    const id = $this.attr(`id`);
    const url = $this.data(`url`);
    const marginLeft = $this.data(`margin-left`);

    new BarHorizontal(`#${id}`, url, marginLeft).render();
  });
};

export { loadBarHorizontalCharts };
