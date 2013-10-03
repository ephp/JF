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
    $('.typography').focus(function() {
        tipografy = $(this).html();
    }).blur(function() {
        if (tipografy !== $(this).html()) {
            var tr = $(this).closest('tr');
            $('#cambia_note_a').text(tr.attr('titolo'));
            $('#note_id').val(tr.attr('id'));
            $('#note_note').val($(this).html().trim());
            assegnaNote();
        }
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