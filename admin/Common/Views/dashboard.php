(() => {
  'use strict';

  // Add your Highcharts configuration here
  const config = {
    title: {
      text: 'My Chart'
    },
    series: [{
      data: [1, 2, 3, 4, 5]
    }]
  };

  try {
    // Initialize Highcharts chart
    Highcharts.chart('container', config);
  } catch (error) {
    // Handle errors
    console.error(error);
  }
})();

