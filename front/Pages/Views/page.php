<!DOCTYPE html>
<html>
<head>
    <title>Improved Complain Form</title>
    <?php js_start(); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php js_end(); ?>
</head>
<body>
    <div id="content">
        <?php echo $content; ?>
    </div>

    <form id="complain_form" action="complain_action.php" method="post">
        <!-- Add your form fields here -->
    </form>

    <?php js_start(); ?>
    <script type="text/javascript">
    $(document).ready(function(){
        $('#complain_form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var actionUrl = form.attr('action');

            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(),
                success: function(data) {
                    alert(data);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
    </script>
    <?php js_end(); ?>
</body>
</html>
