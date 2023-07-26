<style>
    #container {
        height: 500px;
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 320px;
        max-width: 800px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }
</style>

<figure class="highcharts-figure">
    <div id="container"></div>
    <p class="highcharts-description">
        Comparative Variwide charts showing Labor Costs in Europe for two different years, 2016 and 2017.
        The column width is proportional to the country's GDP.
    </p>
</figure>
<script>
    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Comparative Millet Target vs Achievement - One District'
        },
        subtitle: {
            text: 'Source: Your Data Source'
        },
        xAxis: {
            categories: [],
            crosshair: true
        },

        yAxis: {
            title: {
                text: 'Millet Target and Achievement'
            }
        },
        legend: {
            enabled: true
        },
        series: [
            {
                name: 'Millet Target',
                data: [100, 120, 130, 140, 150, 180, 120, 160, 170, 180], // Replace this value with the millet target for the one district
                color: 'rgba(165,170,217,1)',
                borderRadius: 3,
                pointPadding: 0.2,
                borderWidth: 0
            },
            {
                name: 'Millet Achievement',
                data: [80, 70, 90, 40, 30, 70, 85, 65, 95, 65], // Replace this value with the millet achievement for the one district
                color: 'rgba(126,86,134,.9)',
                borderRadius: 3,
                pointPadding: 0.2,
                borderWidth: 0
            }
        ]
    });
</script>