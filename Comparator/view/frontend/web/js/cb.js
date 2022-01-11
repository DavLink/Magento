require([
    'jquery',
    'jquery/jquery.cookie'
], function ($) {
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        $('.compare-checkbox').change();
        $('.comparator-main-container').change(); 
    }
    
    $('.comparator-main-container').each(function(){
        if (($.cookie('Product-A') && $.cookie('Product-B'))){
            $('.compare-page-button-text').removeClass('disabled'); 
            var currentURL = window.location.pathname;
            $.cookie('backURL', currentURL, { expires: 1, path: '/' });

        }
        else{
            $('.compare-page-button-text').addClass('disabled');
            $.cookie('backURL', null, { path: '/' });
        }
        if ($('.product_a').hasClass('off') && $('.product_b').hasClass('off')){
            $(this).hide();
        }
    });
    $('.comparator-main-container').change(function() {
        if ($(this).is( ":hidden" )){
            $(this).slideToggle('slow');
        }
        else if ($('.product_a').hasClass('off') && $('.product_b').hasClass('off')){
            $(this).slideToggle('slow');
        }
        if ($('.product_a').hasClass('off') || $('.product_b').hasClass('off')){
            $('.compare-page-button-text').addClass('disabled');
            $.cookie('backURL', null, { path: '/' });
        }
        else{
            $('.compare-page-button-text').removeClass('disabled'); 
            var currentURL = window.location.pathname;
            $.cookie('backURL', currentURL, { expires: 1, path: '/' }); 
        }

        if ( (!$.cookie('Product-A') && $('.product_a').is(":visible")) || (!$.cookie('Product-B') && $('.product_b').is(":visible")) ) {
            location.reload();
        }
    });
    $('.compare-tray').on('click', function(){
        $('.toggle-compare-v').toggleClass('rotate');
        $('.compared-products').toggleClass('off');
    });
    $('.remove-button-x').on('click', function(){
        $(this).parent().addClass('off');
        if ($(this).parent().hasClass('product_a')){
            var id = $.cookie('Product-A');
            $.cookie('Product-A', null, { path: '/' });
            var selector = 'product-id\\['+ id + '\\]';
            $('#'+selector).each(function(){
                $(this).children(".compare-checkbox").prop("checked", false);
            });
        }
        if ($(this).parent().hasClass('product_b')){
            var id = $.cookie('Product-B');
            $.cookie('Product-B', null, { path: '/' });
            var selector = 'product-id\\['+ id + '\\]';
            $('#'+selector).each(function(){
                $(this).children(".compare-checkbox").prop("checked", false);
            });
        }
        $('.comparator-main-container').change();
        
    });
    $('.remove-page-button').on('click', function(){    
        if ($.cookie('Product-A')){
            var idA = $.cookie('Product-A');
            var selectorA = 'product-id\\['+ idA + '\\]';
            $.cookie('Product-A', null, { path: '/' });
            $('#'+selectorA).each(function(){
                $(this).children(".compare-checkbox").prop("checked", false);
            });
            $('.product_a').addClass('off');
        }
        if ($.cookie('Product-B')){
            var idB = $.cookie('Product-B');
            var selectorB = 'product-id\\['+ idB + '\\]';
            $.cookie('Product-B', null, { path: '/' });
            $('#'+selectorB).each(function(){
                $(this).children(".compare-checkbox").prop("checked", false);
            });
            $('.product_b').addClass('off');
        }
        $('.comparator-main-container').change();    
    });
    $('.compare-checkbox').change(function() {
        //atributo id
        var attrib_id= $(this).parent().attr('id')
        //product id
        var productID = attrib_id.substring(attrib_id.indexOf('[') + 1, attrib_id.indexOf(']') );
        var source = '', link = '', name = '', price = '', soldby = '';
        
        if ( $(this).parents(".product-item-info").find('.product-image-photo').attr('src') ){
            source = $(this).parents(".product-item-info").find('.product-image-photo').attr('src');
            link = $(this).parents(".product-item-info").find('.product-item-link').attr('href');
            name = $(this).parents(".product-item-info").find('.product-image-photo').attr('alt');
            price = $(this).parents(".product-item-info").find('.price').text();
            soldby = $(this).parents(".product-item-info").find('.product-code').clone().children().remove().end().text();
        }
        if ( $('.fotorama__active').find('img.fotorama__img').attr('src') ){
            source = $('.fotorama__active').find('img.fotorama__img').attr('src');
            link = window.location.href;
            name = $('.page-title').find('.base').text();

            if ($('.price-box[data-product-id="'+productID+'"]').parent().parent().find("td").filter('[data-th="Description"]').text()){
                name = $('.price-box[data-product-id="'+productID+'"]').parent().parent().find("td").filter('[data-th="Description"]').text();
            }
            price = $('.price-box[data-product-id="'+productID+'"]').find('.price').text();
            
            soldby = $('.price-box[data-product-id="'+productID+'"]').parents(".product-info-price").find('.cont span:eq(1)').text();

            if ($('.price-box[data-product-id="'+productID+'"]').parent().parent().find("td").filter('[data-th="Sold By"]').text()){
                soldby = $('.price-box[data-product-id="'+productID+'"]').parent().parent().find("td").filter('[data-th="Sold By"]').text();
            } 
        } 
        
        //Set the cookies
        if (!$.cookie('Product-A') && $.cookie('Product-B') != productID){
            $.cookie('Product-A', productID, { expires: 1, path: '/' });
            $(this).prop("checked", true);
            $('.product_a').removeClass('off');
            $('.product_a').each(function(){
                $(this).find(".product-compare-image").attr('src', source);
                $(this).find(".product-compare-link").attr('href', link);
                $(this).find(".description-compared-product-text").text(name);
                $(this).find(".price-compared-product-text").text(price);
                if (!!(soldby)){
                    $(this).find(".soldby-compared-product-text").text('Sold By: ' + soldby);
                }
            });   
            $('.comparator-main-container').change();         
        }
        else{
            if ($.cookie('Product-A') == productID){
                $.cookie('Product-A', null, { path: '/' });
                $(this).prop("checked", false);
                $('.product_a').addClass('off'); 
                $('.product_a').each(function(){
                    $(this).find(".product-compare-image").attr('src', '');
                    $(this).find(".product-compare-link").attr('href', '');
                    $(this).find(".description-compared-product-text").text('');
                    $(this).find(".price-compared-product-text").text('');
                    $(this).find(".soldby-compared-product-text").text('');
                });  
                $('.comparator-main-container').change();      
            }
            else{
                if (!$.cookie('Product-B')){
                    $.cookie('Product-B', productID, { expires: 1, path: '/' });
                    $(this).prop("checked", true);
                    $('.product_b').removeClass('off');
                    $('.product_b').each(function(){
                        $(this).find(".product-compare-image").attr('src', source);
                        $(this).find(".product-compare-link").attr('href', link);
                        $(this).find(".description-compared-product-text").text(name);
                        $(this).find(".price-compared-product-text").text(price);
                        if (!!(soldby)){
                            $(this).find(".soldby-compared-product-text").text('Sold By: ' + soldby);
                        }
                    });         
                    $('.comparator-main-container').change();              
                }
                else{
                    if ($.cookie('Product-B') == productID){
                        $.cookie('Product-B', null, { path: '/' });
                        $(this).prop("checked", false);
                        $('.product_b').addClass('off');
                        $('.product_b').each(function(){
                            $(this).find(".product-compare-image").attr('src', '');
                            $(this).find(".product-compare-link").attr('href', '');
                            $(this).find(".description-compared-product-text").text('');
                            $(this).find(".price-compared-product-text").text('');
                            $(this).find(".soldby-compared-product-text").text('');
                        });  
                        $('.comparator-main-container').change();  
                    }
                    else{
                        var newA = $.cookie('Product-B');
                        var newB = productID;
                        var selectorDelete = 'product-id\\['+ $.cookie('Product-A') + '\\]';
                        $.cookie('Product-A', null, { path: '/' });
                        $.cookie('Product-A', newA, { expires: 1, path: '/' });
                        $.cookie('Product-B', null, { path: '/' });
                        $.cookie('Product-B', newB, { expires: 1, path: '/' });

                        $('#'+selectorDelete).each(function(){
                            $(this).children(".compare-checkbox").prop("checked", false);
                        });
                        
                        var product_b_source = $('.product_b').find(".product-compare-image").attr('src');
                        var product_b_link = $('.product_b').find(".product-compare-link").attr('href');
                        var product_b_name = $('.product_b').find(".description-compared-product-text").text();
                        var product_b_price = $('.product_b').find(".price-compared-product-text").text();
                        var product_b_soldby = $('.product_b').find(".soldby-compared-product-text").text();

                        $('.product_a').each(function(){
                            $(this).find(".product-compare-image").attr('src', product_b_source);
                            $(this).find(".product-compare-link").attr('href', product_b_link);
                            $(this).find(".description-compared-product-text").text(product_b_name);
                            $(this).find(".price-compared-product-text").text(product_b_price);
                            if (product_b_soldby != ''){
                            $(this).find(".soldby-compared-product-text").text(product_b_soldby);
                            }
                        }); 

                        $('.product_b').each(function(){
                            $(this).find(".product-compare-image").attr('src', source);
                            $(this).find(".product-compare-link").attr('href', link);
                            $(this).find(".description-compared-product-text").text(name);
                            $(this).find(".price-compared-product-text").text(price);
                            if (!!(soldby)){
                                $(this).find(".soldby-compared-product-text").text('Sold By: ' + soldby);
                            }
                        });         
                        $('.comparator-main-container').change(); 
                    }
                } 
            }
        }
    });
});