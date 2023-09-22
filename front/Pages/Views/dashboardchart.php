<style>
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        text-align: center;
        width: 100%;
        height: 250px;
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

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    .tentative_map_content {
        position: relative;
    }

    #tentative_map .legends {
        position: absolute;
        bottom: 0px;
        right: 0;
        top: auto;
        left: auto;
        margin: 0;
        transform: translateY(0%);

    }

    #tentative_map .legends p {
        transform: rotate(0deg);
        position: relative;
        left: 0;
        width: auto;
        text-align: center;
    }

    #tentative_map .legends ul {
        flex-flow: row;
    }

    .legends ul {
        margin-bottom: 0;
        display: flex;
        flex-flow: column;
    }

    #tentative_map .legends ul li {
        flex-flow: column;
        margin: 0 5px;
    }

    .legends ul li {
        display: flex;
        margin-bottom: 0px;
        align-items: center;
        color: #888888;
        flex-flow: row;
        font-size: 0.7rem;
        font-weight: 600;
        margin-right: 15px;
    }

    #tentative_map .legends ul li span {
        height: 8px;
        width: 50px;
    }

    .legends ul li span {
        height: 70px;
        width: 16px;
        display: block;
        margin-right: 6px;
        border-radius: 0px;
    }

    .bg-scale0 {
        fill: #EFEFEF !important;
        background: #EFEFEF !important;
    }

    .bg-scale1 {
        fill: #FEE86C !important;
        background: #FEE86C !important;
    }

    .bg-scale2 {
        fill: #FABC5B !important;
        background: #FABC5B !important;
    }

    .bg-scale3 {
        fill: #F7AD5D !important;
        background: #F7AD5D !important;
    }

    .bg-scale4 {
        fill: #F1A777 !important;
        background: #F1A777 !important;
    }

    .maptable {

        /* display: block; */
        height: 500px;
        overflow: auto;
    }

    table thead {
        background-color: #FABC5B;
        position: sticky;
        top: 0;
    }
</style>


<div class="row">
    <div class="col-6">
        <div class="row">
            <div class="col-6">
                <label class="form-label">District</label>
                <?php echo form_dropdown('district_id', $districts, [], ['class' => 'form-control mb-3', 'id' => 'districts_area']); ?>
            </div>
            <div class="col-12">
                <div id="farmervsarea">
                    <figure class="highcharts-figure">
                    </figure>
                </div>
            </div>

        </div>
    </div>

    <div class="col-6">
        <div class="row">
            <div class="col-6">
                <label class="form-label">District</label>
                <?php echo form_dropdown('district_id', $districts, [], ['class' => 'form-control mb-3', 'id' => 'districts_proc']); ?>
            </div>
            <div class="col-12">
                <div id="proc">
                    <figure class="highcharts-figure">
                    </figure>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-6" style="padding-top: 76px;">
        <div class="row">
            <div class="col-6">
            </div>
            <div class="col-12">
                <div id="pds">
                    <figure class="highcharts-figure">
                    </figure>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6" style="padding-top: 76px;">
        <div class="row">
        </div>

        <div class="col-12">
            <div id="establish">
                <figure class="highcharts-figure">
                </figure>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-3">
        <label class="form-label">Year</label>
        <?php echo form_dropdown('year_id', $years, [], ['class' => 'form-control mb-3', 'id' => 'enterprise_years']); ?>
    </div>

    <div class="col-3">
        <label class="form-label">District</label>
        <?php echo form_dropdown('district_id', $districts, [], ['class' => 'form-control mb-3', 'id' => 'enterprise_districts']); ?>
    </div>
</div>
<div class="row">
    <div class="col-7">
        <div id="container"></div>
    </div>

    <div class="col-5">
        <div class="block" id="enterprise_table">
        </div>
    </div>
</div>

