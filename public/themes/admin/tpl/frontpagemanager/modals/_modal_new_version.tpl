<div class="modal hide fade" id="modal-new-version">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}New version available{/t}</h3>
    </div>
    <div class="modal-body">
        <p>
            {t escape=off}There is a new version for this frontpage, if you try to save the current changes the new version will be overwritten.{/t}<br/>
        </p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="{url name=admin_frontpage_list category=$category}">{t}Reload frontpage{/t}</a>
        <a class="btn no" href="#">{t}Close{/t}</a>
    </div>
</div>