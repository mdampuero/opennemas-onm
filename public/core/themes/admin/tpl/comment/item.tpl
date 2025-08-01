{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Comments{/t} >
    {t}Edit{/t} ({$id})
{/block}

{block name="ngInit"}
  ng-controller="CommentCtrl" ng-init="getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-comment m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_comments_list}">
    {t}Comments{/t}
  </a>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    {acl isAllowed="COMMENT_AVAILABLE"}
      <div class="form-group">
        <div class="grid-collapse-title">
          <i class="fa fa-eye m-r-5"></i>
          {t}Status{/t}
        </div>
        <div class="grid-body">
          <div class="controls" ng-repeat="status in extra.statuses" ng-if="status.value" >
            <input type="radio" name="status" value="[% status.value %]" id="status_[% status.value %]" ng-checked="item.status == status.value" ng-model="item.status">
            <label for="status_[% status.value %]" class="form-label">[% status.title %]</label>
          </div>
        </div>
      </div>
    {/acl}
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
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
  </div>
{/block}
