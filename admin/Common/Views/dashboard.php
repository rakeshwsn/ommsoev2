<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Highcharts Example</title>
    <?php js_start(); ?>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <?php js_end(); ?>
</head>
<body>
    <div id="container" class="content"></div>

    <?php js_start(); ?>
    <script type="text/javascript">
        // Add your Highcharts configuration here
        Highcharts.chart('container', {
            title: {
                text: 'My Chart'
            },
            series: [{
                data: [1, 2, 3, 4, 5]
            }]
        });
    </script>
    <?php js_end(); ?>
</body>
</html>
