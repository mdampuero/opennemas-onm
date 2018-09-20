<div class="modal fade" id="modal-sync">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">{t}Syncing from sources{/t}</h4>
            </div>
            <div class="modal-body">
                <div class="progress">
                  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    <span class="sr-only">{t}Syncing from sources{/t}</span>
                  </div>
                </div>
                <p>{t}Downloading articles from news agencies, please wait...{/t}</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary accept" href="{url name=backend_news_agency_unlock}">{t}Stop sync{/t}</a>
            </div>
        </div>
    </div>
</div>
<style>
    #modal-sync .close { display:none; }
</style>
