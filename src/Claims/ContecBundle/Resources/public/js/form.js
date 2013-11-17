$(document).ready(function() {
    $('.fakefile').hide();
});

function callbackDragDrop(item, numero_foto) {
    $('.controls').hide();
    $('.thumbnails').hide().after('<div class="alert alert-block"><strong>Attendere:</strong> Elaborazione in corso. L\'operazione può richiedere alcuni minuti</div>');
    $.post(Routing.generate('contec_import_callback', {'file': item.url, 'mr': $('#mr').attr('checked')}), function(html) {
        $('.content').html(html);
        $.ajax({
            url: item.deleteUrl,
            type: 'DELETE',
            success: function(result) {
                console.log(result);
            }
        });
    });
}