<div class="block" id="tentative_map">
    <div class="block-header block-header-default">
        <h3 class="block-title">Scale of Odisha Millets Mission</h3>
    </div>
    <div class="block-content block-content-full tentative_map_content">
        <div class="map-section">
            <div class="row">
                <div class="col-6">
                    <div id="map-container">

                    </div>
                    <div class="legends">
                        <p class=" mb-1 mt-0">Based on No. of Blocks </p>
                        <ul class="">
                            <li><span class="bg-scale0"></span> 0 </li>
                            <li><span class="bg-scale1"></span> 1 - 3 </li>
                            <li><span class="bg-scale2"></span> 3 - 4 </li>
                            <li><span class="bg-scale3"></span> 4 - 5 </li>
                            <li><span class="bg-scale4"></span> 5+ </li>
                        </ul>
                    </div>
                </div>
                <div class="maptable col-6">
                    <!-- <div class="table-responsive" > -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">District</th>
                                <th scope="col">Total Block</th>
                                <th scope="col">No. of GPs </th>
                                <th scope="col">No. of Villages </th>
                                <th scope="col">Total Farmer</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <!-- </div> -->
                </div>

            </div>
        </div>

    </div>
</div>

<?php js_start(); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="<?php echo theme_url('assets/js/snap.svg-min.js') ?>"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    //AREACOVERAGE START

    var areachartOptions = {
        areachart: {
            type: 'line'
        },
        title: {
            text: 'Millet Demonstration Progress',


        },
        xAxis: {
            categories: [],
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },


        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },

        series: [{
            name: 'Farmers (in number)',
            marker: {
                symbol: 'square',
            },
            data: [],
            color: '#ff0000'
        }, {
            name: 'Area Demonstration (in Hectare)',
            marker: {
                symbol: 'circle'
            },
            data: [],
            color: '#0c97e8'
        }]
    };
    var areachart = Highcharts.chart('farmervsarea', areachartOptions);
    //AREACOVERAGE END
    // PROCUREMENT START
    var procurechartOptions = {
        pchart: {
            type: 'line'
        },
        title: {
            text: 'Ragi Procurement Progress'

        },
        xAxis: {
            categories: [],
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: [{
            name: 'Farmer covered (in Number)',
            marker: {
                symbol: 'triangle'
            },
            data: [],
            color: '#cb0ce8'

        }, {
            name: 'Quantity of Ragi Procures (in Quintals) ',
            marker: {
                symbol: 'diamond'
            },
            data: [],
            color: '#822f0c'
        }, ]
    };
    var pchart = Highcharts.chart('proc', procurechartOptions);
    // PROCUREMENT END


    //PDS START
    var pdschartOptions = {
        pdschart: {
            zoomType: 'xy'
        },
        title: {
            text: 'Ragi Distributed in PDS',
            // color:'#085c19',
            align: 'center'
        },

        xAxis: [{
            categories: [],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}qtl',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Quantity (in Quintals)',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'Card Holders Benefited (in Number) ',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },

            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            align: 'center',
            // x: 80,
            verticalAlign: 'bottom',
            // y: 60,
            // floating: true,
            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                'rgba(255,255,255,0.25)'
        },
        series: [{
            name: 'Quantity (in Quintals)',
            type: 'column',
            yAxis: 1,
            data: [],
            color: '#26d128',
            tooltip: {
                valueSuffix: ' Qtl'
            }

        }, {
            name: 'Card Holders Benefited (in Number) ',
            type: 'spline',
            data: [],
            color: '#1e820c',
            tooltip: {
                valueSuffix: ''
            }
        }]
    };
    var pdschart = Highcharts.chart('pds', pdschartOptions);
    //PDS END
    //ESTABLISHMENT START

    var establishchart = {
        echart: {
            zoomType: 'xy'
        },
        title: {
            text: 'CHC and CMS Establishment Progress',
            align: 'center'
        },

        xAxis: [{
            categories: [],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Blocks',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'CHC/CMSC',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },

            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            align: 'center',
            // x: 80,
            verticalAlign: 'bottom',
            // y: 60,
            // floating: true,
            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                'rgba(255,255,255,0.25)'
        },
        series: [{
                name: 'Programme Blocks (in number)',
                type: 'column',
                yAxis: 1,
                data: [],
                tooltip: {
                    valueSuffix: ' '
                }

            }, {
                name: 'CHC (in Number)',
                type: 'spline',
                data: [],
                tooltip: {
                    valueSuffix: ''
                }
            },
            {
                name: 'CMSC (in number)',
                type: 'spline',
                data: [],
                tooltip: {
                    valueSuffix: ''
                }
            }
        ]
    };
    var echart = Highcharts.chart('establish', establishchart);

    // ESTABLISHMENT END

    // Data retrieved from https://yearbook.enerdata.net/electricity/world-electricity-production-statistics.html

    var chartOptions = {
        chart: {
            type: 'column',
            options3d: {
                enabled: true,
                alpha: 0,
                beta: 0,
                viewDistance: 10,
                depth: 40
            }
        },

        title: {
            text: 'Progress on Millet based Enterprise Establishment',
            align: 'center'
        },

        xAxis: {
            labels: {
                skew3d: true,
                style: {
                    fontSize: '11x'
                }
            },
            categories: []

        },

        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Total Units '
            }
        },

        tooltip: {
            headerFormat: '<b>{point.key}</b><br>',
            pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y} / {point.stackTotal}'
        },

        plotOptions: {

            column: {
                stacking: 'normal',
                depth: 40
            }
        },

        series: [{
            name: 'Enterprises with WSHGs',
            data: [],
            color: '#085c19',

        }, {
            name: 'Enterprises with FPOs',
            data: [],
            color: '#26d128',

        }]
    };
    var chart = Highcharts.chart('container', chartOptions);

    //ENTERPRISE START
    // var chartOptions = {

    //     chart: {
    //         type: 'column'
    //     },

    //     title: {
    //         text: 'Progress on Millet based Enterprise Establishment ',

    //         align: 'center'
    //     },

    //     xAxis: {
    //         categories: []
    //     },
    //     tooltip: {
    //         crosshairs: true,
    //         shared: true
    //     },

    //     yAxis: {
    //         allowDecimals: false,
    //         min: 0,
    //         title: {
    //             text: 'Total Units '
    //         }
    //     },

    //     tooltip: {
    //         format: '<b>{key}</b><br/>{series.name}: {y}<br/>' +
    //             'Total: {point.stackTotal}'
    //     },

    //     plotOptions: {
    //         column: {
    //             stacking: 'normal'
    //         }
    //     },

    //     series: [{
    //         name: ' SHG',
    //         data: [],
    //         color: '#085c19',
    //         stack: 'North America'
    //     }, {
    //         name: 'FPO',
    //         data: [],
    //         color: '#26d128',
    //         stack: 'North America'
    //     }]
    // };
    // var chart = Highcharts.chart('container', chartOptions);
    // ENTERPRISE END

    $(function() {
        //AREA
        $('#districts_area').on('change', function() {
            district_id = $(this).val();
            $.ajax({
                url: '<?= $area_url ?>',
                data: {
                    'district_id': district_id,
                    chart_type: 'achart'
                },
                type: 'GET',
                dataType: 'JSON',
                success: function(response) {
                    areachart.xAxis[0].setCategories(response.areayears);
                    areachart.series[0].setData(response.areafarmers);
                    areachart.series[1].setData(response.areaachievements);
                    areachart.setTitle({
                        text: response.heading
                    });

                }


            });
        });
        $('#districts_area').change();


        //proc
        $('#districts_proc').on('change', function() {
            district_id = $(this).val();
            $.ajax({
                url: '<?= $procure_url ?>',
                data: {
                    'district_id': district_id,
                    chart_type: 'pchart'
                },
                type: 'GET',
                dataType: 'JSON',
                success: function(response) {
                    pchart.xAxis[0].setCategories(response.pyears);
                    pchart.series[0].setData(response.pfarmers);
                    pchart.series[1].setData(response.pquantity);
                    pchart.setTitle({
                        text: response.heading
                    });

                }

            });
        });
        $('#districts_proc').change();
        //PDS

        $.ajax({
            url: '<?= $pds_url ?>',
            data: {

            },
            type: 'GET',
            dataType: 'JSON',
            success: function(response) {
                pdschart.xAxis[0].setCategories(response.pdsyear);
                pdschart.series[0].setData(response.pdsquantity);
                pdschart.series[1].setData(response.card_holders_benefited);

            }

        });
    });


    //ENTERPRISES START
    $('#enterprise_districts,#enterprise_years').on('change', function() {
        district_id = $('#enterprise_districts').val();
        year_id = $('#enterprise_years').val();
        $.ajax({
            url: '<?= $enterprise_url ?>',
            data: {
                'district_id': district_id,
                'year_id': year_id

            },
            type: 'GET',
            dataType: 'JSON',
            success: function(response) {
                chart.xAxis[0].setCategories(response.unit_name);
                chart.series[0].setData(response.wshg);
                chart.series[1].setData(response.fpos);
                chart.setTitle({
                    text: response.heading
                });
                $('#enterprise_table').html(response.table);
            }
        });
    });
    $('#enterprise_districts').change();

    //ENTERPRISES END

    //ESTABLISHMENT
    $.ajax({
        url: '<?= $establish_url ?>',
        data: {

        },
        type: 'GET',
        dataType: 'JSON',
        success: function(response) {
            echart.xAxis[0].setCategories(response.estdistrict);
            echart.series[0].setData(response.chc);
            echart.series[1].setData(response.cmsc);
            echart.series[2].setData(response.blocks);
        }


    });


    window.onload = function() {
        $.ajax({
            url: '<?= $odishamap_url ?>',
            data: {

            },
            type: 'GET',
            dataType: 'JSON',
            success: function(response) {
                mapinfo = response.data
                Snap.load("uploads/files/ommodishamap.svg", function(loadedFragment) {
                    // 'loadedFragment' contains the loaded SVG elements
                    var map = loadedFragment.select("svg");

                    // Get the jQuery element where you want to append the SVG
                    var $element = $("#map-container");

                    // Append the SVG to the element
                    $element.append(map.node);

                    // Now you can manipulate the SVG elements as needed
                    // For example, let's change the color of a path element to red
                   
                    console.log(mapinfo);
                    const district = map.selectAll(".dist");

                    district.forEach(function(obj) {
                        var mapid = obj.attr('id');
                        if (mapid) {

                            const dist = mapinfo.filter(item => item.district_id === mapid);


                            if (dist[0].total_blocks >= 1 && dist[0].total_blocks <= 3) {
                                obj.addClass('bg-scale1')
                            }
                            if (dist[0].total_blocks >= 4 && dist[0].total_blocks <= 5) {
                                obj.addClass('bg-scale2')
                            }
                            if (dist[0].total_blocks >= 6 && dist[0].total_blocks <= 8) {
                                obj.addClass('bg-scale3')
                            }
                            if (dist[0].total_blocks >= 9) {
                                obj.addClass('bg-scale4')
                            } else if (dist[0].total_blocks == 0) {
                                obj.addClass('bg-scale2')
                            }


                        }
                        obj.click(clickCallback);

                    }, "text");

                });

                loadMapTable(mapinfo);
            }


        });


        var clickCallback = function(event) {
            console.log(event);
            var id = event.target.attributes.id.nodeValue;
            console.log(id);
            $(".maptable tr").removeClass("bg-primary");
            $(".maptable tr[data-dist=" + id + "]").addClass("bg-primary");

            const highlightElement = $(".maptable tr[data-dist=" + id + "]");
            if (highlightElement.length) {
                const container = $(".maptable");
                if (container.length) {
                    container.animate({
                        scrollTop: highlightElement.offset().top - container.offset().top + container.scrollTop() - 60
                    }, "slow");
                }
            }

        };

    };

    function loadMapTable(data){
        html='';
        data.forEach(function(obj) {
            html +='<tr data-dist="'+obj.district_id+'">';
            html +='<td>'+obj.districts+'</td>';
            html +='<td>'+obj.total_blocks+'</td>';
            html +='<td>'+obj.total_gps+'</td>';
            html +='<td>'+obj.total_villages+'</td>';
            html +='<td>'+obj.total_farmers+'</td>';
            html +='</tr>';
        });
        $(".maptable tbody").html(html);
    }
</script>
<?php js_end(); ?>