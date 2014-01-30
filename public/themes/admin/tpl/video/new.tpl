{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .utilities-conf {
        position:absolute;
        top:0;
        right:0;
    }
</style>
{/block}

{block name="footer-js" append}
    <script>
    var video_manager_url = {
        get_information: '{url name=admin_videos_get_info}',
        fill_tags: '{url name=admin_utils_calculate_tags}'
    }

    jQuery(document).ready(function($){
        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });
    });
    </script>
    {script_tag src="/onm/video.js" language="javascript"}
{/block}

{block name="content"}
<form action="{if isset($video)}{url name=admin_videos_update id=$video->id}{else}{url name=admin_videos_create}{/if}" method="POST" name="formulario" id="formulario" enctype="multipart/form-data">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if !isset($video)}{t}Creating video{/t}{else}{t}Editing video{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                {if isset($video->id)}
                    {acl isAllowed="VIDEO_UPDATE"}
                        <button href="{url name=admin_videos_update id=$video->id}" name="continue" value="1">
                    {/acl}
                {else}
                    {acl isAllowed="VIDEO_CREATE"}
                        <button href="{url name=admin_videos_create}"  name="continue" value="1">
                    {/acl}
                {/if}
                        <img src="{$params.IMAGE_DIR}save.png" title="Guardar" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_videos category=$category|default:""}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go Back{/t}" alt="{t}Go Back{/t}" ><br />{t}Go Back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content ">

        {render_messages}

        <div class="form-horizontal panel clearfix">
            <div class="utilities-conf form-vertical">
                <div class="control-group">
                    <label for="category" class="control-label">{t}Section:{/t}</label>
                    <div class="controls">
                        <select name="category" id="category">
                        {section name=as loop=$allcategorys}
                            <option value="{$allcategorys[as]->pk_content_category}" {if isset($video) && ($video->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category)}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                            {section name=su loop=$subcat[as]}
                            <option value="{$subcat[as][su]->pk_content_category}" {if isset($video) && ($video->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category)}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                            {/section}
                        {/section}
                    </select>
                    </div>
                </div>
                <div class="control-group">
                <label for="author" class="control-label">{t}Author{/t}</label>
                <div class="controls">
                    {acl isAllowed="CONTENT_OTHER_UPDATE"}
                        <select name="fk_author" id="fk_author">
                            {html_options options=$authors selected=$video->fk_author}
                        </select>
                    {aclelse}
                        {if !isset($album->author->name)}{t}No author assigned{/t}{else}{$video->author->name}{/if}
                        <input type="hidden" name="fk_author" value="{$video->fk_author}">
                    {/acl}

                </div>
            </div>
                <div class="control-group">
                    <label for="available" class="control-label">{t}Available{/t}</label>
                    <div class="controls">
                        <select name="available" id="available"
                            {acl isNotAllowed="VIDEO_AVAILABLE"} disabled="disabled" {/acl} class="required">
                             <option value="1" {if isset($video) && $video->available eq '1'} selected {/if}>Si</option>
                             <option value="0" {if isset($video) && $video->available eq '0'} selected {/if}>No</option>
                        </select>
                    </div>
                </div>
            </div>

            {if $type == "file" || (isset($video) && $video->author_name == 'internal')}
                {include file="video/partials/_form_video_internal.tpl"}
            {elseif $type == "external" || (isset($video) && $video->author_name == 'external')}
                {include file="video/partials/_form_video_external.tpl"}
            {elseif $type == "script" || (isset($video) && $video->author_name == 'script')}
                {include file="video/partials/_form_video_script.tpl"}
            {else}
                {include file="video/partials/_form_video_panorama.tpl"}
            {/if}
        </div>

        <input type="hidden" value="1" name="content_status">
        <input type="hidden" name="type" value="{$smarty.get.type}">
		<input type="hidden" name="id" id="id" value="{$video->id|default:""}" />
	</div>
</form>
{/block}
