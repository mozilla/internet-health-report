import * as d3 from 'd3';

const loadTestCharts = () => {
  const dataStatic = [
    {
      salesperson: `Bob`,
      sales: 33,
    },
    {
      salesperson: `Robin`,
      sales: 12,
    },
    {
      salesperson: `Anne`,
      sales: 41,
    },
    {
      salesperson: `Mark`,
      sales: 16,
    }
  ];
  const el = document.getElementById(`chart-test`);
  const dataUrl = el.getAttribute(`data-url`);
  const margin = { top: 20, right: 20, bottom: 30, left: 40 };
  const width = 960 - margin.left - margin.right;
  const height = 500 - margin.top - margin.bottom;
  const x = d3.scaleBand()
            .range([0, width])
            .padding(0.1);
  const y = d3.scaleLinear()
            .range([height, 0]);
  const svg = d3.select(`.chart-test`).append(`svg`)
      .attr(`width`, width + margin.left + margin.right)
      .attr(`height`, height + margin.top + margin.bottom)
    .append(`g`)
      .attr(`transform`, `translate(${margin.left}, ${margin.top})`);

  const renderChart = (data) => {
    data.forEach(d => d.sales = +d.sales);
    x.domain(data.map(d => d.salesperson));
    y.domain([0, d3.max(data, d => d.sales)]);
    svg.selectAll(`.bar`)
      .data(data)
      .enter().append(`rect`)
        .attr(`class`, `bar`)
        .attr(`x`, d => x(d.salesperson))
        .attr(`width`, x.bandwidth())
        .attr(`y`, d => y(d.sales))
        .attr(`height`, d => height - y(d.sales));

    svg.append(`g`)
      .attr(`transform`, `translate(0,${height})`)
      .call(d3.axisBottom(x));

    // add the y Axis
    svg.append("g")
      .call(d3.axisLeft(y));
  };

  d3.tsv(dataUrl, (error, dataDynamic) => {
    if (error) {
      console.log(error);
      renderChart(dataStatic);
      throw error;
    };

    console.log(dataDynamic[0]);
    renderChart(dataDynamic);
  });
};

export { loadTestCharts };