$(document).ready(function() {
    sanitizeCurrency([$('.currency')]);
    sanitizeCurrencyNp([$('.currencynp')]);
    sanitizeDate([$('.auto_date')]);
    sanitizeUrl([$('.auto_url')]);
    autoupdate();
    checkUm();
    
    $('.goto').click(function(){
        var from = $(this).attr('from');
        var to = $(this).attr('to');
        if(from) {
            $('#'+to).focus().val($('#'+to).val()+'<br/>'+$('#'+from).val());
        } else {
            $('#'+to).focus().append('<br/>'+$(this).attr('title'));
        }
    });
});

function aggiungiEvento() {
    $('#bt_aggiungi_evento').hide();
    $('#wait_aggiungi_evento').show();
    var form = $('#aggiungi_evento');
    $.post(Routing.generate('claims_hospital_aggiungi_evento', {'slug': slug}), form.serialize(), function(out) {
        $('#tab_cal').html(out);
        autoupdateCalendario();
        $.fancybox.close();
        form[0].reset();
        $('#bt_aggiungi_evento').show();
        $('#wait_aggiungi_evento').hide();
    });
}

function evidenziaEvento(id) {
    $.post(Routing.generate('claims_hospital_evidenzia_evento'), {'evento': {'id': id}}, function(out) {
        var span = $('#' + out.id).find('span');
        span.removeClass(out.remove);
        span.addClass(out.add);
        span.attr('style', 'background-color: ' + out.color);
    });
}

function recupero(slug) {
    window.location = Routing.generate('claims_hospital_cambia_recupero', {'slug': slug});
}

function cancellaEvento(id, testo) {
    if (confirm("Vuoi cancellare l'evento \"" + testo + "\"")) {
        $.post(Routing.generate('claims_hospital_cancella_evento'), {'evento': {'id': id}}, function(out) {
            $('#tab_cal').html(out);
            autoupdateCalendario();
        });
    }
}

function aggiungiLink() {
    $('#bt_aggiungi_link').hide();
    $('#wait_aggiungi_link').show();
    var form = $('#aggiungi_link');
    $.post(Routing.generate('claims_hospital_aggiungi_link', {'slug': slug}), form.serialize(), function(out) {
        $('#tab_link').html(out);
        $.fancybox.close();
        form[0].reset();
        $('#bt_aggiungi_link').show();
        $('#wait_aggiungi_link').hide();
    });
}

function cancellaLink(id, testo) {
    if (confirm("Vuoi cancellare il link a \"" + testo + "\"")) {
        $.post(Routing.generate('claims_hospital_cancella_link'), {'link': {'id': id}}, function(out) {
            $('#tab_link').html(out);
        });
    }
}

function _autoupdate($this) {
    val = $this.val();
    pratica = $this.attr('pratica');
    evento = $this.attr('evento');
    report = $this.attr('report');
    field = $this.attr('name');
    if (pratica) {
        $.post(Routing.generate('claims_hospital_pratica_autoupdate', {'slug': slug}), {'pratica': {'field': field, 'value': val}}, function(out) {
            $('.' + out.field).val(out.value);
            checkUm();
            if (out.reload) {
                $('#tab_cal').html(out.calendario);
                autoupdateCalendario();
            }
        });
    } else if (report) {
        field = field.from(7).to(-1);
        $.post(Routing.generate('claims_hospital_report_pratica_autoupdate', {'slug': slug, 'numero': $('#' + report).val()}), {'report': {'field': field, 'value': val}}, function(out) {
        });
    } else {
        $.post(Routing.generate('claims_hospital_evento_autoupdate'), {'evento': {'id': evento, 'field': field, 'value': val}}, function(out) {
            if (out.reload === 1) {
                window.location = window.location;
            }
        });
    }
}

function autoupdate() {
//    $('.autoupdate').change(function() {
//        _autoupdate($(this));
//    });
    $('.autoupdate').blur(function() {
        _autoupdate($(this));
    });

    $('.star').click(function() {
        evidenziaEvento($(this).attr('evento'));
    });
    
    $('.note div').blur(function(){
        val = $(this).html();
        evento = $(this).attr('evento');
        field = $(this).attr('name');
        $.post(Routing.generate('claims_hospital_evento_autoupdate'), {'evento': {'id': evento, 'field': field, 'value': val}}, function(out) {
            if (out.reload === 1) {
                window.location = window.location;
            }
        });
    });
}

function autoupdateCalendario() {
    $('#tab_cal').find('.autoupdate').change(function() {
        _autoupdate($(this));
    });

    $('.star').click(function() {
        evidenziaEvento($(this).attr('evento'));
    });
    
    $('.note div').blur(function(){
        val = $(this).html();
        evento = $(this).attr('evento');
        field = $(this).attr('name');
        $.post(Routing.generate('claims_hospital_evento_autoupdate'), {'evento': {'id': evento, 'field': field, 'value': val}}, function(out) {
            if (out.reload === 1) {
                window.location = window.location;
            }
        });
    });

    $('a.fancybox').each(function() {
        if ($(this).attr('href').startsWith('#')) {
            $(this).fancybox({
                hideOnOverlayClick: false,
                transitionIn: 'elastic',
                padding: 3,
                margin: 0
            });
        } else {
            $(this).fancybox({
                type: 'ajax',
                hideOnOverlayClick: false,
                transitionIn: 'elastic',
                padding: 3,
                margin: 0
            });
        }
    });
}

function checkUm() {
    $('.um').each(function() {
        val = $('#' + $(this).attr('target')).val();
        if (val) {
            if (parseFloat(val) <= 100) {
                $(this).text('%');
            } else {
                $(this).text('€');
            }
        } else {
            $(this).text('');
        }
    });
}

function importaContec() {
    var form = $('#form_contec');
    $.post(form.attr('action'), form.serialize(), function(out) {
        $('#tab_cal').html(out);
        autoupdateCalendario();
        $(".tabbable").tabs({active: 0});
        form[0].reset();
    });
}

function importaRavinale() {
    var form = $('#form_ravinale');
    $.post(form.attr('action'), form.serialize(), function(out) {
        $('#tab_cal').html(out);
        autoupdateCalendario();
        $(".tabbable").tabs({active: 0});
        form[0].reset();
    });
}

function sanitizeCurrencyNp(fields) {
    fields.forEach(function(field) {
        field.change(function() {
            if ($(this).val().match(/N(.)?P(.)?/i)) {
                $(this).val('N.P.');
            } else {
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
            }
        });
    });
}

function loadAjax(a) {
    var url = a.attr('href');
    var tab = a.closest('.tab-pane');
    tab.addClass('wait');
    $.post(url, function(out) {
        tab.html(out);
        tab.removeClass('wait');
        autoupdate();
    });
    return false;
}