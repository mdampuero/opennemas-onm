{*
    OpenNeMas project

    @theme      Lucidity
*}
{section name=g loop=$titulares_gente}
{if $smarty.section.g.last}
    <div class="layout-column span-4 last">
{else}
    <div class="layout-column span-4">
{/if}
        <div>
            <div class="onm-new last">
                <img class="onm-image" style="width:152px;height:100px;" src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$titulares_gente[g]->path_img}" alt="{$titulares_gente[g]->title|clearslash}"/>
                <h4 class="onm-title"><a href="{$titulares_gente[g]->permalink}">{$titulares_gente[g]->title|clearslash}</a></h4>
            </div>
        </div>
    </div>
{/section}