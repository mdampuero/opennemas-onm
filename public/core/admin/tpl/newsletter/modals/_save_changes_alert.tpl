<div class="modal hide fade" id="modal-save-changes">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Save changes{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}You must save a changes or back to list{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes accept" href="#">{t}Accept{/t}</a>

    </div>

<script>
jQuery("#modal-save-changes").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

jQuery('#modal-save-changes a.btn.accept').on('click', function(e){
    jQuery("#modal-save-changes").modal('hide');
    e.preventDefault();

});
</script>