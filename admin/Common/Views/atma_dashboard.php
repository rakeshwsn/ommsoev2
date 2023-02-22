<style>
    .red {
        background-color: rgb(255, 0, 0);
        color: black;
    }
    .orange {
        background-color: rgb(250, 192, 144);
        color: black;
    }
    .yellow {
        background-color: rgb(255, 255, 0);
        color: black;
    }
    .green {
        background-color: #77933C;
        color: black;
    }
    .table thead th {
        
        text-transform: none !important;
}
#chart-container {
  position: relative;
  height: 100vh;
  overflow: hidden;
}

</style>
<div class="content">
    <div class="row invisible" data-toggle="appear">
        <!-- Row #1 -->
        <div class="col-6 col-xl-3" data-toggle="modal" data-target="#myModalone">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-login fa-2x text-earth-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-earth"><i class="fa fa-rupee"></i> <span data-toggle="countTo" data-speed="1000" data-to="<?=$fr?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Fund Receipt</div>
                </div>
            </a>
        </div>
        <?php /*
        <div class="col-6 col-xl-3">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-login fa-2x text-earth-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-earth"><i class="fa fa-rupee"></i> <span data-toggle="countTo" data-speed="1000" data-to="<?=$frel?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Fund Release</div>
                </div>
            </a>
        </div>
        */ ?>
        <div class="col-6 col-xl-3" data-toggle="modal" data-target="#myModalone">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-logout fa-2x text-elegance-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-elegance"><i class="fa fa-rupee"></i> <span data-toggle="countTo" data-speed="1000" data-to="<?=$ex?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Expense</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3" data-toggle="modal" data-target="#myModalone">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-briefcase fa-2x text-pulse"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-pulse"><i class="fa fa-rupee"></i> <span  data-toggle="countTo" data-speed="1000" data-to="<?=$cb?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Closing Balance <small>as on date</small></div>
                </div>
            </a>
        </div>
        <!-- END Row #1 -->
		
		 <div class="col-md-12 col-sm-4">
        <!-- Bars Chart -->
        <div class="block" style="margin-bottom: -300px;">
                <div class="block-header block-header-default">
                    <h3 class="block-title">FUND RECEIPT/EXPENSE</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                        
                        </button>
                    </div>
                </div>
                <div class="block-content block-content-full text-center">
                    <!-- Bars Chart Container -->
                    <div id="chart-container"></div>
                </div>
            </div>
            <!-- END Bars Chart -->

        </div>
    </div>

    <div class="row invisible" data-toggle="appear">
        <?=$upload_status?>
    </div>

    <?php if($components): ?>
        <div class="row invisible" data-toggle="appear">
            <!-- Row #1 -->
            <div class="col-12">
                <div class="block block-themed">
                    <div class="block-header bg-muted">
                        <h3 class="block-title">MPR <?=$year?></h3>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="tableFixHead">
                            <table class="table custom-table " id="txn-table">
                                <thead>
                                <tr>
                                    <th rowspan="3">Sl no</th>
                                    <th rowspan="3">Component</th>
                                    <th rowspan="2" colspan="2">Opening Balance(in lakhs)</th>
                                    <th rowspan="2" colspan="2">Target (in lakhs)</th>
                                    <th rowspan="1" colspan="6">Allotment received (in lakhs)</th>
                                    <th rowspan="1" colspan="4">Expenditure (in lakhs)</th>
                                    <th rowspan="2" colspan="2">Unspent Balance upto the month (in lakhs)</th>
                                </tr>
                                <tr>
                                    <th colspan="2">As per statement upto prev month</th>
                                    <th colspan="2">During the month</th>
                                    <th colspan="2">Upto the month</th>
                                    <th colspan="2">During the month</th>
                                    <th colspan="2">Cumulative upto the month</th>
                                </tr>
                                <tr>
                                    <th>Phy</th>
                                    <th>Fin</th>
                                    <th>Phy</th>
                                    <th>Fin</th>
                                    <th>Phy</th>
                                    <th>Fin</th>
                                    <th>Phy</th>
                                    <th>Fin</th>
                                    <th>Phy</th>
                                    <th>Fin</th>
                                    <th>Phy</th>
                                    <th>Fin</th>
                                    <th>Phy</th>
                                    <th>Fin</th>
                                    <th>Phy</th>
                                    <th>Fin</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?=$components?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Row #1 -->
        </div>
    <?php endif; ?>
</div>

<!-- Pop Out Modal -->
<div class="modal fade" id="modal-popout" tabindex="-1" role="dialog" aria-labelledby="modal-popout" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Fund Receipt</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <div>
                        <p>Do you have any fund receipt this month?
                            <button type="button" data-value="yes" class="btn btn-alt-success btn-fr" >Yes</button>
                            <button type="button" data-value="no" class="btn btn-alt-danger btn-fr" >No</button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Pop Out Modal -->



<!-- model for fund receipt -->

