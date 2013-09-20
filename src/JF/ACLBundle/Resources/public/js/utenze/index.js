$(document).ready(function() {

    $('a.role').click(function() {
        var $a = $(this);
        $.post(Routing.generate('utenze_role', {'slug': $a.attr('slug'), 'role': $a.attr('role')}),
        function(out) {
            if(out.active) {
                $a.removeClass('red').addClass('green');
                $a.find('span').removeClass('ico-check-empty').addClass('ico-check');
            } else {
                $a.removeClass('green').addClass('red');
                $a.find('span').removeClass('ico-check').addClass('ico-check-empty');
            }
        });
    });
    
    $('a.lock').click(function() {
        var $a = $(this);
        $.post(Routing.generate('utenze_lock', {'slug': $a.attr('slug')}),
        function(out) {
            if(out.locked) {
                $a.removeClass('red').addClass('green');
                $a.find('span').removeClass('ico-trash').addClass('ico-refresh');
                $a.closest('tr').addClass('stroke');
            } else {
                $a.removeClass('green').addClass('red');
                $a.find('span').removeClass('ico-refresh').addClass('ico-trash');
                $a.closest('tr').removeClass('stroke');
            }
        });
    });

});
