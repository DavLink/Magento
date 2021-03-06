require([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal'
], function ($) {

    $('#sales_order_view_tabs_custom_email_content').on('show', function(){
        $('#order_comments_customerservice_block').hide();
    });
    $('#sales_order_view_tabs_custom_email_content').on('hide', function(){
        $('#order_comments_customerservice_block').show();
    });

    $('#sendmail-button-parent button').click(function(){

        var mask = false;

        if ($('.loading-mask').is(":visible")){
            mask = true;
        }
        var isSent=setInterval(function(){
            if (!$('.loading-mask').is(":visible")){
                mask = false;
            }
            if (!mask){
                clearInterval(isSent);
                alert("Email has been sent successfully!");
            }
        },1000);
    });

     $('#custom-email-dropdown').change(function(){
        let kids = $('.template-'+$(this).val()+' > .email-message').children().length;
        let e_message = '';
        for (var i = 1; i<=kids; i++){
            e_message += $('.template-'+$(this).val()+' > .email-message p:nth-child('+i+')').html()+'\n\n';
        }
        $("#email-template").val(e_message);
        $(".email-container").html(e_message);
        /* $('#email-template').val($('.template-'+$(this).val()+' > .email-message').html()); */
        $('#email-template-subject').val($('.template-'+$(this).val()+' > .email-subject').text());
    });
    $('#copy-item-select').on('change', function(){
        var value= `<input value="${'<b>'+$(this).val()+'</b>'}" id="selVal" />`;
        $(value).insertAfter('#copy-item-select');
        $("#selVal").select();
        document.execCommand("Copy");
        $('body').find("#selVal").remove();

        $('#press-copy-item').each(function(){
            var $this = $(this);
            var originalText = $this.text();
            $(this).text(" Copied!");
            setTimeout(function() {
                $this.text(originalText)
            }, 2000); 
            
        });
        $(this).prop('selectedIndex',0);
    });
    $('#order-custom-email').click(function(){
        $('#sales_order_view_tabs_custom_email').click();
    });
    $('#custom-email-form').submit(function () {
        return false;
       });
    
    $('#email-template').change(function(){
        $(".email-container").html($('#email-template').val());
    });
    $('#boton_underscore_email').click(function(){
        wrapText('email-template', '<u>', '</u>');
    });
    $('#boton_italic_email').click(function(){
        wrapText('email-template', '<i>', '</i>');
    });
    $('#boton_bold_email').click(function(){
        wrapText('email-template', '<b>', '</b>');
    });
    var undo = false;
    $('#boton_alternative_email').click(function(){
        try {
            if (undo){
                $(this).text(undotext);
                undo = false;
                $('#input_costumer_email').val(undomail);
                $('#showed_customer_email').text(undomail);
            }
            else{
                var search = extractEmails($('.order-payment-method-title').html()).toString();
                if (search){
                    $('#input_costumer_email').val(search);
                    undomail = $('#showed_customer_email').text();
                    $('#showed_customer_email').text(search);
                    $(this).text('Undo');
                    undo = true;
                    undotext="Use alternative email";
                }
                else{
                    $("#email-not-found").show();
                    $(this).hide();
                }
            }
          
        }
        catch (ex) {
            $(this).hide();
            $("#email-not-found").show();
        }
    });

    function extractEmails (text)
    {
        return text.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi);
    }
    function wrapText(elementID, openTag, closeTag) {
        var textArea = $('#' + elementID);
        var len = textArea.val().length;
        var start = textArea[0].selectionStart;
        var end = textArea[0].selectionEnd;
        var selectedText = textArea.val().substring(start, end);
        var replacement = openTag + selectedText + closeTag;
        textArea.val(textArea.val().substring(0, start) + replacement + textArea.val().substring(end, len));
        $('#' + elementID).change();
    }
    document.getElementById("print-email-out").addEventListener("click", function(){ 
        var newWindowContent = document.getElementById('email-print').innerHTML;
        var newWindow = window.open("", "", "width=500,height=400");
        newWindow.document.title = $('#email-template-subject').val();
        newWindow.document.write(newWindowContent);
    });

    (function ($) {
        $.each(['show', 'hide'], function (i, ev) {
          var el = $.fn[ev];
          $.fn[ev] = function () {
            this.trigger(ev);
            return el.apply(this, arguments);
          };
        });
      })(jQuery);
});




