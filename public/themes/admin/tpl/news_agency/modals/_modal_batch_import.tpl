<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h3>{t}Import selected items{/t}</h3>
</div>
<div class="modal-body">
    <p>{t escape=off}Are you sure you want to import [% selected %] elemets?{/t}</p>
</div>
<div class="modal-footer">
    <span class="loading" ng-if="deleting == 1"></span>
    <button class="btn btn-primary" ng-click="importSelected(route)" type="button">{t}Yes, import them{/t}</button>
    <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
</div>

<script>
jQuery(document).ready(function ($){
    $('#modal-news-agency-batch-import a.btn.yes').on('click', function(e, ui){
        $('#formulario').attr('action', '{url name="admin_news_agency_batch_import"}');
        $('#formulario').attr('method', 'POST');
        $('#formulario').submit();
        e.preventDefault();
    });
});
</script>
