<div class="modal hide fade" id="modal-image-accept">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete multiple images{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}You must select some elements.{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary accept" href="#">{t}Accept{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-image-accept").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});
jQuery('#modal-image-accept a.btn.accept').on('click', function(e){
    jQuery("#modal-image-accept").modal('hide');
    e.preventDefault();
});
</script>