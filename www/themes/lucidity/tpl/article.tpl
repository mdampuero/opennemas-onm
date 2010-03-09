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
        {* Cambiar color del menú segun la section *}
        {literal}
        <style type="text/css">
                #main_menu{
                        background-color:#009677;
                }
        </style>
        {/literal}
       
        {include file="widget_ad_top.tpl"}

        <div class="wrapper clearfix">
            <div class="container clearfix span-24">

                <div id="header" class="">

                   {include file="frontend_header.tpl"}

                   {include file="frontend_menu.tpl"}

                </div>

                <div id="main_content" class="single-article span-24">
                    <div class="in-big-title span-24">
                        <h1>{$article->title|clearslash}</h1>
                        <p class="in-subtitle">{$article->subtitle|clearslash}</p>
                        <div class="info-new">
                            <span class="author">{$article->agency|clearslash}</span> - <span class="place">Santiago de Compostela</span> - <span class="publish-date">{articledate article=$article updated=$article->changed}</span>
                        </div>
                    </div><!-- fin lastest-news -->

                    <div class="span-24">
                        <div class="layout-column first-column span-16">
                            <div class="border-dotted">
		                <div class="span-16 toolbar">
		                    {include file="widget_votes.tpl"}

		                    {include file="widget_utilities.tpl"}

                                </div><!--fin toolbar -->
                                <div class="content-article">
                                    <div class="main-photo">
                                         {if $photoInt->name}
                                             <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" title="{$article->img2_footer|clearslash|escape:"html"}" alt="{$article->img2_footer|clearslash|escape:"html"}" />
                                         {/if}
                                        <div class="photo-subtitle">
                                               {$article->img2_footer|clearslash|escape:"html"}
                                                <span class="photo-autor">FOTO: CHEMA REY | MARCA</span>
                                        </div>
                                    </div>
                                    <p>{$article->body|clearslash}</p>
                                </div><!-- /content-article -->

                                {include file="widget_votes.tpl"}
                                <hr class="new-separator"/>

                                {include file="widget_utilities_bottom.tpl"}

                                <hr class="new-separator"/>

                                <div class="more-news-bottom-article">
                                    {if !empty($relationed)}
                                        <p class="title">Si le interesó esta noticia, eche un vistazo a estas:</p>
                                         <ul>
                                            {section name=r loop=$relationed}
                                                {if $relationed[r]->pk_article neq  $article->pk_article}
                                                   {renderTypeRelated content=$relationed[r]}
                                                {/if}
                                            {/section}
                                        </ul>
                                    {/if}
                                </div><!--fin more-news-bottom-article -->

                               {include file="module_comments.tpl"}
                            </div>
                        </div>

                        {include file="article_last_column.tpl"}
                    </div>
                </div><!-- fin #main_content -->

            </div><!-- fin .container -->
        </div><!-- fin .wrapper -->

        <div class="wrapper clearfix">

            <div class="container clearfix span-24">
              {include file="frontend_footer.tpl"}

            </div><!-- fin .container -->

        </div>
    </body>
</html>
