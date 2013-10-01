$(document).ready(function() {
    sanitizeCurrency([$('.currency')]);
    sanitizeDate([$('.auto_date')]);
    sanitizeUrl([$('.auto_url')]);
//    $(".autogrow").autoGrow();
    autoupdate();
    checkUm();
});

function aggiungiEvento() {
    $('#bt_aggiungi_evento').hide();
    $('#wait_aggiungi_evento').show();
    var form = $('#aggiungi_evento');
    $.post(Routing.generate('claims_hospital_aggiungi_evento', {'slug': slug}), form.serialize(), function(out) {
        $('#tab_cal').html(out);
        autoupdate();
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
        span.attr('style', 'background-color: '+out.color);
    });
}

function cancellaEvento(id, testo) {
    if (confirm("Vuoi cancellare l'evento \"" + testo + "\"")) {
        $.post(Routing.generate('claims_hospital_cancella_evento'), {'evento': {'id': id}}, function(out) {
            $('#tab_cal').html(out);
            autoupdate();
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

function autoupdate() {
    $('.autoupdate').change(function() {
        val = $(this).val();
        pratica = $(this).attr('pratica');
        evento = $(this).attr('evento');
        field = $(this).attr('name');
        if (pratica) {
            $.post(Routing.generate('claims_hospital_pratica_autoupdate', {'slug' : slug}), {'pratica': {'field': field, 'value': val}}, function(out) {
                checkUm();
            });
        } else {
            $.post(Routing.generate('claims_hospital_evento_autoupdate'), {'evento': {'id': evento, 'field': field, 'value': val}}, function(out) {
                if (out.reload === 1) {
                    window.location = window.location;
                }
            });
        }
    });
    
    $('.star').click(function(){
        evidenziaEvento($(this).attr('evento'));
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

function importaRavinale() {
    var form = $('#form_ravinale');
    $.post(form.attr('action'), form.serialize(), function(out) {
        $('#tab_cal').html(out);
        autoupdate();
        $( ".tabbable" ).tabs({ active: 0 });
        form[0].reset();
    });
}