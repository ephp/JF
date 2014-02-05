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


function sanitizePartialDate(fields) {
    fields.forEach(function(field) {
        field.change(function() {
            value = $(this).val().toLowerCase();
            if(value === '') {
                return false;
            }
            d = new Date();
            if (value.endsWith('gg') || value === 'oggi' || value === 'domani') {
                switch (value) {
                    case '0gg':
                    case 'oggi':
                        break;
                    case '1gg':
                    case 'domani':
                        d = calcolaData(d, 1);
                        break;
                    default:
                        nd = parseInt(value.substr(0, value.length - 2));
                        d = calcolaData(d, nd);
                        break;
                }
                g = d.getUTCDate() < 10 ? '0'+d.getUTCDate() : d.getUTCDate();
                m = d.getUTCMonth() < 9 ? '0'+(d.getUTCMonth() + 1) : (d.getUTCMonth() + 1);
                a = d.getUTCFullYear();
                $(this).val(g+'/'+m+'/'+a);
            } else {
                numeri = $(this).val().replace(/\-/g, "/").replace(/\./g, "/").replace(/\//g, " ").words();
                if(numeri.length === 3) {
                    d.setUTCDate(parseInt(numeri[0], 10));
                    d.setUTCMonth(parseInt(numeri[1], 10));
                    d.setUTCFullYear(parseInt(numeri[2], 10) < 100 ? 2000 + parseInt(numeri[2], 10) : parseInt(numeri[2], 10));
                } 
                if(numeri.length === 2) {
                    d.setUTCDate(1);
                    d.setUTCMonth(parseInt(numeri[0], 10));
                    d.setUTCFullYear(parseInt(numeri[1], 10) < 100 ? 2000 + parseInt(numeri[1], 10) : parseInt(numeri[1], 10));
                } 
                if(numeri.length === 1) {
                    d.setUTCDate(1);
                    d.setUTCMonth(1);
                    d.setUTCFullYear(parseInt(numeri[0], 10) < 100 ? 2000 + parseInt(numeri[0], 10) : parseInt(numeri[0], 10));
                } 
                g = parseInt(d.getUTCDate()) < 10 ? '0'+d.getUTCDate() : d.getUTCDate();
                m = parseInt(d.getUTCMonth()) + 1 < 10 ? '0'+(parseInt(d.getUTCMonth()) + 1) : (parseInt(d.getUTCMonth()) + 1);
                a = d.getUTCFullYear() - (parseInt(m) === 12 ? 1 : 0);
                if(numeri.length === 1) {
                    $(this).val(a);
                } else if(numeri.length === 2) {
                    $(this).val(m+'/'+a);
                } else {
                    $(this).val(g+'/'+m+'/'+a);
                }
            }
        });
    });
}

function sanitizeCurrencyFormat(fields) {
    fields.forEach(function(field) {
        field.change(function() {
            value = $(this).val().replace(",", ".").remove(/[^0-9\.]/g);
            n = 0;
            i = 0;
            nc = 0;
            value.chars(function(c) {
                if (c === '.') {
                    n++;
                }
                if (n === 2) {
                    i = nc;
                    n++;
                }
                nc++;
            });
            if (n > 1) {
                value = value.substring(0, i);
                value = Math.abs(parseFloat(value === '' || value === '.' ? 0 : value));
                $(this).val(value.toFixed(2));
            } else {
                value = Math.abs(parseFloat(value === '' || value === '.' ? 0 : value));
                $(this).val(value.toFixed(2));
            }
            $(this).val(parseFloat($(this).val()).format(2, '.', ','));
        });
    });
}
