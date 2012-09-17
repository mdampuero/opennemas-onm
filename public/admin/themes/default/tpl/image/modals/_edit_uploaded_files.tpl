<div class="modal hide fade" id="modal-edit-uploaded-files">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal-edit-uploaded-files" aria-hidden="true">×</button>
      <h3>{t}Files uploaded successfully{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}The dropped files are uploaded but you should complete their information.{/t}</p>
        <p>{t}You can:{/t}</p>
        <ul>
            <li>{t}Edit them now{/t}</li>
            <li>{t}Upload more images and complete the information later{/t}</li>
        </ul>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Edit them{/t}</a>
        <a class="btn no" href="#">{t}Upload more images{/t}</a>
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