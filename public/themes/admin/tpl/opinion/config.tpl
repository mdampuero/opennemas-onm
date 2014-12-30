{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    label {
        width:150px;
        display:inline-block;
    }
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    td {
        vertical-align:middle;
    }
    tr {
        padding: 10px;
    }
    </style>
{/block}

{block name="content"}
<form action="{url name=admin_opinions_config}" method="POST" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Opinions{/t} :: {t}Settings{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_opinions}" title="{t}Go back to list{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div class="form-horizontal panel">
            <div class="control-group">
                <label for="opinion_settings[total_director]" class="control-label">{t}Director opinions in Opinion frontpage{/t}</label>
                <div class="controls">
                    <input type="number" required="required" name="opinion_settings[total_director]"
                       value="{$configs['opinion_settings']['total_director']|default:"1"}" />
                    <div class="help-block">{t}How many director opinions will be shown in the opinion frontpage.{/t}</div>
                </div>
            </div>
            <div class="control-group">
                <label for="opinion_settings[total_editorial]" class="control-label">{t}Editorial opinions in Opinion frontpage{/t}</label>
                <div class="controls">
                    <input type="number" required="required" name="opinion_settings[total_editorial]" id="opinion_settings[total_editorial]"
                        value="{$configs['opinion_settings']['total_editorial']|default:"2"}" />
                    <div class="help-block">{t}How many editorial opinions will be shown in the opinion frontpage.{/t}</div>
                </div>
            </div>
            <div class="control-group">
                <label for="opinion_settings[total_opinions]" class="control-label">{t}Opinions in Opinion frontpage{/t}</label>
                <div class="controls">
                    <input type="number" required="required" name="opinion_settings[total_opinions]" id="opinion_settings[total_opinions]"
                        value="{$configs['opinion_settings']['total_opinions']|default:"16"}" />
                    <div class="help-block">{t}How many opinions opinions will be shown in the opinion frontpage.{/t}</div>
                </div>
            </div>
            <hr class="divisor">

            <div class="control-group">
                <label for="opinion_settings[total_opinion_authors]" class="control-label">{t}Author opinions in frontpage opinion widget:{/t}</label>
                <div class="controls">
                    <input type="number" required="required" name="opinion_settings[total_opinion_authors]" id="opinion_settings[total_opinion_authors]"
                        value="{$configs['opinion_settings']['total_opinion_authors']|default:"6"}" />
                    <div class="help-block">{t}How many author opinions will be shown in the widget.{/t}</div>
                </div>
            </div>

            {is_module_activated name="BLOG_MANAGER"}
            <hr class="divisor">
            <div class="control-group">
                <label for="blog_orderFrontpage" class="control-label">{t}Order blog's frontpage by{/t}</label>
                <div class="controls">
                    <select name="opinion_settings[blog_orderFrontpage]" id="blog_orderFrontpage" required >
                        <option value="created" {if !isset($configs['opinion_settings']['blog_orderFrontpage']) || $configs['opinion_settings']['blog_orderFrontpage'] eq "created"} selected {/if}>{t}Created Date{/t}</option>
                        <option value="blogger" {if $configs['opinion_settings']['blog_orderFrontpage'] eq "blogger"} selected {/if}>{t}Blogger{/t}</option>
                    </select>
                    <div class="help-block">
                        {t}Select if order blogs's frontpages by created date or bloggers name.{/t}
                    </div>
                </div>
            </div>
            <hr class="divisor">
            <div class="control-group">
                <label for="blog_itemsFrontpage]" class="control-label">{t}Items per blog page{/t}</label>
                <div class="controls">
                    <input type="number" id="blog_itemsFrontpage" name="opinion_settings[blog_itemsFrontpage]" value="{$configs['opinion_settings']['blog_itemsFrontpage']|default:12}">
                </div>
            </div>
            {/is_module_activated}
        </div>
    </div>
</form>

{/block}
