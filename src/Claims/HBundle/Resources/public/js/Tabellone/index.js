$(document).ready(function() {
    $('span.label').click(function() {
        var tr = $(this).closest('tr');
        $('#cambia_priorita_a').text(tr.attr('titolo'));
        $('#priorita_id').val(tr.attr('id'));
        $('#priorita_priorita').val(tr.attr('priorita'));
        $('#bt_cambia_priorita').show();
        $('#wait_cambia_priorita').hide();
    });

    $('a.gestore').click(function() {
        var tr = $(this).closest('tr');
        $('#cambia_gestore_a').text(tr.attr('titolo'));
        $('#gestore_id').val(tr.attr('id'));
        $('#gestore_gestore').val(tr.attr('gestore'));
        $('#bt_cambia_gestore').show();
        $('#wait_cambia_gestore').hide();
    });

    var tipografy = '';
    $("div[class^='typography']").focus(function() {
        tipografy = $(this).html();
    }).blur(function() {
        if (tipografy !== $(this).html()) {
            var tr = $(this).closest('tr');
            var td = $(this).closest('td');
            if(td.hasClass('note')) {
                $('#cambia_note_a').text(tr.attr('titolo'));
                $('#note_id').val(tr.attr('id'));
                $('#note_note').val($(this).html().trim());
                assegnaNote();
            }
            if(td.hasClass('audit')) {
                $('#cambia_audit_a').text(tr.attr('titolo'));
                $('#audit_id').val(tr.attr('id'));
                $('#audit_audit').val($(this).html().trim());
                assegnaAudit();
            }
            if(td.hasClass('azioni')) {
                $('#cambia_azioni_a').text(tr.attr('titolo'));
                $('#azioni_id').val(tr.attr('id'));
                $('#azioni_azioni').val($(this).html().trim());
                assegnaAzioni();
            }
            if(td.hasClass('dati_recupero')) {
                $('#cambia_dati_recupero_a').text(tr.attr('titolo'));
                $('#dati_recupero_id').val(tr.attr('id'));
                $('#dati_recupero_dati_recupero').val($(this).html().trim());
                assegnaDatiRecupero();
            }
        }
    });
    $('.autoupdate').change(function() {
        _autoupdate($(this));
    });
    
    $('.goto-mr').click(function(){
        var to = $(this).attr('to');
        $('#'+to).focus().append('<br/>'+$(this).attr('title'));
    });

    $('td.stato_pratica').click(function() {
        var i = 0;
        var tr = $(this).closest('tr');
        $('#cambia_stato_a').text(tr.attr('titolo'));
        $('#stato_id').val(tr.attr('id'));
        $('#stato_stato').val(tr.attr('stato'));
        $('#bt_cambia_stato').show();
        $('#wait_cambia_stato').hide();
    });
});

function _autoupdate($this) {
    val = $this.val();
    pratica = $this.attr('pratica');
    field = $this.attr('name');
    if (pratica) {
        $.post(Routing.generate('claims_hospital_pratica_autoupdate', {'slug': pratica}), {'pratica': {'field': field, 'value': val}}, function(out) {
            $('.' + out.field).val(out.value);
        });
    }
}

function assegnaPriorita() {
    $('#bt_cambia_priorita').hide();
    $('#wait_cambia_priorita').show();
    var form = $('#cambia_priorita');
    $.post(Routing.generate('claims_hospital_cambia_priorita'), form.serialize(), function(out) {
        var riga = $('#' + $('#priorita_id').val());
        var label = riga.find('.label');
        var abbr = label.find('abbr');
        label = riga.find('.label');
        label.removeClass('label-normal')
                .removeClass('label-info')
                .removeClass('label-green')
                .removeClass('label-warning')
                .removeClass('label-important')
                .removeClass('label-success')
                .addClass(out.css);
        riga.attr('priorita', out.id);
        abbr.attr('title', out.label);
        $.fancybox.close();
    });
}

function assegnaGestore() {
    $('#bt_cambia_gestore').hide();
    $('#wait_cambia_gestore').show();
    var form = $('#cambia_gestore');
    $.post(Routing.generate('claims_hospital_cambia_gestore'), form.serialize(), function(out) {
        var riga = $('#' + $('#gestore_id').val());
        var a = riga.find('.gestore');
        a.html('<div class="user"><abbr title="{nome}">{sigla}</abbr></div>'.assign(out));
        riga.attr('gestore', out.slug);
        var label = riga.find('.label');
        var abbr = label.find('abbr');
        label = riga.find('.label');
        label.removeClass('label-normal')
                .removeClass('label-info')
                .removeClass('label-green')
                .removeClass('label-warning')
                .removeClass('label-important')
                .removeClass('label-success')
                .addClass(out.css);
        riga.attr('priorita', out.id);
        abbr.attr('title', out.label);
        var dasc = riga.find('.dasc').find('a');
        dasc.text(out.dasc);
        $.fancybox.close();
    });
}

