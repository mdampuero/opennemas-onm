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
    {javascripts src="@AdminTheme/js/onm/video.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
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
                        <button href="{url name=admin_videos_update id=$video->id}" id="continue" name="continue" value="1">
                    {/acl}
                {else}
                    {acl isAllowed="VIDEO_CREATE"}
                        <button href="{url name=admin_videos_create}" id="continue" name="continue" value="1">
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

    <div class="wrapper-content">

        {render_messages}
        <div class="form-vertical video-edit-form">

            <div class="contentform-inner clearfix">
                <div class="contentbox-container">
                    <div class="contentbox">
                        <h3 class="title">{t}Attributes{/t}</h3>
                        <div class="content">
                            <input type="checkbox" value="1" id="content_status" name="content_status" {if $video->content_status eq 1}checked="checked"{/if}>
                            <label for="content_status" >{t}Available{/t}</label>
                            {is_module_activated name="COMMENT_MANAGER"}
                            <br/>
                            <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($album) && ($commentsConfig['with_comments'])) || (isset($album) && $album->with_comment eq 1)}checked{/if} value="1" />
                            <label for="with_comment">{t}Allow comments{/t}</label>
                            <hr class="divisor">
                            {/is_module_activated}

                            <h4>{t}Category{/t}</h4>
                            {include file="common/selector_categories.tpl" name="category" item=$video}
                            <br/>
                            <hr class="divisor" style="margin-top:8px;">
                            <h4>{t}Author{/t}</h4>
                            {acl isAllowed="CONTENT_OTHER_UPDATE"}
                                <select name="fk_author" id="fk_author">
                                    {html_options options=$authors selected=$video->fk_author}
                                </select>
                            {aclelse}
                                {if !isset($video->author->name)}{t}No author assigned{/t}{else}{$video->author->name}{/if}
                                <input type="hidden" name="fk_author" value="{$album->fk_author}">
                            {/acl}
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
	</div>
</form>
{/block}
