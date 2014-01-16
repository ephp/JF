function rispondi() {
    $.post(Routing.generate('claims-h-audit-risposta'), $('#risposta').serialize(), function(html) {
        $('#question').html(html);
    });
}
function pagina(audit, ordine, pratica) {
    $.post(Routing.generate('claims-h-audit-get-risposta', {'id': audit, 'ordine': ordine, 'pratica': pratica}), function(html) {
        $('#question').html(html);
    });
}
