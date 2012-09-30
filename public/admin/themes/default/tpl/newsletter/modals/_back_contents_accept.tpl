<div class="modal hide fade" id="modal-newsletter-accept">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Potential lose of the actual generated HTML.{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}This newsletter already have a generated HTML, potentially changed by you.<br>If you update the newsletter contents the HTML will overwrite the actual HTML.{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary accept" href="#">{t}Overwrite actual HTML{/t}</a>
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