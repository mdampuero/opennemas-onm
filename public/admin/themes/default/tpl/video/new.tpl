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
            <div class="title"><h2>{t}Video manager{/t} :: {if !isset($video)}{t}Creating video{/t}{else}{t}Editing video{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                {if isset($video->id)}
                    {acl isAllowed="VIDEO_UPDATE"}
                        <button href="{url name=admin_videos_update id=$video->id}">
                    {/acl}
                {else}
                    {acl isAllowed="VIDEO_CREATE"}
                        <button href="{url name=admin_videos_create}">
                    {/acl}
                {/if}
                        <img src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                {if isset($video->id)}
                {acl isAllowed="VIDEO_CREATE"}
                <li>
                    <button name="continue" value="1">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />
                        {t}Save and continue{/t}
                    </button>
                </li>
                {/acl}
                {/if}
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
