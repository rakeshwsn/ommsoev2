<div id="chart_div" style="height: 500px;"></div>
<script>
    google.charts.load('current', { packages: ['corechart', 'bar'] });
    google.charts.setOnLoadCallback(drawMaterial);

    function drawMaterial() {
        var data = new google.visualization.DataTable();
        data.addColumn('timeofday', 'Name Of District');
        data.addColumn('number', 'Target In Hectare');
        data.addColumn('number', 'Achievement In Hectare');

        data.addRows([
            [{ v: [8, 0, 0], f: '8 am' }, 1, .25],
            [{ v: [9, 0, 0], f: '9 am' }, 2, .5],
            [{ v: [10, 0, 0], f: '10 am' }, 3, 1],
            [{ v: [11, 0, 0], f: '11 am' }, 4, 2.25],
            [{ v: [12, 0, 0], f: '12 pm' }, 5, 2.25],
            [{ v: [13, 0, 0], f: '1 pm' }, 6, 3],
            [{ v: [14, 0, 0], f: '2 pm' }, 7, 4],
            [{ v: [15, 0, 0], f: '3 pm' }, 8, 5.25],
            [{ v: [16, 0, 0], f: '4 pm' }, 9, 7.5],
            [{ v: [17, 0, 0], f: '5 pm' }, 10, 10],
        ]);

        var options = {
            title: 'Dist wise Target vs Achievement',
            hAxis: {
                title: 'Time of Day',
                format: 'h:mm a',
                viewWindow: {
                    min: [7, 30, 0],
                    max: [17, 30, 0]
                }
            },
            vAxis: {
                title: 'Rating (scale of 1-10)'
            },
            colors: ['#FFD700', '#FF1493'], // Specify your desired colors here
            series: {
                0: {  // For the first series (Target In Hectare)
                    targetAxisIndex: 0,
                    bar: { groupWidth: '10%' } // Adjust the width of the bars here (e.g., '50%' means 50% of the available space)
                },
                1: {  // For the second series (Achievement In Hectare)
                    targetAxisIndex: 1,
                    bar: { groupWidth: '10%' } // Adjust the width of the bars here (e.g., '50%' means 50% of the available space)
                }
            }
        };

        var materialChart = new google.charts.Bar(document.getElementById('chart_div'));
        materialChart.draw(data, options);
    }
</script>