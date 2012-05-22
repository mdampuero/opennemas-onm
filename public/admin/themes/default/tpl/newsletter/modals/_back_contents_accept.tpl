<div class="modal hide fade" id="modal-newsletter-accept">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Back to list contents{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}If you go back, you may lose some changes in newsletter{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn primary accept" href="#">{t}Yes, Go Back{/t}</a>
        <a class="btn secondary no" href="#">{t}Cancel{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-newsletter-accept").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

</script>