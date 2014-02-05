$(document).ready(function() {
    sanitizeCurrency([$('.currency')]);
    autoupdate();
});

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

function rispondiGruppo() {
    $.post(Routing.generate('claims-h-audit-risposte'), $('#risposta').serialize(), function(html) {
        $('#question').html(html);
    });
}
function paginaGruppo(audit, ordine, pratica) {
    $.post(Routing.generate('claims-h-audit-get-risposte', {'id': audit, 'ordine': ordine, 'pratica': pratica}), function(html) {
        $('#question').html(html);
    });
}


function _autoupdate($this) {
    val = $this.val();
    pratica = $this.attr('pratica');
    field = $this.attr('name');
        $.post(Routing.generate('claims-h-audit-autoupdate', {'slug': pratica}), {'pratica': {'field': field, 'value': val}}, function(out) {
            console.log(out);
        });
}

function autoupdate() {
//    $('.autoupdate').change(function() {
//        _autoupdate($(this));
//    });
    var old = null;
    $('.autoupdate').focus(function(){
        old = $(this).val();
    }).blur(function() {
        if($(this).val() !== old) {
            _autoupdate($(this));
        }
        old = null;
    });
}

