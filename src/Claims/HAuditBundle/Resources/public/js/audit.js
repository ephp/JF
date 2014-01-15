$(document).ready(function() {
    $('.fakefile').hide();
});

function callbackDragDrop(item, numero_foto) {
    $('.controls').hide();
    $('.thumbnails').hide().after('<div class="alert alert-block"><strong>Attendere:</strong> Elaborazione in corso. L\'operazione può richiedere alcuni minuti</div>');
    $.post(Routing.generate('claims-h-audit-file-callback', {'file': item.url, 'audit': $('#audit').val()}), function(html) {
        $('#form').html(html);
        $.ajax({
            url: item.deleteUrl,
            type: 'DELETE',
            success: function(result) {
                console.log(result);
            }
        });
    });
}
