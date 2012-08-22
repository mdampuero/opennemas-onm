<div class="modal hide fade" id="modal-article-delete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete article{/t}</h3>
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
    $("#modal-article-delete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false
    });

    $('.del').click(function(e, ui) {
        e.preventDefault();
        $('#modal-article-delete .modal-body span').html( $(this).data('title') );
        $("#modal-article-delete ").modal('show');
        $("body").data("selected-for-del", $(this).data("url"));
    });

    $('#modal-article-delete a.btn.yes').on('click', function(e, ui) {
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

    $('#modal-article-delete a.btn.no').on('click', function(e, ui){
        $("#modal-article-delete").modal('hide');
        e.preventDefault();
    });
});
</script>