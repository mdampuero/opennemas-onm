<div class="modal hide fade" id="modal-album-accept">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal-album-accept" aria-hidden="true">×</button>
      <h3>{t}Delete albums{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}You must select some elements.{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary accept" href="#">{t}Accept{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-album-accept").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});
jQuery('#modal-album-accept a.btn.accept').on('click', function(e){
    jQuery("#modal-album-accept").modal('hide');
    e.preventDefault();
});
</script>