<div class="modal hide fade" id="modal-delete-contents">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal-delete-contents" aria-hidden="true">×</button>
      <h3>{t}Delete contents{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}Are you sure you want to delete the selected contents?{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-delete-contents").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

jQuery('#modal-delete-contents a.btn.yes').on('click', function(){
    var form = jQuery('#trashform');
    form.attr('action', '{url name=admin_trash_batchdelete}');
    form.submit();
});

jQuery('#modal-delete-contents a.btn.no').on('click', function(e){
    jQuery("#modal-delete-contents").modal('hide');
    e.preventDefault();
});
</script>