<form method="post" action="<?=$action?>" id="fpo-form-details" onsubmit="return false;" enctype="multipart/form-data">
    <?php
    $html="";
    foreach($formdata as $form){
        switch($form['input_type']){
            case "text":
               $html.=generateTextHtml($form);
               break;
            case "email":
                $html.=generateEmailHtml($form);
                break;
            case "date":
                $html.=generateDateHtml($form);
                break;
            case "select":
                $html.=generateSelectHtml($form);
                break;
            case "file":
                $html.=generateFileHtml($form);
                break;
            default:
                $html.="";
        }

  }
  echo $html;
?>
</form>
<script>
    jQuery(function(){
        Codebase.helpers(['flatpickr']);
    });
    jQuery('[data-toggle="custom-file-input"]:not(.js-custom-file-input-enabled)').each(function (e, a) {
        var t = jQuery(a);
        t.addClass("js-custom-file-input-enabled").on("change", function (e) {
            var a = e.target.files.length > 1 ? e.target.files.length + " " + (t.data("lang-files") || "Files") : e.target.files[0].name;
            t.next(".custom-file-label").css("overflow-x", "hidden").html(a);
        });
    });

    jQuery(function(){
        $("[data-calculation*='+']").each( function() {
            var cal=$(this).data('calculation');
            var id=$(this).attr('id')
            //"a=b,c:d".split('=').join(',').split(':').join(',').split(',')
            const calarray = cal.split("+");
            calarray.forEach(function (item,index){
                $("#"+item).attr('data-targetid',id);
            });

        });

        $(document).on('keyup', "[data-targetid]", function() {
            var target=$(this).data('targetid');

            var sum=0;
            $("[data-targetid]").each( function() {
                console.log($(this).val());
                sum+=parseInt($(this).val());
            });
            console.log(sum);
            $("#"+target).val(sum);
        });


    })
</script>