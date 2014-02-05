$(document).ready(function(){
    $( ".gruppo" ).change(function(){
        var id = $(this).attr('ref');
        var group = $(this).val();
        $.post(Routing.generate('eph_domande-audit_group', {id: id, group: group}));
    });
    
    $( ".ordine" ).change(function(){
        var id = $(this).attr('ref');
        var order = $(this).val();
        $.post(Routing.generate('eph_domande-audit_order', {id: id, order: order}))
    });
});