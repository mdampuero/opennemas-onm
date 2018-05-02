{extends file="base/admin.tpl"}
{block name="content"}
  <form action="{url name=admin_comments_update id=$comment->id}" method="POST" name="formulario" id="formulario">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=admin_comments}" title="{t}Go back to list{/t}">
                  <i class="fa fa-comment"></i>
                  {t}Comments{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <div class="p-l-10 p-r-10 p-t-10">
                <i class="fa fa-angle-right"></i>
              </div>
            </li>
            <li class="quicklinks hidden-xs">
              <h5><strong>{t}Edit{/t}</strong></h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button type="submit" id="save-exit" title="{t}Update{/t}" data-text="{t}Updating{/t}..." class="btn btn-primary" id="update-button">
                  <span class="fa fa-save"></span>
                  <span class="text">{t}Update{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="row">
        <div class="col-md-4 col-md-push-8">
          <div class="grid simple">
            {acl isAllowed="COMMENT_AVAILABLE"}
            <div class="form-group">
              <div class="grid-collapse-title">
                <i class="fa fa-eye m-r-5"></i>
                {t}Status{/t}
              </div>
              <div class="grid-body">
                <div class="controls">
                  {foreach $statuses as $item}
                  {if $item['value'] neq -1}
                  <div>
                    <input type="radio" name="status" value="{$item['value']}" id="content_status_{$item['value']}" {if $comment->status == {$item['value']}}checked{/if}>
                    <label for="content_status_{$item['value']}" class="form-label">{$item['title']}</label>
                  </div>
                  {/if}
                  {/foreach}
                </div>
              </div>
            </div>
            {/acl}
          </div>
        </div>
        <div class="col-md-8 col-md-pull-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <label class="form-label" for="title"><i class="fa fa-user m-r-5"></i> {t}Author{/t}</label>
                <div class="controls m-l-20">
                  <table>
                    <tr>
                      <th class="p-r-15">{t}Nickname{/t}</th>
                      <td>{$comment->author|clearslash}</td>
                    </tr>
                    <tr>
                      <th class="p-r-15">{t}Email{/t}</th>
                      <td>{$comment->author_email|clearslash}</td>
                    </tr>
                    <tr>
                      <th class="p-r-15">{t}Submitted on{/t}</th>
                      <td>{date_format date=$comment->date}</td>
                    </tr>
                    <tr>
                      <th class="p-r-15">{t}Sender IP{/t}</th>
                      <td>{$comment->author_ip}</td>
                    </tr>
                  </table>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="title"><i class="fa fa-archive"></i>  {t}Commented on{/t}</label>
                <div class="controls m-l-20">
                  <strong>{t}{$comment->content->content_type_l10n_name}{/t}</strong>:
                  <a href="/content/{$comment->content->id}">{localize_filter field=$comment->content->title|clearslash params=$language_data}</a>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="body"><i class="fa fa-comment m-r-5"></i> {t}Body{/t}</label>
                <div class="controls">
                  <textarea onm-editor onm-editor-preset="simple" name="body" id="body" ng-model="body" class="form-control">{$comment->body|clearslash}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
