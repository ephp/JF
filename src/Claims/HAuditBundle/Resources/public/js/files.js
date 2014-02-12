$(document).ready(function(){
    $('.fakefile').hide();
}); 
function callbackDragDrop(item, numero_foto) {
    console.log(item);
    $('#file_id').val(item.id);
    $form = $('#form_files');
    $.post($form.attr('action'), $form.serialize(), function(html){
        $('#files').html(html);
    });
}