<div class="modal fade" id="myModalone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="padding: 0 !important;">
  <div class="modal-dialog modal-xl" style="width: 100%; max-width: none;height: auto;margin: 0;">
    <div class="modal-content" style="height: 100%;border: 0;border-radius: 0;">
      <div class="modal-header" style="display: block !important;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="font-size: 73px;">&times;</button>
        <div style="display: flex;justify-content: center;">
      <div class="">
        <div class="col-md-12 col-sm-4">
        <div class="block">
        <div class="block-content block-content-full">
        <table class="table table-bordered table-vcenter" id="table-mpr">
                <thead>
                <tr style="background: black;color: white;">
                    <th>Block</th>
                    <th class="">Fund Receipt(in lakh)</th>
                    <th class="">Expenditure(in lakh)</th>
                    <th class="">% of Expenditure</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($abstracts as $abstractn){  
                    if($abstractn['uc'] >= 60) {
                        $c="green";
                    }else if($abstractn['uc'] < 60 && $abstractn['uc'] > 40) {
                        $c="yellow";
                    } else if($abstractn['uc'] < 40 && $abstractn['uc'] > 25){

                        $c = "orange";
                    } else if($abstractn['uc'] < 25){
                        $c = "red";

                    }
                    
                    ?>    
                  <tr class="<?php echo $c; ?>">
                    <td class="date-uploaded"><?php echo $abstractn['block']?></td>
                    <td class="date-uploaded"><?php echo $abstractn['ftotal']?></td>
                    <td class="uploaded"><?php echo $abstractn['etotal']?></td>
                  
                    <td class="uploaded"><?php echo $abstractn['uc']?></td>

             
                </tr>
                <?php  }  ?>             
             </tbody>
            </table>
            
        </div>
        </div>
          </div>
           </div>
          </div>
      </div>
      
    </div>
  </div>
</div>
<!-- model for fund receipt -->
<?php js_start(); ?>
<script src="https://fastly.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>


<script>
    var dom = document.getElementById('chart-container');
var myChart = echarts.init(dom, null, {
  renderer: 'canvas',
  useDirtyRect: false
});
var app = {};

var option;

const posList = [
  'left',
  'right',
  'top',
  'bottom',
  'inside',
  'insideTop',
  'insideLeft',
  'insideRight',
  'insideBottom',
  'insideTopLeft',
  'insideTopRight',
  'insideBottomLeft',
  'insideBottomRight'
];
app.configParameters = {
  rotate: {
    min: -90,
    max: 90
  },
  align: {
    options: {
      left: 'left',
      center: 'center',
      right: 'right'
    }
  },
  verticalAlign: {
    options: {
      top: 'top',
      middle: 'middle',
      bottom: 'bottom'
    }
  },
  position: {
    options: posList.reduce(function (map, pos) {
      map[pos] = pos;
      return map;
    }, {})
  },
  distance: {
    min: 0,
    max: 100
  }
};
app.config = {
  rotate: 90,
  align: 'left',
  verticalAlign: 'middle',
  position: 'insideBottom',
  distance: 20,
  onChange: function () {
    const labelOption = {
      rotate: app.config.rotate,
      align: app.config.align,
      verticalAlign: app.config.verticalAlign,
      position: app.config.position,
      distance: app.config.distance
    };
    myChart.setOption({
      series: [
        {
          label: labelOption
        },
        {
          label: labelOption
        },
        {
          label: labelOption
        },
        {
          label: labelOption
        }
      ]
    });
  }
};
const labelOption = {
  show: true,
  position: app.config.position,
  distance: app.config.distance,
  align: app.config.align,
  verticalAlign: app.config.verticalAlign,
  rotate: app.config.rotate,
  formatter: '{c}  {name|{a}}',
  fontSize: 16,
  rich: {
    name: {}
  }
};
option = {
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'shadow'
    }
  },
  legend: {
    data: ['Fund Receipt', 'Expense'],
    backgroundColor: '#ccc',
  },
  toolbox: {
    show: true,
    orient: 'vertical',
    left: 'right',
    top: 'center',
    feature: {
      mark: { show: true },
    //   dataView: { show: true, readOnly: false },
      magicType: { show: true, type: ['bar', 'stack'] },
    //   restore: { show: true },
      saveAsImage: { show: true }
    }
  },
  legend: {
    selectedMode: true,
  },
  grid: {
    height: "40%",
    width: "85%"
  },
  xAxis: [
    {
      type: 'category',
      axisTick: { show: false },
      axisLabel: { interval: 0, rotate: 45 },
      data: <?php echo json_encode($fund['label'])?>
    }
  ],
  yAxis: [
    {
      type: 'value'
    }
  ],
  series: [
    {
      name: 'Fund Receipt',
      type: 'bar',
      stack: 'x',
      barGap: 0,
    //   label: labelOption,
      emphasis: {
        focus: 'series'
      },
      data: <?php echo json_encode($fund['data'],JSON_NUMERIC_CHECK)?>
      
    },
    {
      name: 'Expense',
      type: 'bar',
      stack: 'x',
    //   label: labelOption,
      emphasis: {
        focus: 'series'
      },
      data: <?php echo json_encode($abstract['data'],JSON_NUMERIC_CHECK)?>
    }
  ]
};

if (option && typeof option === 'object') {
  myChart.setOption(option);
}

window.addEventListener('resize', myChart.resize);
</script>

<script type="text/javascript">
    <?php if($fr_check): ?>
    $(function () {
        $('#modal-popout').modal('show');

        $('.btn-fr').click(function () {
            choice = $(this).data('value');
            $.ajax({
                data:{choice:choice},
                type:'GET',
                dataType:'JSON',
                success:function (json) {
                    $('#modal-popout').modal('hide');
                    if(choice=='yes') {
                        location.href = "<?=$fr_url?>";
                    }
                },
                error:function () {
                    alert('Request not successful');
                    $('#modal-popout').modal('hide');
                }
            });
        });
    });
    <?php endif; ?>
</script>
<?php js_end(); ?>


                