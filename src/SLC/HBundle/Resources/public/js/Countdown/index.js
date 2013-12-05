$(document).ready(function() {
    cambiaGestore();
});

function cambiaGestore() {
    $('a.gestore').click(function() {
        var tr = $(this).closest('tr');
        $('#cambia_gestore_a').text(tr.attr('titolo'));
        $('#gestore_id').val(tr.attr('id'));
        $('#gestore_gestore').val(tr.attr('gestore'));
        $('#bt_cambia_gestore').show();
        $('#wait_cambia_gestore').hide();
    });
}

function assegnaGestore() {
    $('#bt_cambia_gestore').hide();
    $('#wait_cambia_gestore').show();
    var form = $('#cambia_gestore');
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