<div class="modal hide fade" id="modal-upgrade-instance">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Upgrade instance{/t}</h3>
    </div>
    <div class="modal-body">
        <h5>
            {t}Are you sure you want to proceed with this upgrade ?{/t}
        </h5>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Request upgrade{/t}</a>
        <a class="btn secondary no" href="#">{t}Cancel{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $("#modal-upgrade-instance").modal({
        backdrop: 'static',
        keyboard: true,
        show: false,
    });

    $('#modal-upgrade-instance a.btn.yes').on('click', function() {
        jQuery('#upgrade-form').submit();
    });

    $('#modal-upgrade-instance a.btn.no').on('click', function(e){
        e.preventDefault();
        $("#modal-upgrade-instance").modal('hide');
    });
});
</script>