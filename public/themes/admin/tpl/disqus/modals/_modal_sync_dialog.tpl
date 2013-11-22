<div class="modal hide fade" id="modal-sync">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Syncing from Disqus{/t}</h3>
    </div>
    <div class="modal-body">
        <div class="progress progress-striped active">
          <div class="bar" style="width: 100%;"></div>
        </div>
        <p>{t}Downloading comments from Disqus, please wait...{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary accept" href="{url name=admin_news_agency_unlock}">{t}Stop sync{/t}</a>
    </div>
</div>
<style>
    #modal-sync .close { display:none; }
</style>
<script>
jQuery("#modal-sync").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: false, //Can close on escape
    show: false,
});
</script>
