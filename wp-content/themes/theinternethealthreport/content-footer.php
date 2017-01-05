<?php wp_footer(); ?>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script>
  var dataStatic = [
    {
      salesperson: 'Bob',
      sales: 33,
    },
    {
      salesperson: 'Robin',
      sales: 12,
    },
    {
      salesperson: 'Anne',
      sales: 41,
    },
    {
      salesperson: 'Mark',
      sales: 16,
    }
  ];
  var el = document.getElementById('chart-test');
  var dataUrl = el.getAttribute('data-url');
  var margin = { top: 20, right: 20, bottom: 30, left: 40 };
  var width = 960 - margin.left - margin.right;
  var height = 500 - margin.top - margin.bottom;
  var x = d3.scaleBand()
            .range([0, width])
            .padding(0.1);
  var y = d3.scaleLinear()
            .range([height, 0]);
  var svg = d3.select('.chart-test').append('svg')
      .attr('width', width + margin.left + margin.right)
      .attr('height', height + margin.top + margin.bottom)
    .append('g')
      .attr('transform', 'translate(' + margin.left + ', ' + margin.top + ')');

  var renderChart = function(data) {
    data.forEach(function(d) { return d.sales = +d.sales; });
    x.domain(data.map(function(d) { return d.salesperson; }));
    y.domain([0, d3.max(data, function(d) { return d.sales; })]);
    svg.selectAll('.bar')
      .data(data)
      .enter().append('rect')
        .attr('class', 'bar')
        .attr('x', function(d) { return x(d.salesperson); })
        .attr('width', x.bandwidth())
        .attr('y', function(d) { return y(d.sales); })
        .attr('height', function(d) { return height - y(d.sales); });

    svg.append('g')
      .attr('transform', 'translate(0, ' + height + ')')
      .call(d3.axisBottom(x));

    // add the y Axis
    svg.append("g")
      .call(d3.axisLeft(y));
  };

  // d3.tsv(dataUrl, function(error, dataDynamic) {
  //   if (error) {
  //     // renderChart(dataStatic);
  //     JSON.stringify(error);
  //     throw error;
  //   };

  //   renderChart(dataDynamic);
  // });

  d3.request(dataUrl)
    .mimeType("text/tab-separated-values")
    .response(function(xhr) {
      console.log('response');
      console.log(xhr);
      return d3.tsvParse(xhr.responseText);
    })
    .get(function(error, dataDynamic) {
      if (error) {
        // renderChart(dataStatic);
        console.log(error);
        throw error;
      };

      renderChart(dataDynamic);
    });
</script>
</body>
</html>