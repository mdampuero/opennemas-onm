  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
    <h4 class="modal-title">{t}Add new item{/t}</h4>
  </div>
  <div class="modal-body">
    <div class="form-group">
      <label class="form-label" for="item-type">
        {t}Type{/t}
      </label>
      <select class="form-control" id="item-type" ng-model="type">
        <option value="external">{t}External link{/t}</option>
        {if count($categories) > 0}
          <option value="category">{t}Frontpages{/t}</option>
        {/if}
        {is_module_activated name="ALBUM_MANAGER"}
          {if count($albumCategories) > 0}
            <option value="albumCategory">{t}Album Categories{/t}</option>
          {/if}
        {/is_module_activated}
        {is_module_activated name="VIDEO_MANAGER"}
          {if count($videoCategories) > 0}
            <option value="videoCategory">{t}Video Categories{/t}</option>
          {/if}
        {/is_module_activated}
        {is_module_activated name="POLL_MANAGER"}
          {if count($pollCategories) > 0}
            <option value="pollCategory">{t}Poll Categories{/t}</option>
          {/if}
        {/is_module_activated}
        {if count($pages) > 0}
          <option value="internal">{t}Modules{/t}</option>
        {/if}
        {if count($static_pages) > 0}
          <option value="static">{t}Static Pages{/t}</option>
        {/if}
        {is_module_activated name="SYNC_MANAGER"}
          {if count($sync_sites) > 0}
            <option value="syncCategory">{t}Sync Categories{/t}</option>
          {/if}
        {/is_module_activated}
        {if count($categories) > 0}
          <option value="blog-category">{t}Automatic Categories{/t}</option>
        {/if}
        {is_module_activated name="SYNC_MANAGER"}
          {if count($sync_sites) > 0}
            <option value="syncBlogCategory">{t}Sync Automatic Categories{/t}</option>
          {/if}
        {/is_module_activated}
      </select>
    </div>
    <div ng-if="type == 'external'">
      <p>{t}Fill the below form with the title and the external URL you want to add to the menu.{/t}</p>
      <div class="form-group">
        <label class="form-label" for="external-link-title">
          {t}Title{/t}
        </label>
        <div class="controls">
          <input class="form-control" id="external-link-title" name="external-link-title" ng-model="$parent.externalLinkTitle" type="text">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="external-link-url">
          {t}URL{/t}
        </label>
        <div class="controls">
          <input class="form-control" id="external-link-url" name="external-link-url" ng-model="$parent.externalLinkUrl" type="text">
        </div>
      </div>
    </div>
    {if count($categories) > 0}
      <div ng-if="type == 'category'" ng-init="categories = {json_encode($categories)|clear_json}">
        <div class="form-group" ng-repeat="category in categories">
          <div class="checkbox col-md-6">
            <input id="checkbox-frontpage-[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
            <label for="checkbox-frontpage-[% $index %]">
              [% category.title %]
            </label>
          </div>
        </div>
      </div>
    {/if}
    {is_module_activated name="ALBUM_MANAGER"}
      {if count($albumCategories) > 0}
        <div ng-if="type == 'albumCategory'" ng-init="albumCategories = {json_encode($albumCategories)|clear_json}">
          <div class="form-group" ng-repeat="category in albumCategories">
            <div class="checkbox col-md-6">
              <input id="checkbox-album-[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
              <label for="checkbox-album-[% $index %]">
                [% category.title %]
              </label>
            </div>
          </div>
        </div>
      {/if}
    {/is_module_activated}
    {is_module_activated name="VIDEO_MANAGER"}
      {if count($videoCategories) > 0}
        <div ng-if="type == 'videoCategory'" ng-init="videoCategories = {json_encode($videoCategories)|clear_json}">
          <div class="form-group" ng-repeat="category in videoCategories">
            <div class="checkbox col-md-6">
              <input id="checkbox-video-[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
              <label for="checkbox-video-[% $index %]">
                [% category.title %]
              </label>
            </div>
          </div>
        </div>
      {/if}
    {/is_module_activated}
    {is_module_activated name="POLL_MANAGER"}
      {if count($pollCategories) > 0}
        <div ng-if="type == 'pollCategory'" ng-init="pollCategories = {json_encode($pollCategories)|clear_json}">
          <div class="form-group" ng-repeat="category in pollCategories">
            <div class="checkbox col-md-6">
              <input id="checkbox-poll-[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
              <label for="checkbox-poll-[% $index %]">
                [% category.title %]
              </label>
            </div>
          </div>
        </div>
      {/if}
    {/is_module_activated}
    {if count($pages) > 0}
      <div ng-if="type == 'internal'" ng-init="pages = {json_encode($pages)|clear_json}">
        <div class="form-group" ng-repeat="page in pages">
          <div class="checkbox col-md-6">
            <input id="checkbox-module-[% $index %]" checklist-model="selected" checklist-value="page" type="checkbox">
            <label for="checkbox-module-[% $index %]">
              [% page.title %]
            </label>
          </div>
        </div>
      </div>
    {/if}
    {if count($static_pages) > 0}
      <div ng-if="type == 'static'" ng-init="staticPages = {json_encode($static_pages)|clear_json}">
        <div class="form-group" ng-repeat="page in staticPages">
          <div class="checkbox col-md-6">
            <input id="checkbox-static-pages-[% $index %]" checklist-model="selected" checklist-value="page" type="checkbox">
            <label for="checkbox-static-pages-[% $index %]">
              [% page.title %]
            </label>
          </div>
        </div>
      </div>
    {/if}
    {is_module_activated name="SYNC_MANAGER"}
      {if count($sync_sites) > 0}
        <div ng-if="type == 'syncCategory'" ng-init="elements = {json_encode($sync_sites)|clear_json}">
          <div ng-repeat="(site, params) in elements" ng-init="siteIndex=$index">
            <h5>[% site %]</h5>
            <div class="form-group" ng-repeat="category in params.categories" >
              <div class="checkbox col-md-6">
                <input id="checkbox-poll-[% siteIndex %]_[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
                <label for="checkbox-poll-[% siteIndex %]_[% $index %]">
                  [% category %]
                </label>
              </div>
            </div>
          </div>
        </div>
      {/if}
    {/is_module_activated}
    {if count($categories) > 0}
      <div ng-if="type == 'blog-category'" ng-init="automaticCategories = {json_encode($categories)|clear_json}">
        <div class="form-group" ng-repeat="category in automaticCategories">
          <div class="checkbox col-md-6">
            <input id="checkbox-poll-[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
            <label for="checkbox-poll-[% $index %]">
              [% category.title %]
            </label>
          </div>
        </div>
      </div>
    {/if}
    {is_module_activated name="SYNC_MANAGER"}
     {if count($sync_sites) > 0}
      <div ng-if="type == 'syncBlogCategory'" ng-init="elements = {json_encode($sync_sites)|clear_json}">
        <div ng-repeat="(site, params) in elements" ng-init="siteIndex=$index">
          <h5>[% site %]</h5>
          <div class="form-group" ng-repeat="category in params.categories">
            <div class="checkbox col-md-6">
              <input id="checkbox-poll-[% siteIndex %]_[% $index %]" checklist-model="selected" checklist-value="category" type="checkbox">
              <label for="checkbox-poll-[% siteIndex %]_[% $index %]">
                [% category %]
              </label>
            </div>
          </div>
        </div>
      </div>
     {/if}
    {/is_module_activated}
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="close()">{t}Close{/t}</button>
    <button type="button" class="btn btn-primary" ng-click="addItem()">{t}Add item{/t}</button>
  </div>
<script>
  jQuery("#modal-add-item").modal({
      backdrop: 'static', //Show a grey back drop
      keyboard: true, //Can close on escape
      show: false
  });

  jQuery('#modal-add-item a.btn.yes').on('click', function(e, ui){
      e.preventDefault();
      var name = jQuery('#itemTitle').val();
      var link = jQuery('#link').val();

      if (name && link) {
          ul = jQuery('#menuelements');

          var li = document.createElement('li');

          ul.append( '<li data-title="'+ name +'" data-link="'+ link +
                      '" class="menuItem" data-name="'+ name +'" data-id ="'+ name +
                      '" data-item-id="" data-type="external"><div>'+name+
                      '<div class="btn-group actions" style="float:right;">'+
                          '<a href="#" class="edit-menu-item"><i class="fa fa-pencil"></i></a> '+
                          '<a href="#" class="delete-menu-item"><i class="fa fa-trash"></i></a>'+
                      '</div></div></li>' );

          jQuery('#itemTitle').attr('value','');
          jQuery('#link').attr('value','');
          jQuery('#linkInsertions').hide();
      }
      jQuery('#modal-add-item').modal('hide');
  });
</script>
