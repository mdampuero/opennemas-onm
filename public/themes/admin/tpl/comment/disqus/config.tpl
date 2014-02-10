{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_comments_disqus_config}" method="POST">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Settings{/t}</h2>
            </div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png"><br />
                        {t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a class="change" data-controls-modal="modal-comment-change" href="#" title="{t}Change comments module{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}/template_manager/refresh48x48.png" alt="{t}Change system{/t}"><br>
                        {t}Change manager{/t}
                    </a>
                </li>
                <li>
                    <a href="{url name=admin_comments_disqus}" title="{t}Go back to list{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
    {render_messages}

    <h4>{t}Set your Disqus configuration:{/t}</h4>

        <div class="form-horizontal panel">

            <fieldset>
                <div class="control-group">
                    <label for="shortname" class="control-label">Disqus Id (shortname):</label>
                    <div class="controls">
                        <input type="text" class="input-xxlarge" name="shortname" id="shortname" value="{$shortname|default:""}" required/>
                        <div class="help-block">
                            {t}A shortname is the unique identifier assigned to a Disqus site. All the comments posted to a site are referenced with the shortname{/t}.
                        </div>
                    </div>
                </div>

                <div class="control-group">
                    <label for="secret_key" class="control-label">Disqus API Secret Key:</label>
                    <div class="controls">
                        <input type="text" class="input-xxlarge" name="secret_key" id="secret_key" value="{$secretKey|default:""}" required/>
                        <div class="help-block">
                            {t escape=off}You can get your Disqus secret key in <a href="http://disqus.com/api/applications/" target="_blank">here</a>{/t}.
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
{include file="comment/modals/_modalChange.tpl"}
{/block}
