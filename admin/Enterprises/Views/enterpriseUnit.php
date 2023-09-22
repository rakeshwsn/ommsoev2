<div class="row">

    <div class="col-4">
        <div class="block">
            <div class="block-header block-header-default"> Enterprises</div>
            <div class="content">               
                    <?= $form ?>
            </div>
        </div>
    </div>

    <div class="col-8">
        <div class="block">
            <div class="block-header block-header-default"> Enterprises List</div>
            <div class="content">
                <table class="table" id="datatable">
                    <thead>
                        <tr>
                            <th scope="col">slno</th>
                            <th scope="col">Unit Name</th>
                            <th scope="col">Group Unit </th>
                            <th scope="col">Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                            
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>

<?php js_start(); ?>
<script type="text/javascript">
    $(function(){
        $('#datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "columnDefs": [
                { targets: 'no-sort', orderable: false }
            ],
            "ajax":{
                url :"<?=$datatable_url?>", // json datasource
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    $(".datatable_error").html("");
                    $("#datatable").append('<tbody class="datatable_error"><tr><th colspan="7">No data found.</th></tr></tbody>');
                    $("#datatable_processing").css("display","none");
                },
                dataType:'json'
            },
        });
    });

    </script>
<?php js_end(); ?>