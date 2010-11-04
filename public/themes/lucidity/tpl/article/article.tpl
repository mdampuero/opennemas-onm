{extends file='base/frontpage_layout.tpl'}

{block name='header-css'}
{$smarty.block.parent}
<title>{$article->title|clearslash} - {$category_real_name|clearslash|capitalize} {$subcategory_real_name|clearslash|capitalize} - Noticias de Galicia - {$smarty.const.SITE_TITLE} </title>
<meta name="keywords" content="{$article->metadata|clearslash}" />
<meta name="description" content="{$article->summary|strip_tags|clearslash}" />
<link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.CSS_DIR}video-js.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.JS_DIR}/facebox/facebox.css" media="screen" type="text/css"/>
{/block}

{block name='header-js'}
{$smarty.block.parent}
{/block}

{block name="footer-js"}
{$smarty.block.parent}
<script type="text/javascript" src="{$params.JS_DIR}facebox/facebox.js"></script>
<script type="text/javascript" src="{$params.JS_DIR}videojs.js"></script>
<script charset="utf-8" type="text/javascript">
    $(function(){
      VideoJS.setup();
    })

    jQuery(document).ready(function(){
        $("#tabs").tabs();
        $lock = false;
        jQuery("div.share-actions").hover(
          function () {
            if (!$lock){
              $lock=true;
              jQuery(this).children("ul").fadeIn("fast");
            }
            $lock=false;
          },
          function () {
            if (!$lock){
              $lock=true;
              jQuery(this).children("ul").fadeOut("fast");
            }
            $lock=false;
          }
        );
    });
</script>
{include file="misc_widgets/widget_analytics.tpl"}
{/block}
    
{block name="content"}
    {insert name="intersticial" type="150"}
    {include file="ads/widget_ad_top.tpl" type1='101' type2='102'}
    <div class="wrapper clearfix">
        <div class="container clearfix span-24 last">
            <div id="header" class="">
               {include file="frontpage/frontpage_header.tpl"}
               {include file="frontpage/frontpage_menu.tpl"}
            </div>
            <div id="main_content" class="single-article span-24">
                <div class="in-big-title span-24">
                    {if !empty($article->title_int)}
                        <h1>{$article->title_int|clearslash}</h1>
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
                                {include file="utilities/widget_ratings.tpl"}
                                {include file="utilities/widget_utilities.tpl" long="true"}
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
                                      <div class="related-news-embebed span-5">
                                         <p class="title">Noticias relacionadas:</p>
                                         <ul>
                                            {section name=r loop=$relationed}
                                                {if $relationed[r]->pk_article neq  $article->pk_article}
                                                   <li>{renderTypeRelated content=$relationed[r]}</li>
                                                {/if}
                                            {/section}
                                        </ul>
                                     </div>
                                {/if}
                                <div>{$article->body|clearslash}</div>
                            </div><!-- /content-article -->
                            <div class="span-16 toolbar">
                                {include file="utilities/widget_ratings.tpl"}
                                {include file="utilities/widget_utilities.tpl" long="true"}
                            </div><!--fin toolbar -->
                            <hr class="new-separator"/>
                            {include file="ads/widget_ad_robapagina.tpl"}
                             <hr class="new-separator"/>
                            <div class="more-news-bottom-article">                                                          
                                {if !empty($suggested)}
                                    <p class="title">Si le interesó esta noticia, eche un vistazo a estas:</p>
                                     <ul>
                                        {section name=r loop=$suggested}
                                             {if $suggested[r].pk_content neq $article->pk_article}
                                               <li><a href="{$suggested[r].permalink}">{$suggested[r].title|clearslash}</a></li>
                                            {/if}
                                        {/section}
                                    </ul>
                                {/if}
                            </div><!--fin more-news-bottom-article -->
                           {include file="module_comments.tpl" content=$article}
                        </div>
                    </div>
                    {include file="article/article_last_column.tpl"}
                </div>
            </div><!-- fin #main_content -->  
        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->
{/block}
    
{block name="footer"}
<div class="wrapper clearfix">
    <div class="container clearfix span-24 last">
        <div id="footer" class="">
             {include file="ads/widget_ad_bottom.tpl" type1='9' type2='10'}
             {include file="frontpage/frontpage_footer.tpl"}
        </div><!-- fin .footer -->
    </div><!-- fin .container -->
</div><!-- .wrapper -->
{/block}