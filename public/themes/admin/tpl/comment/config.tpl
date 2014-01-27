{extends file="base/admin.tpl"}


{block name="content"}
<form action="{url name=admin_comments_config}" method="POST">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Comments{/t} :: {t}Settings{/t}</h2>
            </div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="Save"><br>{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_comments}" title="{t}Go back to list{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" >
                        <br />
                        {t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        {render_messages}
        <div class="form-horizontal panel">
            <fieldset>
                <div class="control-group">
                    <label for="config[number_elements]" class="control-label">{t}Display{/t}</label>
                    <div class="controls">
                        <input type="number" id="name" name="configs[number_elements]" value="{$configs['number_elements']|default:10}" class="input-small">
                        <div class="help-block">{t}Number of comments to show by page{/t}</div>
                    </div>
                </div>
                <div class="control-group">
                    <label for="config[autoaccept]" class="control-label">{t}Before a comment appears{/t}</label>
                    <div class="controls">
                        <input type="checkbox" id="name" name="configs[moderation]" value="1" {if $configs['moderation'] == true}checked="checked"{/if} >
                        <div class="help-block help-block-inline">{t}An administrator must always approve the comment {/t}</div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
{/block}
