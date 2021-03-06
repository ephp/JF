$(document).ready(function() {
    
    sanitizeDate([$('.auto_date')]);
    
    $("#mydatepicker").datepicker({
        dateFormat: 'dd-mm-yy',
        closeText: 'Chiudi',
        prevText: 'Perc.',
        nextText: 'Prox.',
        currentText: 'Oggi',
        monthNames: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
        monthNamesShort: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
        dayNames: ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'],
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'],
        dayNamesMin: ['D', 'L', 'Ma', 'Me', 'G', 'V', 'S'],
        firstDay: 1,
        weekHeader: "S",
        onSelect: function(date) {
            date = Date.create(date, 'it');
            $.post(Routing.generate('calendario_personale_giorno', {giorno: date.getDate(), mese: date.getMonth() + 1, anno: date.getFullYear()}), {}, function(data) {
                setGiorno(giorni);
                $('#daily').html(data);
            });
        },
        onChangeMonthYear: function(year, month) {
            $.post(Routing.generate('calendario_personale_json', {mese: month, anno: year}), {}, function(data) {
                giorni = data;
                setGiorno(giorni);
                $('#daily').html('');
            });
        }
    });

    setGiorno(giorni);

    $('.ads').click(function() {
        $('#evento_intero').val($(this).html());
        if ($('#evento_intero').val() === 'Sì') {
            $('#ad').show();
        } else {
            $('#ad').hide();
        }
        $('.ads').toggle();
    });
});

function setGiorno(giorni) {
    $.each(giorni, function(giorno, val) {
        console.log(giorno);
        $('.calendar td').find('a').each(function() {
            $this = $(this);
            if (parseInt($this.html()) === parseInt(giorno)) {
                $td = $this.closest('td');
                $td.html($this);
                $.each(val.tipo, function(label, v) {
                    $this.after('<div class="nscal ' + v.css + '" title="' + label + ': ' + v.n + ' event' + (v.n === 1 ? 'o' : 'i') + '">' + v.n + '</div>');
                });
                $this.after('<div class="ncal">' + val.tot + '</div>');
            }
        });
    });
} 

function aggiungiEvento() {
    $('#bt_aggiungi_evento').hide();
    $('#wait_aggiungi_evento').show();
    var form = $('#aggiungi_evento');
    $.post(Routing.generate('calendar_aggiungi_evento'), form.serialize(), function(out) {
        $.post(Routing.generate('calendario_personale_json', {mese: out.month, anno: out.year}), {}, function(data) {
            giorni = data;
            setGiorno(giorni);
            $('#daily').html('');
        });
        $.fancybox.close();
        if($('#evento_intero').val() === 'Sì'){
            $('.ads').toggle();
        }
        form[0].reset();
        $('#ad').show();
        $('#bt_aggiungi_evento').show();
        $('#wait_aggiungi_evento').hide();
    });
}

function autoupdateCalendario() {
    $('#tab_cal').find('.autoupdate').change(function() {
        _autoupdate($(this));
    });

    $('.star').click(function() {
        evidenziaEvento($(this).attr('evento'));
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
