{*
    OpenNeMas project

    @theme      Lucidity
*}
{section name=g loop=$titulares_gente}
    {if $smarty.section.g.last}
        <div class="layout-column first-column span-3">
    {else}
        <div class="layout-column first-column span-4">
    {/if}
            <div>
                <div class="nw-big">
                    <img class="nw-image" style="width:152px;height:100px;" src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$titulares_gente[g]->path_img}" alt="{$titulares_gente[g]->title|clearslash}"/>
                    <h4 class="nw-title"><a href="{$titulares_gente[g]->permalink}">{$titulares_gente[g]->title|clearslash}</a></h4>
                </div>
            </div>
        </div>
 {/section}