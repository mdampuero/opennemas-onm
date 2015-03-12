  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">{t}Add new item{/t}</h4>
  </div>
  <div class="modal-body">
    <div class="form-group">
      <label class="form-label" for="item-type">
        {t}Type{/t}
      </label>
      <select class="form-control" ng-model="type">
        <option value="external-link">{t}External link{/t}</option>
        {if count($categories) > 0}
          <option value="frontpages">{t}Frontpages{/t}</option>
        {/if}
        {is_module_activated name="ALBUM_MANAGER"}
          {if count($albumCategories) > 0}
            <option value="album-categories">{t}Album Categories{/t}</option>
          {/if}
        {/is_module_activated}
        {is_module_activated name="VIDEO_MANAGER"}
          {if count($videoCategories) > 0}
            <option value="video-categories">{t}Video Categories{/t}</option>
          {/if}
        {/is_module_activated}
        {is_module_activated name="POLL_MANAGER"}
          {if count($pollCategories) > 0}
            <option value="poll-categories">{t}Poll Categories{/t}</option>
          {/if}
        {/is_module_activated}
        {if count($pages) > 0}
          <option value="modules">{t}Modules{/t}</option>
        {/if}
        {if count($staticPages) > 0}
          <option value="static-pages">{t}Static Pages{/t}</option>
        {/if}
        {is_module_activated name="SYNC_MANAGER"}
          {if count($elements) > 0}
            <option value="sync-categories">{t}Sync Categories{/t}</option>
          {/if}
        {/is_module_activated}
        {is_module_activated name="FRONTPAGES_LAYOUT"}
          {if count($categories) > 0}
            <option value="automatic-categories">{t}Automatic Categories{/t}</option>
          {/if}
          {is_module_activated name="SYNC_MANAGER"}
            {if count($elements) > 0}
              <option value="sync-automatic-categories">{t}Sync Automatic Categories{/t}</option>
            {/if}
          {/is_module_activated}
        {/is_module_activated}
      </select>
    </div>
    <div ng-if="type == 'external-link'">
      <p>{t}Fill the below form with the title and the external URL you want to add to the menu.{/t}</p>
      <div class="form-group">
        <label class="form-label" for="external-link-title">
          {t}Title{/t}
        </label>
        <div class="controls">
          <input class="form-control" id="external-link-title" name="external-link-title" type="text">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="external-link-url">
          {t}URL{/t}
        </label>
        <div class="controls">
          <input class="form-control" id="external-link-url" name="external-link-url" type="text">
        </div>
      </div>
    </div>
    {if count($categories) > 0}
      <div ng-if="type == 'frontpages'" ng-init="categories = {json_encode($categories)|replace:'"':'\''}">
        <div class="form-group" ng-repeat="category in categories">
          <div class="checkbox">
            <input id="checkbox-frontpage-[% $index %]" checklist-model="selected.contents" checklist-value="category.id" type="checkbox">
            <label for="checkbox-frontpage-[% $index %]">
              [% category.title %]
            </label>
          </div>
        </div>
        <li id="cat_{$category->pk_content_category}"
            data-title="{$category->title}"
            data-type="category"
            data-link="{$category->name}"
            data-item-id="{$category->pk_content_category}"
            class="drag-category"
            pk_menu="">
            <div>
                <span class="type">{t}Frontpage{/t}:</span>
                <span class="menu-title">{$category->title}</span>
                <div class="btn-group actions" style="float:right;">
                    <a href="#" class="add-item"><i class="icon-plus"></i></a>
                    <a href="#" class="edit-menu-item"><i class="fa fa-pencil"></i></a>
                    <a href="#" class="delete-menu-item"><i class="fa fa-trash"></i></a>
                </div>
            </div>
        </li>
      </div>
    {/if}
    {is_module_activated name="ALBUM_MANAGER"}
      {if count($albumCategories) > 0}
        <div ng-if="type == 'album-categories'" ng-init="albumCategories = {json_encode($albumCategories)|replace:'"':'\''}">
          <div class="form-group" ng-repeat="category in albumCategories">
            <div class="checkbox">
              <input id="checkbox-album-[% $index %]" checklist-model="selected.contents" checklist-value="category.id" type="checkbox">
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
        <div ng-if="type == 'video-categories'" ng-init="videoCategories = {json_encode($videoCategories)|replace:'"':'\''}">
          <div class="form-group" ng-repeat="category in videoCategories">
            <div class="checkbox">
              <input id="checkbox-video-[% $index %]" checklist-model="selected.contents" checklist-value="category.id" type="checkbox">
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
        <div ng-if="type == 'poll-categories'" ng-init="pollCategories = {json_encode($pollCategories)|replace:'"':'\''}">
          <div class="form-group" ng-repeat="category in pollCategories">
            <div class="checkbox">
              <input id="checkbox-poll-[% $index %]" checklist-model="selected.contents" checklist-value="category.id" type="checkbox">
              <label for="checkbox-poll-[% $index %]">
                [% category.title %]
              </label>
            </div>
          </div>
        </div>
      {/if}
    {/is_module_activated}
    {if count($pages) > 0}
      <div ng-if="type == 'modules'" ng-init="pages = {json_encode($pages)|replace:'"':'\''}">
        <div class="form-group" ng-repeat="page in pages">
          <div class="checkbox">
            <input id="checkbox-module-[% $index %]" checklist-model="selected.contents" checklist-value="page.link" type="checkbox">
            <label for="checkbox-module-[% $index %]">
              [% page.title %]
            </label>
          </div>
        </div>
      </div>
    {/if}
    {if count($staticPages) > 0}
      <div ng-if="type == 'static-pages'" ng-init="staticPages = {json_encode($staticPages)|replace:'"':'\''}">
        <div class="form-group" ng-repeat="page in staticPages">
          <div class="checkbox">
            <input id="checkbox-static-pages-[% $index %]" checklist-model="selected.contents" checklist-value="page.link" type="checkbox">
            <label for="checkbox-static-pages-[% $index %]">
              [% page.title %]
            </label>
          </div>
        </div>
      </div>
    {/if}
    {is_module_activated name="SYNC_MANAGER"}
      {if count($elements) > 0}
        <div ng-if="type == 'sync-categories'" ng-init="elements = {json_encode($elements)|replace:'"':'\''}">
          <div ng-repeat="(site, syncCategories) in elements">
            <h5>[% site %]</h5>
            <div class="form-group" ng-repeat="category in syncCategories">
              <div class="checkbox">
                <input id="checkbox-poll-[% $index %]" checklist-model="selected.contents" checklist-value="category" type="checkbox">
                <label for="checkbox-poll-[% $index %]">
                  [% category %]
                </label>
              </div>
            </div>
          </div>
        </div>
      {/if}
    {/is_module_activated}
    {is_module_activated name="FRONTPAGES_LAYOUT"}
      {if count($categories) > 0}
        <div ng-if="type == 'automatic-categories'" ng-init="automaticCategories = {json_encode($categories)|replace:'"':'\''}">
          <div class="form-group" ng-repeat="category in automaticCategories">
            <div class="checkbox">
              <input id="checkbox-poll-[% $index %]" checklist-model="selected.contents" checklist-value="category.id" type="checkbox">
              <label for="checkbox-poll-[% $index %]">
                [% category.title %]
              </label>
            </div>
          </div>
        </div>
      {/if}
      {is_module_activated name="SYNC_MANAGER"}
       {if count($elements) > 0}
        <div ng-if="type == 'sync-automatic-categories'" ng-init="elements = {json_encode($elements)|replace:'"':'\''}">
          <div ng-repeat="(site, syncCategories) in elements">
            <h5>[% site %]</h5>
            <div class="form-group" ng-repeat="category in syncCategories">
              <div class="checkbox">
                <input id="checkbox-poll-[% $index %]" checklist-model="selected.contents" checklist-value="category" type="checkbox">
                <label for="checkbox-poll-[% $index %]">
                  [% category %]
                </label>
              </div>
            </div>
          </div>
        </div>
       {/if}
      {/is_module_activated}
    {/is_module_activated}
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary">Save changes</button>
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
