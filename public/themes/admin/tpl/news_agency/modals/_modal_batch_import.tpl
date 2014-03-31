<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h3>{t}Import selected items{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure you want to import [% selected %] elemets?{/t}</p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="deleteSelected(route)" type="button">{t}Yes, import them{/t}</button>
    <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
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
        $('#formulario').attr('method', 'POST');
        $('#formulario').submit();
        e.preventDefault();
    });

    $('#modal-news-agency-batch-import a.btn.no').on('click', function(e, ui){
        $("#modal-news-agency-batch-import").modal('hide');
        e.preventDefault();
    });
});
</script>
