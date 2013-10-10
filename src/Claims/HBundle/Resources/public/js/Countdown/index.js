$(document).ready(function() {
    cambiaGestore();
});

function cambiaGestore() {
    $('.chg').click(function() {
        tr = $(this).closest('tr');
        $('#assegna_gestore_a').text(tr.attr('titolo'));
        $('#cd_id').val(tr.attr('cd'));
        $('#cd_gestore').val(tr.attr('gestore'));
    });
}

function assegnaGestore() {
    form = $('#assegna_gestore_scheda');
    $.post(Routing.generate('claims_h_countdown_assegna_gestore'), form.serialize(), function(out) {
        window.location = out.redirect;
    });
}

function leggi(id) {
    $('.rh').hide();
    $(id).show();
    $(id).find('div').show();
}
var risposta = '';
function rispondi(id) {
    $(id).show();
    $(id).find('div').show();
    risposta = id;
    attach = [];
    $('.tmb').hide();
}
var idn = 0;
var attach = [];
function multiUploadDoneCB(response) {
    $(risposta + '_ul').append($('#li').html().assign(response).assign({id: ++idn}));
    attach = eval($(risposta + '_docs').val());
    attach.add(response.url);
    $(risposta + '_docs').val(JSON.stringify(attach));
    $('.tmb').hide();
}
function cancella(id, url) {
    $.post(url, {}, function(output) {
        console.log(output);
        $('#' + id).remove();
    });
}
function invia(id) {
    console.log($('#' + id));
    $('#' + id).submit();
}        