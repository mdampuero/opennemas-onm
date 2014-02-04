{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
.panel {
    min-height: 0px;
}
</style>
{/block}

{block name="content"}
<form action="{url name=admin_comments_facebook_config}" method="POST">
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
                    <a href="{url name=admin_comments_facebook}" title="{t}Go back to list{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        {render_messages}

        <div class="form-horizontal panel">

            <p>{t escape=off}To be able to moderate comments of your site in Facebook you must create and set here your <strong>Facebook App Id</strong>.{/t}</p>

            <fieldset>
                <div class="control-group">
                    <label for="facebook_api_key" class="control-label">Facebook App Id</label>
                    <div class="controls">
                        <input type="text" id="facebook_api_key" class="input-xxlarge" name="facebook[api_key]" id="fb_app_id" value="{$fb_app_id|default:""}"/>
                        <div class="help-block">
                            {t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers website</a>.{/t}
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
{include file="comment/modals/_modalChange.tpl"}
{/block}
