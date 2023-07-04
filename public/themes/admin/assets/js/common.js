$(function(){
	$(document).delegate('.popup', 'click', function(e) {
		e.preventDefault();

		$('#modal-popup').remove();

		var element = this;
		var editor="visual";
		$.ajax({
			url: editor,
			type: 'get',
			dataType: 'html',
			success: function(data) {
				html  = '<div id="modal-popup" class="modal">';
				html += '  <div class="modal-dialog modal-fullscreen">';
				html += '    <div class="modal-content">';
				//html += '      <div class="modal-header">';
				//html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
				//html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
				//html += '      </div>';
				html += '      <div class="modal-body">' + data + '</div>';
				html += '    </div';
				html += '  </div>';
				html += '</div>';

				$('body').append(html);
				Vvveb.Builder.init($(element).attr('href'), function() {
					//run code after page/iframe is loaded
				});
				Vvveb.Gui.init();

				$('#modal-popup').modal('show');
				
			}
		});
		
		
		
	});
});

//rakesh
function numOnly() {
    //input type text to number
    // Get the input field
    var input = $('.physical');

    // Attach keypress event handler
    input.keypress(function(event) {
        // Get the key code of the pressed key
        var keyCode = event.which;

        // Check if the key is a number
        if (keyCode < 48 || keyCode > 57) {
            // Prevent the input if the key is not a number
            event.preventDefault();
        }
    });
}

function decimalOnly() {
    // Get the input field
    var input = $('.financial');

    $('.financial').on('keypress',function (e) {
        // Get the key code of the pressed key
        var keyCode = event.which;

        // Allow decimal point (.) and numbers (48-57) only
        if (keyCode !== 46 && (keyCode < 48 || keyCode > 57)) {
            // Prevent the input if the key is not a number or decimal point
            event.preventDefault();
        }

        // Allow only one decimal point
        if (keyCode === 46 && $(this).val().indexOf('.') !== -1) {
            // Prevent the input if there is already a decimal point
            event.preventDefault();
        }
        // Disallow comma (,)
        if (keyCode === 44) {
            // Prevent the input if the key is a comma
            event.preventDefault();
        }
    });
}

function cleanupInput() {
    $(".financial").keyup(function(event) {
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;

        // format number
        $(this).val(function(index, value) {
            return value
                .replace(/,/g , "");
        });
    });
}