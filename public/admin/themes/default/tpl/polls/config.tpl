{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
label {
    display:block;
    color:#666;
    text-transform:uppercase;
}
.utilities-conf label {
    text-transform:none;
}

fieldset {
    border:none;
    border-top:1px solid #ccc;
}
legend {
    color:#666;
    text-transform:uppercase;
    font-size:13px;
    padding:0 10px;
}
</style>
{/block}

{block name="content"}
<form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Poll :: Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{$smarty.server.PHP_SELF}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}


        <div class"panel" style="border:1px solid #ccc; padding:10px;">
            <fieldset>
                <legend>Poll section preferences</legend>
                <label for="poll[typeValue]">{t}Values type{/t}</label>
                <select name="poll_settings[typeValue]" id="poll_settings[typeValue]" class="required">
                    <option value="percent" {if $configs['poll_settings']['typeValue'] eq 'percent'} selected {/if}>{t}Percents{/t}</option>
                    <option value="vote" {if $configs['poll_settings']['typeValue'] eq 'vote'} selected {/if}>{t}Vote count{/t}</option>
                </select>
                <br/><br/>

                <div style="display:inline-block">
                    <label for="poll[heightPoll]">{t}Charts height{/t}</label>
                    <input type="text" class="required" id="name" name="poll_settings[heightPoll]" value="{$configs['poll_settings']['heightPoll']|default:"500"}" />
                </div><!-- / -->
                <div style="display:inline-block">
                    <label for="poll[widthPoll]">{t}Charts width{/t}</label>
                <input type="text" class="required" id="name" name="poll_settings[widthPoll]" value="{$configs['poll_settings']['widthPoll']|default:"600"}" />
                </div><!-- / -->


            </fieldset>

            <fieldset>
                <legend>Poll home widget preferences</legend>
                <label for="poll[total_widget]">{t}Elements in frontpage widget{/t}</label>
                <input type="text" class="required" name="poll_settings[total_widget]" value="{$configs['poll_settings']['total_widget']|default:"1"}" />
                <br/><br/>

                <div style="display:inline-block">
                    <label for="poll[widthWidget]">{t}Chart width{/t}</label>
                    <input type="text" class="required" id="name" name="poll_settings[widthWidget]" value="{$configs['poll_settings']['widthWidget']|default:"240"}" />
                </div><!-- / -->
                <div style="display:inline-block">
                    <label for="poll[heightWidget]">{t}Chart height{/t}</label>
                    <input type="text" class="required" id="name" name="poll_settings[heightWidget]" value="{$configs['poll_settings']['heightWidget']|default:"240"}" />
                </div><!-- / -->


            </fieldset>
        </div><!-- / -->

        <div class="action-bar clearfix">
            <div class="right">
                <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="save_config" />
    </div>
</form>
{/block}
