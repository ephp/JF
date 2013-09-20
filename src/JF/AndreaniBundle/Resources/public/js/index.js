$(document).ready(function() {

    $('a.menu').click(function() {
        $('.submenu').hide();
        $('#'+$(this).attr('submenu')).show();
    });
    
    $('a.funzioni').click(function() {
        $('#fx').html('<div style="text-align:center; margin-top: 200px;"><img src="/img/loaders/1d_2.gif"></div>');
        $.post(Routing.generate('andreani_funzione', {'slug': $(this).attr('funzione')}), function(html) {
            $('#fx').html(html);
        });
    });

});