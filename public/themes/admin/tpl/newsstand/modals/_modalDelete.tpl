<div class="modal hide fade" id="modal-kiosko-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete kiosko{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure that do you want to delete the selected cover?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
(function($){
    var modal_delete = $("#modal-kiosko-delete");
    modal_delete.modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false
    });

    $('.del').click(function(e) {
        e.preventDefault();
        modal_delete.data("selected-for-del", $(this).data("url"));
        //Sets up the modal
        modal_delete.modal('show');
    });

    $('#modal-kiosko-delete a.btn.yes').on('click', function(e, ui){
        e.preventDefault();
        var url = modal_delete.data("selected-for-del");
        if (url) {
            $.ajax({
                url:  url,
                success: function(){
                    location.reload();
                }
            });
        }
    });

    $('#modal-kiosko-delete a.btn.no').on('click', function(e){
        modal_delete.modal('hide');
        e.preventDefault();
    });
})(jQuery);

</script>