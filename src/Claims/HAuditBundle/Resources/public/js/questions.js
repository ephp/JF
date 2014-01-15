$(document).ready(function(){
    $( "#si, #no" ).sortable({
      connectWith: ".questions"
    }).disableSelection();
    
    $('#claims_hauditbundle_question_submit').click(function(){
        var $form = $(this).closest('form');
        console.log($form.serialize());
        $.post($form.attr('action'), $form.serialize(), function(html) {
            $('#si').append(html);
            $form[0].reset();
            $.fancybox.close();
        });
    });
    
    $('#end').click(function(){
        var ids = [];
        $('#si').children('li').each(function(){
            ids.add($(this).attr('value'));
        });
        $.post(Routing.generate('claims-h-audit-save-questions', {'id': audit}), {ids: ids}, function(out) {
            window.location = out.redirect;
        });
    });
});