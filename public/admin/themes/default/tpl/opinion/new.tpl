{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/tiny_mce/opennemas-config.js"}
    {script_tag src="/jquery-onm/jquery.inputlength.js"}
    <script>
        tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
        OpenNeMas.tinyMceConfig.advanced.elements = "body";
        tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );

        jQuery(document).ready(function ($){
            $('#opinion-form').tabs();
            $('#title').inputLengthControl();
            $('#title input').on('change', function(e, ui) {
                fill_tags($('#title input').val(), '#metadata', '{url name=admin_utils_calculate_tags}');
            });
            $('#formulario').onmValidate({
                'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
            });
            $('#type_opinion').on('change', function() {
                var selected = $(this).find('option:selected').val();
                if (selected != 0) {
                    $('#author').hide();
                } else {
                    $('#author').show();
                }
            });
        });
    </script>
{/block}

{block name="content"}
<form action="{iF $opinion->id}{url name=admin_opinion_update id=$opinion->id}{else}{url name=admin_opinion_create}{/if}" method="POST" id="formulario">
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Opinion Manager{/t} :: {if $opinion->id}{t 1=$opinion->title}Editing opinion "%1"{/t}{else}{t}Creating new opinion{/t}{/if}</h2></div>
        <ul class="old-button">
            <li>
                <button type="submit" name="continue" value="1">
                    <img src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                </a>
            </li>
            <li>
                <button type="submit">
                    <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save and exit{/t}"><br />{t}Save{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_opinions}" title="{t}Go back{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

    <div class="form-horizontal panel clearfix">

        <div class="utilities-conf" class="pull-right" style="width:150px; position:absolute; top:0; right:0;">
            <table>
                <tr>
                    <td>
                        <input type="checkbox" name="available" id="available" {if $opinion->available eq 1}checked="checked"{/if} />
                        <label for="title">{t}Available{/t}</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="in_home" id="in_home" {if $opinion->in_home eq 1}checked="checked"{/if}>
                        <label for="title">{t}In homepage{/t}</label>
                    </td>
                </tr>
                {is_module_activated name="COMMENT_MANAGER"}
                <tr>
                    <td>
                        <input type="checkbox" name="with_comment" id="with_comment" {if $opinion->with_comment eq 1}checked="checked"{/if} />
                        <label for="title">{t}Allow comments{/t}</label>
                    </td>
                </tr>
                {/is_module_activated}
            </table>
        </div>

        <div class="control-group">
            <label for="title" class="control-label">{t}Title{/t}</label>
            <div class="controls">
                <div class="input-append" id="title">
                    <input type="text" name="title" value="{$opinion->title|clearslash|escape:"html"}"
                        required="required" class="input-xlarge" />
                    <span class="add-on"></span>
                </div>
            </div>
        </div>

        <div class="control-group">
            <label for="metadata" class="control-label">{t}Keywords{/t}</label>
            <div class="controls">
                <input type="text" id="metadata" name="metadata" title="{t}Metadata{/t}" value="{$opinion->metadata|clearslash}" class="input-xxlarge" required="required" />
                <div class="help-block">{t}List of words separated with commas.{/t}</div>
            </div>
        </div>

        <div class="control-group">
            <label for="type_opinion" class="control-label">{t}Type{/t}</label>
            <div class="controls">
                <select name="type_opinion" id="type_opinion" required="required">
                    <option value="-1">{t}-- Pick an author --{/t}</option>
                    <option value="0" {if $opinion->type_opinion eq 0} selected {/if}>{t}Opinion from author{/t}</option>
                    <option value="1" {if $opinion->type_opinion eq 1} selected {/if}>{t}Opinion from editorial{/t}</option>
                    <option value="2" {if $opinion->type_opinion eq 2} selected {/if}>{t}Director's letter{/t}</option>
                </select>
            </div>
        </div>

        <div class="control-group" id="author" {if $opinion->type_opinion neq 0}style="display:none;"{/if}>
            <label for="fk_author" class="control-label">{t}Author{/t}</label>
            <div class="controls">
                <select id="fk_author" name="fk_author" required="required">
                    <option value="0" {if isset($author) && $author eq "0"}selected{/if}>{t} - Select one author - {/t}</option>
                    {foreach from=$all_authors item=author}
                    <option value="{$author->pk_author}" {if $opinion->fk_author eq $author->pk_author}selected{/if}>{$author->name}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="body" class="control-label">{t}Body{/t}</label>
            <div class="controls">
                <textarea name="body" id="body" required="required" style="width:100%; min-height:600px;">{$opinion->body|clearslash}</textarea>
            </div>
        </div>

        <input type="hidden" id="fk_user_last_editor" name="fk_user_last_editor" value="{$publisher|default:""}"/>
        <input type="hidden" id="category" name="category" title="opinion" value="opinion" />
    </div>
</div>
</form>
{/block}
