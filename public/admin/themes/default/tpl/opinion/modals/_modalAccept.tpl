<div class="modal hide fade" id="modal-opinion-accept">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete opinions{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}You must select some elements.{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn primary accept" href="#">{t}Accept{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-opinion-accept").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});
jQuery('#modal-opinion-accept a.btn.accept').on('click', function(e){
    jQuery("#modal-opinion-accept").modal('hide');
    e.preventDefault();
});
</script>