<div class="modal hide fade" id="modal-news-agency-batch-import">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Import all elements{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to import <span>%num%</span> elemets?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, import all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery(document).ready(function ($){
    $("#modal-news-agency-batch-import").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    $('.importChecked').on('click', function(e, ui) {
        var number = $(".minput:checked").length;
        if(number >= 1 ) {
            $('#modal-news-agency-batch-import .modal-body span').html(number);
            $("#modal-news-agency-batch-import").modal('show');
        } else {
            $("#modal-news-agency-batch-import").modal('hide');
            $("#modal-instance-accept").modal('show');
            $('#modal-instance-accept .modal-body')
                .html("{t}You must select some elements to import{/t}");
        }

        e.preventDefault();
    });

    $('#modal-news-agency-batch-import a.btn.yes').on('click', function(e, ui){
        $('#formulario').attr('action', '{url name="admin_news_agency_batch_import"}');
        $('#formulario').submit();
        e.preventDefault();
    });

    $('#modal-news-agency-batch-import a.btn.no').on('click', function(e, ui){
        $("#modal-news-agency-batch-import").modal('hide');
        e.preventDefault();
    });
});
</script>
