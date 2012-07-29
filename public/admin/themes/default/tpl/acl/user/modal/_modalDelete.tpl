<div class="modal hide fade" id="modal-user-delete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
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
    $("#modal-user-delete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true //Can close on escape
    });

    $('.del').click(function(e, ui) {
        e.preventDefault();
        $('#modal-user-delete .modal-body span').html( $(this).data('title') );
        $("#modal-user-delete ").modal('show');
        $("body").data("selected-for-del", $(this).data("url"));
    });

    $('#modal-user-delete a.btn.yes').on('click', function(e, ui) {
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

    $('#modal-user-delete a.btn.no').on('click', function(e, ui){
        $("#modal-user-delete").modal('hide');
        e.preventDefault();
    });
});
</script>