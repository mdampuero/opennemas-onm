<div class="modal hide fade" id="modal-opinion-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal-opinion-delete" aria-hidden="true">×</button>
      <h3>{t}Delete opinion{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure that do you want delete "<span>%title%</span>"?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $("#modal-opinion-delete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false
    });

    $('.del').click(function(e, ui) {
        e.preventDefault();
        $('#modal-opinion-delete .modal-body span').html( $(this).data('title') );
        $("#modal-opinion-delete ").modal('show');
        $("body").data("selected-for-del", $(this).data("url"));
    });

    $('#modal-opinion-delete a.btn.yes').on('click', function(e, ui) {
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

    $('#modal-opinion-delete a.btn.no').on('click', function(e, ui){
        $("#modal-opinion-delete").modal('hide');
        e.preventDefault();
    });
});
</script>