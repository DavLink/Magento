require([
    'jquery',
    'jquery/jquery.cookie'
], function ($) {
    var a = 0;
    $('.hide-row').on('click', function() {
        $(this).parent().hide();
        a = a + 1;
        $('.features-row').show();
        $('.hidden-features').text(a + ' hidden feature(s)');
    });
    $('.showall-row').on('click', function() {
        $('.hide-row').parent().show();
        a = 0;
        $('.features-row').hide();
    });
    $('.remove-button-A').on('click', function() {
        $.cookie('Product-A', null, { path: '/' });
        $('thead > tr > td:nth-child(2)').attr('style', 'display: none')
        $('tbody > tr > td:nth-child(1)').attr('style', 'display: none')
        if (!$.cookie('Product-B')){
            goBack();
        } 
    });
    $('.remove-button-B').on('click', function() {
        $.cookie('Product-B', null, { path: '/' });
        $('thead > tr > td:nth-child(3)').attr('style', 'display: none')
        $('tbody > tr > td:nth-child(2)').attr('style', 'display: none')
        if (!$.cookie('Product-A')){
            goBack();
        } 
    });
    $('.remove-all-items-button').on('click', function() {
        $.cookie('Product-A', null, { path: '/' });
        $.cookie('Product-B', null, { path: '/' });
        goBack();
    });
    $('.print-comparison-table').on('click', function() {
        window.print();
    });
    $('.goback-button').on('click', function() {
        goBack();
    });
    function goBack(){
        if($.cookie('backURL')){
            window.open($.cookie('backURL'), "_self");
        }
        else{
            location.reload();
        }
    }
});