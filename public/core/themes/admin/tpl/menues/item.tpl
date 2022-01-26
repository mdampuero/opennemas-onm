{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Menus{/t} >
    {t}Edit{/t} ({$id})
{/block}

{block name="ngInit"}
  ng-controller="MenuCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-list-alt m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_menus_list}">
    {t}Menus{/t}
  </a>
{/block}

{block name="rightColumn"}

{/block}

{block name="leftColumn"}

          <div class="grid simple">
            <div class="grid-body">
              {include file="ui/component/input/text.tpl" iField="name" iTitle="{t}Name{/t}"}
              {if !empty($menu_positions) && count($menu_positions) > 1}
                <div class="form-group no-margin">
                  <label for="name" class="form-label">{t}Position{/t}</label>
                  <div class="controls">
                    {html_options options=$menu_positions selected=$menu->position name=position}
                    <br>
                    <span class="help"><span class="fa fa-info-circle text-info"></span> {t}If your theme has defined positions for menus you can assign one menu to each of them{/t}</span>
                  </div>
                </div>
              {/if}
            </div>
          </div>
          <div class="grid simple">
            <div class="grid-title">
              <h4>
                {t}Menu structure{/t}
              </h4>
              <button class="btn btn-white pull-right btn-small" type="button" ng-click="open('modal-add-item')">
                <i class="fa fa-plus"></i>
                {t}Add items{/t}
              </button>
            </div>
            <div class="grid-body">
              <p>
                {t}Use drag and drop to sort and nest elements.{/t}
              </p>
              <div class="menu-items ng-cloak" ui-tree data-max-depth="2">
                <ol ui-tree-nodes="" ng-model="menu.items">
                  <li ng-repeat="item in menu.items" ui-tree-node ng-include="'menu-item'" ng-init="parentIndex = $index"></li>
                </ol>
              </div>
            </div>
          </div>
  {* <div class="grid simple">
    <div class="grid-body">
      <div class="form-group">
        <label class="form-label" for="title"><i class="fa fa-user m-r-5"></i> {t}Author{/t}</label>
        <div class="controls m-l-20">
          <table>
            <tr>
              <th class="p-r-15">{t}Nickname{/t}</th>
              <td>[% item.author %]</td>
            </tr>
            <tr>
              <th class="p-r-15">{t}Email{/t}</th>
              <td>[% item.author_email %]</td>
            </tr>
            <tr>
              <th class="p-r-15">{t}Submitted on{/t}</th>
              <td>[% item.date | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]</td>
            </tr>
            <tr>
              <th class="p-r-15">{t}Sender IP{/t}</th>
              <td>[% item.author_ip %]</td>
            </tr>
          </table>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="title"><i class="fa fa-archive"></i>  {t}Commented on{/t}</label>
        <div class="controls m-l-20">
          <strong>{include file="common/component/icon/content_type_icon.tpl" iField="extra.contents[item.content_id]" iFlagName=true}</strong>:
          <a href="/content/[% item.content_id %]" target="_blank">[% localizeText(extra.contents[item.content_id].title) %]</a>
        </div>
      </div>
      <div class="form-group">
        <div class="controls">
          <label class="form-label" for="body"><i class="fa fa-comment m-r-5"></i> {t}Body{/t}</label>
          {include file="ui/component/content-editor/textarea.tpl" field="body" preset="simple" rows=15}
        </div>
      </div>
    </div>
  </div> *}
{/block}
