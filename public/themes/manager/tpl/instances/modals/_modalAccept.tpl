<div class="modal hide fade" id="modal-instance-accept">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete instances{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}You must select some instances.{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary accept" href="#">{t}Accept{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function($){
    $("#modal-instance-accept").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false
    });
    $('#modal-instance-accept a.btn.accept').on('click', function(e){
        $("#modal-instance-accept").modal('hide');
        e.preventDefault();
    });
})
</script>
