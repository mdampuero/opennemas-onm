{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .author-info-title {
        min-width:150px;
        font-weight: bold;
        display:inline-block;
        margin-bottom:4px;
    }
</style>
{/block}

{block name="footer-js" append}
{/block}

{block name="content"}
<form action="{url name=admin_comments_update id=$comment->id}" method="POST" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Editing comment{/t}</h2></div>
            <ul class="old-button">

                <li>
                    <button type="submit" id="save-exit" title="{t}Update{/t}">
                        <img src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Update{/t}" /><br />{t}Update{/t}
                    </button>
                </li>

                <li>
                    <a href="{url name=admin_comments_delete id=$comment->id}" class="admin_add">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />{t}Delete{/t}
                    </a>
                </li>

                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_comments}" value="{t}Go back{/t}" title="{t}Go back{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" >
                        <br />
                        {t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        {render_messages}
        <div id="edicion-contenido" class="form-horizontal panel">

            <div class="control-group">
                <label class="control-label" for="title">{t}Author{/t}</label>
                <div class="controls">
                    <div class="author-info-title">{t}Nickname{/t}</div> {$comment->author|clearslash}
                    <br>

                    <div class="author-info-title">{t}Email{/t}</div> {$comment->author_email|clearslash}
                    <br>

                    <div class="author-info-title">{t}Submitted on{/t}</div>  {date_format date=$comment->date}
                    <br>

                    <div class="author-info-title">{t}Sent from IP address{/t}</div> {$comment->author_ip}
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="title">{t}Commented on{/t}</label>
                <div class="controls">
                    {$comment->content->title|clearslash}
                </div>
            </div>

            {acl isAllowed="COMMENT_AVAILABLE"}
            <div class="control-group">
                <label class="control-label" for="content_status">{t}Status{/t}</label>
                <div class="controls">
                    {html_radios name=status options=$statuses selected=$comment->status}
                </div>
            </div>
            {/acl}

            <div class="control-group">
                <label class="control-label" for="body">{t}Body{/t}</label>
                <div class="controls">
                    <textarea name="body" id="body" class="onm-editor" data-preset="simple">{$comment->body|clearslash}</textarea>
                </div>
            </div>
        </div>
    </div>
</form>
{/block}
