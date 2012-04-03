<div class="modal hide fade" id="modal-author-accept">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete authors{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}You must select some elements.{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn primary accept" href="#">{t}Accept{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-author-accept").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});
jQuery('#modal-author-accept a.btn.accept').on('click', function(e){
    jQuery("#modal-author-accept").modal('hide');
    e.preventDefault();
});
</script>