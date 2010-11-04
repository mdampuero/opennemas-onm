{extends file='base/frontpage_layout.tpl'}

{block name='meta'}
    {if $smarty.request.action eq "read"}
        <title>{$opinion->name|clearslash} - {$opinion->title|clearslash} - Opinión de Galicia {$smarty.const.SITE_TITLE} </title>
        <meta name="keywords" content="{$opinion->metadata|clearslash}" />
        <meta name="description" content="{$opinion->summary|clearslash}" />
    {elseif $smarty.request.action eq 'list_op_author'}
         <title>{$author_name} - Artículos de Opinión  - Opinión de Galicia - {$smarty.const.SITE_TITLE}</title>
        <meta name="keywords" content="opinion, {$smarty.const.SITE_KEYWORDS}" />
        <meta name="description" content="Opinión: {$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />
    {else}
        <title> Articulos Opinion - Opinión de Galicia - {$smarty.const.SITE_TITLE}</title>
        <meta name="keywords" content="opinion, {$smarty.const.SITE_KEYWORDS}" />
        <meta name="description" content="Opinión: {$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />
    {/if}
{/block}

{block name='header-css'}
    {$smarty.block.parent}
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/opinion.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.JS_DIR}/facebox/facebox.css" media="screen" type="text/css"/>
    <style type="text/css">
    div.opinion #submenu { background-color:#e9ddaf !important; }
    div.opinion #submenu > ul, div.opinion div.toolbar-bottom a, div.opinion div.utilities a.share-action, div.opinion .transparent-logo{
            background-color:#d3bc5f !important;
    }
    </style>
{/block}

{block name='header-js'}
    {$smarty.block.parent}
    <script type="text/javascript" src="{$params.JS_DIR}facebox/facebox.js"></script>
{/block}

{block name="footer-js"}
    <script type='text/javascript'>
        jQuery(document).ready(function(){
            $("#tabs").tabs();
            $lock=false;
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

{block name="footer"}
	{$smarty.block.parent}
	<div class="wrapper clearfix">
        <div class="container clearfix span-24 last">
            <div id="footer" class="">
                {include file="ads/widget_ad_bottom.tpl"  type1='109' type2='110'}
                {include file="frontpage/frontpage_footer.tpl"}
            </div><!-- fin .footer -->
        </div><!-- fin .container -->
    </div><!-- .wrapper -->
{/block}


{block name="content"}
    {insert name="intersticial" type="150"}
    {include file="ads/widget_ad_top.tpl" type1='101' type2='102'}
    <div class="wrapper clearfix opinion">
        <div class="container clearfix span-24 last">
            <div id="header" class="">
               {include file="frontpage/frontpage_header.tpl"}
               {include file="frontpage/frontpage_menu.tpl"}
            </div>
            <div id="main_content" class="opinion-index span-24 last">
                <div class="span-16">										
	                {include file="opinion/opinion_inner.tpl"}
                </div>
            </div><!-- fin #main_content -->  
        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->
{/block}