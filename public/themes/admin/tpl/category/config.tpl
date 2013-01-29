{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_categories_config}" method="POST" name="formulario" id="formulario">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Categories{/t} :: {t}Settings{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png"><br />
                        {t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_categories}" class="admin_add" title="{t}Go back to list{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        {render_messages}
    </div>

    <div class="wrapper-content panel">

        <div class="form-horizontal">
            <label for="section_settings[allowLogo]" class="control-label">{t}Show change headers and color in frontpages:{/t}</label>
            <div class="controls">
                 <select name="section_settings[allowLogo]" id="section_settings[allowLogo]" class="required">
                    <option value="0">{t}No{/t}</option>
                    <option value="1" {if $configs['section_settings']['allowLogo'] eq "1"} selected {/if}>{t}Yes{/t}</option>
                </select>
            </div>

        </div>
        <br>
        <br>
        <input type="hidden" id="action" name="action" value="save_config" />
        </div>
</form>
{/block}
