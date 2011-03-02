{extends file='base/frontpage_layout.tpl'}

{block name="meta" append}
<title>{$article->title|clearslash} - {$category_real_name|clearslash|capitalize} {$subcategory_real_name|clearslash|capitalize} - {$smarty.const.SITE_TITLE} </title>
<meta name="keywords" content="{$article->metadata|clearslash}" />
<meta name="description" content="{$article->summary|strip_tags|clearslash}" />

<meta property="og:title" content="{$article->title|clearslash}" />
<meta property="og:description" content="{$article->summary|strip_tags|clearslash}" />
<meta property="og:image" content="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" />
{/block}

{block name='header-css'}
{$smarty.block.parent}
<link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.CSS_DIR}parts/video-js.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.JS_DIR}/facebox/facebox.css" media="screen" type="text/css"/>
{/block}


{block name="footer-js"}
{$smarty.block.parent}
<script type="text/javascript" src="{$params.JS_DIR}facebox/facebox.js"></script>
<script type="text/javascript" src="{$params.JS_DIR}videojs.js"></script>
<script charset="utf-8" type="text/javascript">
    $(function(){
      VideoJS.setup();
    })
</script>
{/block}

{block name="content"}
    <div class="container_ads">
        {include file="ads/ad_in_header.tpl" type1='101' type2='102' nocache}
    </div>
    <div class="wrapper clearfix">
        <div class="container container_with_border">

            <div id="header">
               {include file="base/partials/_frontpage_header.tpl"}
               {include file="base/partials/_frontpage_menu.tpl"}
            </div><!-- #header -->

            <div id="main_content" class="wrapper_content_inside_container span-24">

                <div class="span-24">
                    <div class="layout-column span-16 inner-article">
                            <div class="span-16 last title-subtitle-legend">
                                {if !empty($article->title_int)}
                                    <h1 class="inner-article-title">{$article->title_int|clearslash}</h1>
                                {else}
                                    <h1 class="inner-article-title">{$article->title|clearslash}</h1>
                                {/if}
                                <div class="inner-article-subtitle">{$article->summary|clearslash}</div>
                                <div class="inner-article-legend">
                                    <span class="inner-article-author">{$article->agency|clearslash}</span> |
                                    <span class="inner-article-publish-date">{articledate article=$article updated=$article->changed}</span>
                                </div>
                            </div><!--title-subtitle-legend-->
                            <div class="span-16 inner-article-content  clearfix">
                                <div id="inner-article-body">
                                    <div class="span-12 inner-article-other-contents">
                                        <div class="inner-article-image">
                                            {if $photoInt->name}
                                               <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" title="{$article->img2_footer|clearslash|escape:"html"}" alt="{$article->img2_footer|clearslash|escape:"html"}" />
                                               <div class="photo-subtitle">
                                                      <span class="photo-autor">{$article->img2_footer|clearslash|escape:"html"}</span>
                                               </div>
                                            {/if}
                                        </div>

                                        <div>{$article->body|clearslash}</div>
                                    </div>
                                    <div class="span-4 inner-article-utilities-box">
                                        <div class="share-buttons clearfix">
                                            <div class="title">Comparte:</div>
                                            {include file="utilities/share_buttons.tpl"}
                                        </div>
                                        <div class="rating clearfix">
                                            <div class="title">Vota esta noticia:</div>
                                            {include file="utilities/widget_ratings.tpl"}
                                        </div>
                                        <div class="utilities clearfix">
                                            <div class="title">Más acciones:</div>
                                            {include file="utilities/widget_utilities.tpl" long="true"}
                                        </div>
                                        {include file="article/partials/_related-contents.tpl"}
                                    </div>
                                </div>
                            </div>

                            {include file="ads/ad_robapagina.tpl" nocache}
                            {include file="article/partials/_other-contents.tpl"}
                            {include file="internal_widgets/block_comments.tpl" contentid=$articleId nocache}

                    </div><!--inner-article-->
                    {include file="article/partials/_last_column.tpl"}
                </div>
            </div><!-- fin #main_content -->

        </div><!-- fin .container -->

    </div><!-- fin .wrapper -->
{/block}


{block name="footer"}
<div class="container_ads">
    {include file="ads/ad_in_footer.tpl" type1='109' type2='110' nocache}
</div>
<div id="wrapper-footer" class="wrapper clearfix">
    <div class="container clearfix span-24 last">
        <div id="footer" class="">
             {include file="base/partials/_frontpage_footer.tpl"}
        </div><!-- fin .footer -->
    </div><!-- fin .container -->
</div>
{/block}
