<div class="modal hide fade" id="modal-edit-uploaded-files">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Files uploaded successfully{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}The dropped files are uploaded but you must .{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Edit them{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-edit-uploaded-files").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});
jQuery('#modal-edit-uploaded-files a.btn.yes').on('click', function(e){
    e.preventDefault();
    var fileIds = '';
    jQuery('.file-id').each(function(){
        fileIds += "&id[]="+jQuery(this).val();
    });
    window.location = image_uploader.show_url + fileIds;
    jQuery("#modal-edit-uploaded-files").modal('hide');
});
jQuery('#modal-edit-uploaded-files a.btn.no').on('click', function(e){
    jQuery("#modal-edit-uploaded-files").modal('hide');
});
</script>