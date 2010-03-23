{*
OpenNeMas project

@category   OpenNeMas
@package    OpenNeMas
@theme      Lucidity

@copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)

Smarty template: frontpage.tpl
*}



{include file="module_head.tpl"}


    {* Cambiar color del menú segun la section *}


    {include file="widget_ad_top.tpl"}

    <div class="wrapper clearfix">
        <div class="container clearfix span-24">

            <div id="header" class="">

               {include file="frontend_header.tpl"}

               {include file="frontend_menu.tpl"}

            </div>

            <div id="main_content" class="single-article span-24">
                <div class="in-big-title span-24">
                    {if !empty($article->title_int)} <h1>{$article->title_int|clearslash}</h1>
                    {else}
                       <h1>{$article->title|clearslash}</h1>
                    {/if}
                    <p class="in-subtitle">{$article->subtitle|clearslash}</p>
                    <div class="info-new">
                        <span class="author">{$article->agency|clearslash}</span> - <span class="place">Santiago de Compostela</span> - <span class="publish-date">{articledate article=$article updated=$article->changed}</span>
                    </div>
                </div><!-- fin lastest-news -->

                <div class="span-24">
                    <div class="layout-column first-column span-16">
                        <div class="border-dotted">
                            <div class="span-16 toolbar">
                                {include file="widget_ratings.tpl"}

                                {include file="widget_utilities.tpl"}

                            </div><!--fin toolbar -->
                            <div class="content-article">
                                <div class="main-photo">
                                     {if $photoInt->name}
                                         <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" title="{$article->img2_footer|clearslash|escape:"html"}" alt="{$article->img2_footer|clearslash|escape:"html"}" />
                                    
                                        <div class="photo-subtitle">
                                               <span class="photo-autor">{$article->img2_footer|clearslash|escape:"html"}</span>
                                        </div>
                                     {/if}
                                </div>
                                  {if !empty($relationed)}
                                    <div style="width:180px;margin:10px;float:left;border:1px solid #333;">
                                        Relacionadas:
                                         <ul>
                                            {section name=r loop=$relationed}
                                                {if $relationed[r]->pk_article neq  $article->pk_article}
                                                   {renderTypeRelated content=$relationed[r]}
                                                {/if}
                                            {/section}
                                        </ul>
                                     </div>
                                {/if}
                                <p>{$article->body|clearslash}
                                </p>
                              
                               
                            </div><!-- /content-article -->

                            {include file="widget_ratings.tpl"}
                            <hr class="new-separator"/>

                            {include file="widget_utilities_bottom.tpl"}

                            <hr class="new-separator"/>

                            <div class="more-news-bottom-article">                                                          
                                {if !empty($suggested)}
                                    <p class="title">Si le interesó esta noticia, eche un vistazo a estas:</p>
                                     <ul>
                                        {section name=r loop=$suggested}
                                            {if $suggested[r].pk_article neq  $article->pk_article}
                                               <li><a href="{$suggested[r].permalink}">{$suggested[r].title|clearslash}</a></li>
                                            {/if}
                                        {/section}
                                    </ul>
                                {/if}
                            </div><!--fin more-news-bottom-article -->

                           {include file="module_comments.tpl" content=$article}
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
   
    {literal}
        <script type="text/javascript">
            jQuery(document).ready(function(){
                $("#tabs").tabs();
            });
        </script>
    {/literal}
</body>
</html>
