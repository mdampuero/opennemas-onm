<div class="modal hide fade" id="modal-user-group-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete user{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure that do you want to delete "<span>%title%</span>"?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $("#modal-user-group-delete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    $('.del').click(function(e, ui) {
        e.preventDefault();
        $('#modal-user-group-delete .modal-body span').html( $(this).data('title') );
        $("#modal-user-group-delete ").modal('show');
        $("body").data("selected-for-del", $(this).data("url"));
    });

    $('#modal-user-group-delete a.btn.yes').on('click', function(e, ui) {
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

    $('#modal-user-group-delete a.btn.no').on('click', function(e, ui){
        $("#modal-user-group-delete").modal('hide');
        e.preventDefault();
    });
});
</script>