function assegnaNote() {
    $('#bt_cambia_note').hide();
    $('#wait_cambia_note').show();
    var form = $('#cambia_note');
    $.post(Routing.generate('claims_hospital_cambia_note'), form.serialize(), function(out) {
        var riga = $('#' + $('#note_id').val());
        var note = riga.find('.note').find('a');
        note.text(out.note);
        var label = riga.find('.label');
        var abbr = label.find('abbr');
        label = riga.find('.label');
        label.removeClass('label-normal')
                .removeClass('label-info')
                .removeClass('label-green')
                .removeClass('label-warning')
                .removeClass('label-important')
                .removeClass('label-success')
                .addClass(out.css);
        riga.attr('priorita', out.id);
        abbr.attr('title', out.label);
        $.fancybox.close();
    });
}

function assegnaAudit() {
    $('#bt_cambia_audit').hide();
    $('#wait_cambia_audit').show();
    var form = $('#cambia_audit');
    $.post(Routing.generate('claims_hospital_cambia_audit'), form.serialize(), function(out) {
        var riga = $('#' + $('#audit_id').val());
        var audit = riga.find('.audit').find('a');
        audit.text(out.audit);
        var label = riga.find('.label');
        var abbr = label.find('abbr');
        label = riga.find('.label');
        label.removeClass('label-normal')
                .removeClass('label-info')
                .removeClass('label-green')
                .removeClass('label-warning')
                .removeClass('label-important')
                .removeClass('label-success')
                .addClass(out.css);
        riga.attr('priorita', out.id);
        abbr.attr('title', out.label);
        $.fancybox.close();
    });
}

function assegnaAzioni() {
    $('#bt_cambia_azioni').hide();
    $('#wait_cambia_azioni').show();
    var form = $('#cambia_azioni');
    $.post(Routing.generate('claims_hospital_cambia_azioni'), form.serialize(), function(out) {
        var riga = $('#' + $('#azioni_id').val());
        var azioni = riga.find('.azioni').find('a');
        azioni.text(out.azioni);
        var label = riga.find('.label');
        var abbr = label.find('abbr');
        label = riga.find('.label');
        label.removeClass('label-normal')
                .removeClass('label-info')
                .removeClass('label-green')
                .removeClass('label-warning')
                .removeClass('label-important')
                .removeClass('label-success')
                .addClass(out.css);
        riga.attr('priorita', out.id);
        abbr.attr('title', out.label);
        $.fancybox.close();
    });
}

function assegnaDatiRecupero() {
    $('#bt_cambia_dati_recupero').hide();
    $('#wait_cambia_dati_recupero').show();
    var form = $('#cambia_dati_recupero');
    $.post(Routing.generate('claims_hospital_cambia_dati_recupero'), form.serialize(), function(out) {
        var riga = $('#' + $('#dati_recupero_id').val());
        var dati_recupero = riga.find('.dati_recupero').find('a');
        dati_recupero.text(out.dati_recupero);
        var label = riga.find('.label');
        var abbr = label.find('abbr');
        label = riga.find('.label');
        label.removeClass('label-normal')
                .removeClass('label-info')
                .removeClass('label-green')
                .removeClass('label-warning')
                .removeClass('label-important')
                .removeClass('label-success')
                .addClass(out.css);
        riga.attr('priorita', out.id);
        abbr.attr('title', out.label);
        $.fancybox.close();
    });
}

function assegnaStato() {
    $('#bt_cambia_stato').hide();
    $('#wait_cambia_stato').show();
    var form = $('#cambia_stato');
    $.post(Routing.generate('claims_hospital_cambia_stato'), form.serialize(), function(out) {
        var riga = $('#' + $('#stato_id').val());
        var stato = riga.find('.stato_pratica').find('a');
        stato.text(out.stato);
        riga.attr('stato', out.stato_id);
        var label = riga.find('.label');
        var abbr = label.find('abbr');
        label = riga.find('.label');
        label.removeClass('label-normal')
                .removeClass('label-info')
                .removeClass('label-green')
                .removeClass('label-warning')
                .removeClass('label-important')
                .removeClass('label-success')
                .addClass(out.css);
        riga.attr('priorita', out.id);
        abbr.attr('title', out.label);
        $.fancybox.close();
    });
}