$(document).ready(function(){
    $( ".gruppo" ).change(function(){
        var id = $(this).attr('ref');
        var group = $(this).val();
        $.post(Routing.generate('eph_domande-audit_group', {id: id, group: group}));
    });
    
    $( ".sottogruppo" ).change(function(){
        var id = $(this).attr('ref');
        var group = $(this).val();
        $.post(Routing.generate('eph_domande-audit_subgroup', {id: id, subgroup: group}));
    });
    
    $( ".ordine" ).change(function(){
        var id = $(this).attr('ref');
        var order = $(this).val();
        $.post(Routing.generate('eph_domande-audit_order', {id: id, order: order}))
    });
    
    $( ".anteprima" ).change(function(){
        var id = $(this).attr('ref');
        var order = $(this).val();
        $.post(Routing.generate('eph_domande-audit_preview', {id: id, preview: order}))
    });
    
    $( ".question" ).change(function(){
        var id = $(this).attr('ref');
        var order = $(this).val();
        $.post(Routing.generate('eph_domande-audit_test', {id: id, type: 'question'}), {text: order})
    });
    
    $( ".domanda" ).change(function(){
        var id = $(this).attr('ref');
        var order = $(this).val();
        $.post(Routing.generate('eph_domande-audit_text', {id: id, type: 'domanda'}), {text: order})
    });
    
    $( ".example" ).change(function(){
        var id = $(this).attr('ref');
        var order = $(this).val();
        $.post(Routing.generate('eph_domande-audit_text', {id: id, type: 'example'}), {text: order})
    });
    
    $( ".esempio" ).change(function(){
        var id = $(this).attr('ref');
        var order = $(this).val();
        $.post(Routing.generate('eph_domande-audit_text', {id: id, type: 'esempio'}), {text: order})
    });
});

function ricerca(id) {
    $.post(Routing.generate('eph_domande-audit_research', {id: id}), function(out){
        $('#search_'+id).removeClass(out.remove).addClass(out.add);
    });
}