<div class="modal hide fade" id="modal-container-active">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}There is no active container{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}You must select a container{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn primary accept" href="#">{t}Accept{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-container-active").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

</script>