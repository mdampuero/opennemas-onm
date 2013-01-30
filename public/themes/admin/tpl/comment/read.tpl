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
    {script_tag src="/tiny_mce/opennemas-config.js"}
    <script type="text/javascript" language="javascript">
        tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
        OpenNeMas.tinyMceConfig.advanced.elements = "body";
        tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
    </script>
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
        <div id="edicion-contenido" class="form-horizontal panel">

            <div class="control-group">
                <label class="control-label" for="title">{t}Author{/t}</label>
                <div class="controls">
                    <div class="author-info-title">{t}Nickname{/t}</div> {$comment->author|clearslash}
                    <input type="hidden" id="author" name="author" title="author" value="{$comment->author|clearslash}" class="required" />
                    <br>

                    <div class="author-info-title">{t}Email{/t}</div> {$comment->email|clearslash}
                    <input type="hidden" id="email" name="email" title="email" value="{$comment->email|clearslash}" class="required" />
                    <br>

                    <div class="author-info-title">{t}Written on{/t}</div> {$comment->created}
                    <input type="hidden" id="date" name="date" title="author" value="{$comment->created}" class="required" size="20" readonly />
                    <br>

                    <div class="author-info-title">{t}Sent from IP address{/t}</div> {$comment->ip}
                    <input type="hidden" id="ip" name="ip" title="author" value="{$comment->ip}" class="required" size="20" readonly />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="title">{t}Commented on{/t}</label>
                <div class="controls">
                    <strong>{$content_types[$content->content_type]}</strong>: {$content->title|clearslash}
                </div>
            </div>

            {acl isAllowed="COMMENT_AVAILABLE"}
            <div class="control-group">
                <label class="control-label" for="content_status">{t}Published{/t}</label>
                <div class="controls">
                    <select name="content_status" id="content_status" class="required">
                        <option value="1" {if $comment->content_status eq 1} selected {/if}>{t}Yes{/t}</option>
                        <option value="0" {if $comment->content_status eq 0} selected {/if}>{t}No{/t}</option>
                        <option value="2" {if $comment->content_status eq 2} selected {/if}>{t}Rejected{/t}</option>
                    </select>
                </div>
            </div>
            {/acl}

            <div class="control-group">
                <label class="control-label" for="title">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="title" name="title" value="{$comment->title|clearslash|escape:"html"}" class="required input-xlarge" placeholder="{t}Comment title{/t}"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="body">{t}Body{/t}</label>
                <div class="controls">
                    <textarea name="body" id="body" style="width:100%; height:20em;">{$comment->body|clearslash}</textarea>
                </div>
            </div>

            <input type="hidden" id="fk_content" name="fk_content"   value="{$comment->fk_content}" />
            <input type="hidden" id="category" name="category" value="{$comment->category}" />
        </div>
    </div>
</form>
{/block}
