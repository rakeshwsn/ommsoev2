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
<div class="content1">
    <div class="row invisible" data-toggle="appear">
        <!-- Row #1 -->
        <div class="col-6 col-xl-4" data-toggle="modal" data-target="#myModalone">
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
        <div class="col-6 col-xl-4" data-toggle="modal" data-target="#myModalone">
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
        <div class="col-6 col-xl-4" data-toggle="modal" data-target="#myModalone">
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
        <!-- rakesh nayak code updated -->
     
        <div class="col-md-12 col-sm-4">
        <!-- Bars Chart -->
        <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">FUND RECEIPT/EXPENSE</h3>
                    <div class="block-options">
                    <select name="selectwisedata" id="selectwisedata" style="width: 150px;text-align: center;">
                        <option value="all" selected='selected'>All</option>
                        <option value="district">Districtwise</option>
                        <option value="agency">Agencywise</option>
                        <option value="percentage">Percentage</option>
                    </select>
					<button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo"></button>
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
    <!--<div class="row invisible" data-toggle="appear">
        <?/*=$upload_status*/?>
    </div>-->
</div>



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
                    <th>District/Agency</th>
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
                    <td class="date-uploaded"><?php echo $abstractn['district']?></td>
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

	$(function(){
		shownewchart('all');
		$('#selectwisedata').on('change',function(){
			if($(this).val()=='all'){
				shownewchart('all');
			}
			if($(this).val()=='district'){
				shownewchart('district');
			}
			if($(this).val()=='agency'){
				shownewchart('agency');
			}
			if($(this).val()=='percentage'){
				shownewchartvalue('percentage');
			}
		});
	});
    
    var myChart = echarts.init(document.getElementById('chart-container'));
    function shownewchart(selectdata){
       // var selectdata=$("#selectwisedata option:selected").val();
    $.ajax({
            url: '<?php echo base_url() ?>/admin/spmu/chart',
            data:{ 'data': selectdata},
            method: 'GET',
            type: 'json',
            success: function(resp) {  
                var ob = JSON.parse(resp);
               // console.log(ob);
				option = {
				  tooltip: {
					trigger: 'axis',
					axisPointer: {
					  type: 'shadow'
					}
				  },
				  legend: {
					data: ['Fund Receipt', 'Expense'],
				   // backgroundColor: '#ccc',
					selectedMode: true,
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
				  
				  grid: {
					height: "40%",
					width: "85%"
				  },
				  xAxis: [
					{
					  type: 'category',
					  axisTick: { show: false },
					  axisLabel: { interval: 0, rotate: 45 },
					  data: ob.fund.label
					}
				  ],
				  yAxis: [
					{
					  type: 'value',
					  boundaryGap: true,
					// min: 100,
					// scale: false,
					// splitNumber: 10,
				   
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
					  data: ob.fund.data
					  
					},
					{
					  name: 'Expense',
					  type: 'bar',
					  stack: 'x',
					//   label: labelOption,
					  emphasis: {
						focus: 'series'
					  },
					  data: ob.abstract.data
					}
				  ]
				};

				if (option && typeof option === 'object') {
				  myChart.setOption(option);
				}
			}
		});
    }

window.addEventListener('resize', myChart.resize);
</script>

<script>
    function shownewchartvalue(data){
        var myChart = echarts.init(document.getElementById('chart-container'));
      //  console.log(data);
        $.ajax({
            url: '<?php echo base_url() ?>/admin/spmu/chart',
            data:{ 'data': data},
            method: 'GET',
            type: 'json',
      
         success: function(resp) {   
            var ob = JSON.parse(resp);
            console.log(ob.abstracts)
          
            myChart.setOption({
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                    type: 'shadow'
                    }
                },
                legend: {
                        data: ['Percentage'],
                      //  backgroundColor: '#ccc',
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
                    grid: {
                        height: "40%",
                        width: "85%"
                    },
                    xAxis: [
                        {
                        type: 'category',
                        axisTick: { show: false },
                        axisLabel: { interval: 0, rotate: 45 },
                        data: ob.abstracts.district
                        }
                    ],
                    series: [
                    {
                    name: 'Percentage',
                    type: 'bar',
                    stack: 'x',
                    //   label: labelOption,
                    emphasis: {
                        focus: 'series'
                    },
                    data: ob.abstracts.uc
                    }
                ]
                
                   
                   
                },{
					replaceMerge: ['series']
				});
            
            }
        });
    }
</script>


<?php js_end(); ?>