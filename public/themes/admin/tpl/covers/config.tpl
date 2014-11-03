{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_covers_config}" method="POST">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}ePaper{/t} :: {t}Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png"><br />
                        {t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_covers}" title="{t}Go back to list{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div class="form-horizontal panel">
            <div class="control-group">
                <label for="kiosko_settings[orderFrontpage]" class="control-label">{t}Order newsstand frontpage by{/t}</label>
                <div class="controls">
                    <select name="kiosko_settings[orderFrontpage]" id="kiosko_settings[orderFrontpage]" class="required">
                        <option value="sections" {if $configs['kiosko_settings']['orderFrontpage'] eq "sections"} selected {/if}>{t}Sections{/t}</option>
                        <option value="dates" {if $configs['kiosko_settings']['orderFrontpage'] eq "dates"} selected {/if}>{t}Dates{/t}</option>
                        <option value="grouped" {if $configs['kiosko_settings']['orderFrontpage'] eq "grouped"} selected {/if}>{t}Grouped by date{/t}</option>
                    </select>
                    <div class="help-block">
                        {t}Select if order newsstand's frontpage by dates or by section.{/t} <br>
                        {t}Grouped by date is recommended for weekly newspapers.{/t}
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
{/block}
