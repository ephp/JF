$(document).ready(function(){
    $('.fakefile').hide();
}); 
function callbackDragDrop(item, numero_foto) {
    console.log(item);
    $('.thumbnails').html('<li>Wait for refresh file list</li>');
    $('#file_id').val(item.id);
    $form = $('#form_files');
    $.post($form.attr('action'), $form.serialize(), function(html){
        $('#files').html(html);
        $('.thumbnails').html('');
    });
}
