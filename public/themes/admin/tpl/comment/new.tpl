{extends file="base/admin.tpl"}
{block name="content"}
<form action="{url name=admin_comments_update id=$comment->id}" method="POST" name="formulario" id="formulario">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-comment"></i>
                            {t}Comments{/t}
                        </h4>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                      <h5>{t}Editing comment{/t}</h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                      <li class="quicklinks">
                        <a class="btn btn-link" href="{url name=admin_comments}" value="{t}Go back{/t}" title="{t}Go back{/t}">
                          <span class="fa fa-reply"></span>
                        </a>
                      </li>
                      <li class="quicklinks"><span class="h-seperate"></span></li>
                        <li class="quicklinks">
                            <button type="submit" id="save-exit" title="{t}Update{/t}" class="btn btn-primary">
                              <span class="fa fa-save"></span>
                              {t}Update{/t}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

  <div class="content">
    {render_messages}
    <div class="grid simple">
      <div class="grid-body">
        <div class="form-group">
          <label class="form-label" for="title">{t}Author{/t}</label>
          <div class="controls">
            <table>
              <tr>
                <th>{t}Nickname{/t}</th>
                <td>{$comment->author|clearslash}</td>
              </tr>
              <tr>
                <th>{t}Email{/t}</th>
                <td>{$comment->author_email|clearslash}</td>
              </tr>
              <tr>
                <th>{t}Submitted on{/t}</th>
                <td>{date_format date=$comment->date}</td>
              </tr>
              <tr>
                <th>{t}Sent from IP address{/t}</th>
                <td>{$comment->author_ip}</td>
              </tr>
            </table>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="title">{t}Commented on{/t}</label>
          <div class="controls">
            <strong>{$comment->content->content_type_l10n_name}</strong> - {$comment->content->title|clearslash}
          </div>
        </div>
        {acl isAllowed="COMMENT_AVAILABLE"}
        <div class="form-group">
          <label class="form-label" for="content_status">{t}Status{/t}</label>
          <div class="controls">
            {html_radios name=status options=$statuses selected=$comment->status id="conten_status"}
          </div>
        </div>
        {/acl}
        <div class="form-group">
          <label class="form-label" for="body">{t}Body{/t}</label>
          <div class="controls">
            <textarea ckeditor ckeditor-preset="simple" name="body" id="body" class="form-control">{$comment->body|clearslash}</textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
