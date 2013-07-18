<div class="modal hide fade" id="modal-delete-server">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Remove the server configuration{/t}</h3>
    </div>
    <div class="modal-body">
        <p>
            {t escape=off}Are you sure that you want to delete the selected server configuration?{/t}
        </p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Remove{/t}</a>
        <a class="btn no" href="#">{t}Cancel{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#modal-delete-server').modal({
        backdrop: 'static',
        keyboard: true,
        show: false
    });

    jQuery('.del-server').on('click',function(e, ui) {
        e.preventDefault();
        $('#modal-delete-server').modal('show');
        $("body").data("selected-for-del", $(this).data("url"));
    });

    $('#modal-delete-server').on('click', 'a.btn.yes', function(e, ui) {
        e.preventDefault();
        var url = $("body").data("selected-for-del");
        if (url) {
            $.ajax({
                url:  url,
                success: function(){
                    location.reload();
                }
            });
        }
    });

    $('#modal-delete-server').on('click', 'a.btn.no', function(e,ui) {
        e.preventDefault();
        $('#modal-delete-server').modal('hide');
    });
});
</script>