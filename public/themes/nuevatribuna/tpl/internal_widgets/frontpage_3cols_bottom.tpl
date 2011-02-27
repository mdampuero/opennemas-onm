{*
    OpenNeMas project

    @theme      Lucidity
*}
<div style="float:left; display:block; margin-left:-10px; width:200px;height:200px;">
    {insert name="renderbanner" type=16 width="200" height="200" cssclass=""}
</div>
{section name=g loop=$titulares_gente}
{if $smarty.section.g.last}
    <div class="layout-column last" style="float:left; display:block; margin-left:9px; height:100%; width:185px;">
{else}
    <div class="layout-column" style="float:left; display:block; margin-left:9px; height:100%; width:185px;">
{/if}
        <div>
            <div class="onm-new last">
                <a href="{$titulares_gente[g]->uri}">
                    <img class="onm-image" style="width:185px;height:125px;" src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$titulares_gente[g]->path_img}" alt="{$titulares_gente[g]->title|clearslash}"/></a>
                <h4 class="onm-title"><a href="{$titulares_gente[g]->uri}">{$titulares_gente[g]->title|clearslash}</a></h4>
                <p class="subtitle">{$titulares_gente[g]->summary|clearslash}</p>
            </div>
        </div>
    </div>
{/section}
<div style="float:left; display:block; margin-left:10px; margin-right:-10px; width:200px;height:200px;">
    {insert name="renderbanner" type=36 width="200" height="200"  cssclass=""}
</div>
