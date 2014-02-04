$(document).ready(function(){
    $( ".gruppo" ).change(function(){
        var id = $(this).attr('ref');
        var group = $(this).val();
        $.post(Routing.generate('claims-h-audit-risposta'))
    });
});