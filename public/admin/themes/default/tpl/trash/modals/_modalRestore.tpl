<div class="modal hide fade" id="modal-restore-contents">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal-restore-contents" aria-hidden="true">×</button>
      <h3>{t}Restore contents from trash{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}Are you sure you want to restore from trash the selected contents?{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, restore{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-restore-contents").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

jQuery('#modal-restore-contents a.btn.yes').on('click', function(){
    var form = jQuery('#trashform');
    form.attr('action', '{url name=admin_trash_batchrestore}');
    form.submit();
});

jQuery('#modal-restore-contents a.btn.no').on('click', function(e){
    jQuery("#modal-restore-contents").modal('hide');
    e.preventDefault();
});
</script>