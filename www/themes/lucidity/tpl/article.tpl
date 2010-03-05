{*
    OpenNeMas project

    @category   OpenNeMas
    @package    OpenNeMas
    @theme      Lucidity

    @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)

    Smarty template: frontpage.tpl
*}

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
    {include file="module_head.tpl"}
    <body>
        {* publicidad insert name="intersticial" type="50" *}

        {include file="widget_ad_top.tpl"}

        <div class="wrapper clearfix">
            <div class="container clearfix span-24">

                <div id="header" class="">

                   {include file="frontend_header.tpl"}

                   {include file="frontend_menu.tpl"}

                </div>

                <div id="main_content" class="span-24">

                    {include file="widget_headlines.tpl"}

                    <div class="span-24">
                        <div class="layout-column first-column span-16">
                            <div>
                            <div class="nw-big">
                                
                                <div class="nw-category-name sports">{$article->subtitle|upper|clearslash}</div>
                                <h3 class="nw-title"><a href="{$article->permalink|clearslash}" title="{$article->title|clearslash}">{$article->title|clearslash}</a></h3>
                                <p class="nw-subtitle">{$article->summary|clearslash}</p>

                                 {if $photoInt->name}
                                    <img style="float:right;padding:4px;width:300px;" class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" title="{$article->title|clearslash|escape:"html"}" alt="{$article->img2_footer|clearslash|escape:"html"}" />
                                {elseif $photoExt->name}
                                    <img style="float:right;padding:4px;width:300px;" class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoExt->path_file}{$photoExt->name}" title="{$article->title|clearslash|escape:"html"}" alt="{$article->img1_footer|clearslash|escape:"html"}" />
                                {/if}
                                
                                <p class="nw-subtitle">{$article->body|clearslash}</p>
                                {if !empty($item->related_contents)}
                                    {assign var='relacionadas' value=$item->related_contents}
                                    <div class="more-resources">
                                        <ul>
                                            {section name=r loop=$relacionadas}
                                                {if $relacionadas[r]->pk_article neq  $item->pk_article}
                                                   {renderTypeRelated content=$relacionadas[r]}
                                                {/if}
                                            {/section}
                                        </ul>
                                    </div>
                                {/if}
                            </div>

                            </div>
                        </div>

                        <div class="layout-column last-column last span-8">
                             {include file="widget_express.tpl"}
                             <hr class="new-separator"/>
                            {include file="widget_ad_lateral.tpl"}

                            
                        </div>

                    </div>


                    <hr class="news-separator">

                    
                    <hr class="news-separator" />

                    <div class="span-24">
                        <div class="layout-column first-column span-8">
                            {include file="widget_video.tpl" type_video="youtube"}
                        </div>
                        <div class="layout-column middle-column span-8">
                            {include file="widget_galery.tpl"}

                            {include file="widget_ad_button.tpl"}
                        </div>
                        <div class="layout-column last-column last span-8">
                            {include file="widget_video.tpl"}
                        </div>
                    </div>

                    {include file="module_other_headlines.tpl"}
                </div><!-- fin #main_content -->

            </div><!-- fin .container -->
        </div><!-- fin .wrapper -->

        <div class="wrapper clearfix">

            <div class="container clearfix span-24">
                <div id="footer" class="">

                </div><!-- fin .footer -->

            </div><!-- fin .container -->

        </div>
    </body>
</html>
