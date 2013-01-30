{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_polls_config}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Polls{/t} :: {t}Settings{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />
                        {t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_polls}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
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
                <label for="poll[typeValue]" class="control-label">{t}Poll section preferences{/t}</label>
                <div class="controls">
                    <div class="form-inline-block">
                        <div class="control-group">
                            <label for="" class="control-label">{t}Values type{/t}</label>
                            <div class="controls">
                                <select name="poll_settings[typeValue]" id="poll_settings[typeValue]" class="required">
                                    <option value="percent" {if $configs['poll_settings']['typeValue'] eq 'percent'} selected {/if}>{t}Percents{/t}</option>
                                    <option value="vote" {if $configs['poll_settings']['typeValue'] eq 'vote'} selected {/if}>{t}Vote count{/t}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-inline-block">
                        <div class="control-group">
                            <label for="poll[heightPoll]" class="control-label">{t}Charts height{/t}</label>
                            <div class="controls">
                                <input type="number" name="poll_settings[heightPoll]" value="{$configs['poll_settings']['heightPoll']|default:"500"}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="poll[widthPoll]" class="control-label">{t}Charts width{/t}</label>
                            <div class="controls">
                                <input type="number" name="poll_settings[widthPoll]" value="{$configs['poll_settings']['widthPoll']|default:"600"}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label for="" class="control-label">{t}Poll home widget preferences{/t}</label>
                <div class="controls">
                    <div class="form-inline-block">
                        <div class="control-group">
                            <label for="poll[total_widget]" class="control-label">{t}Elements in frontpage widget{/t}</label>
                            <div class="controls">
                                <input type="number" name="poll_settings[total_widget]" value="{$configs['poll_settings']['total_widget']|default:"1"}" required/>
                            </div>
                        </div>
                    </div>
                    <div class="form-inline-block">
                        <div class="control-group">
                            <label for="poll[widthWidget]" class="control-label">{t}Chart width{/t}</label>
                            <div class="controls">
                                <input type="number" name="poll_settings[widthWidget]" value="{$configs['poll_settings']['widthWidget']|default:"240"}" required/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="poll[heightWidget]" class="control-label">{t}Chart height{/t}</label>
                            <div class="controls">
                                <input type="number" class="required" id="name" name="poll_settings[heightWidget]" value="{$configs['poll_settings']['heightWidget']|default:"240"}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- / -->

        <input type="hidden" id="action" name="action" value="save_config" />
    </div>
</form>
{/block}
