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
</script>
{/block}

{block name="content"}
    <div class="container_ads">
        {include file="ads/ad_in_header.tpl" type1='1' type2='2' nocache}
    </div>
    <div class="wrapper clearfix static-page">
        <div class="container container_with_border">
            
            <div id="header">
               {include file="base/partials/_frontpage_header.tpl"}
               {include file="base/partials/_frontpage_menu.tpl"}
            </div><!-- #header -->
            
            <div id="main_content" class="wrapper_content_inside_container span-24">
                
                <div class="span-24">
                    <div class="layout-column span-16 inner-article">
                            <div class="span-16 last title-subtitle-legend">
                                <h1 class="inner-article-title">{$page->title|clearslash}</h1>
                            </div><!--title-subtitle-legend-->
                            <div class="span-16 inner-article-content  clearfix">
                                <div id="inner-article-body">
                                    <div class="span-16 inner-article-other-contents">
                                        <div> {$page->body}</div>
                                    </div>
                                </div>
                            </div>
                            
                            {include file="ads/ad_robapagina.tpl" nocache}
                    </div><!--inner-article-->
                    {include file="article/partials/_last_column.tpl"}
                </div>
            </div><!-- fin #main_content -->

        </div><!-- fin .container -->

    </div><!-- fin .wrapper -->
{/block}


{block name="footer"}
<div id="wrapper-footer" class="wrapper clearfix">
    <div class="container clearfix span-24 last">
        <div id="footer" class="">
             {include file="base/partials/_frontpage_footer.tpl"}
        </div><!-- fin .footer -->
    </div><!-- fin .container -->
</div>
{/block}