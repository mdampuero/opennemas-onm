{extends file='base/frontpage_layout.tpl'}

{block name="meta" append}

	{if $smarty.request.action eq "read"}
		<title>{$opinion->title|clearslash} - {$opinion->name|clearslash} - {$smarty.const.SITE_TITLE} </title>
		<meta name="keywords" content="{$opinion->metadata|clearslash}" />
		<meta name="description" content="{$opinion->summary|clearslash}" />

		<meta property="og:title" content="{$opinion->title|clearslash}" />
		<meta property="og:description" content="{$opinion->summary|strip_tags|clearslash}" />
	{elseif $smarty.request.action eq 'list_op_author'}
		<title>Artículos de Opinión de {$author_name} - {$smarty.const.SITE_TITLE}</title>
		<meta name="keywords" content="opinion, {$smarty.const.SITE_KEYWORDS}" />
		<meta name="description" content="Opinión: {$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />
	{else}
		<title> Articulos Opinion - {$smarty.const.SITE_TITLE}</title>
		<meta name="keywords" content="opinion, {$smarty.const.SITE_KEYWORDS}" />
		<meta name="description" content="Opinión: {$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />
	{/if}


{/block}


{block name='header-css'}
{$smarty.block.parent}
	<link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="{$params.CSS_DIR}parts/video-js.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="{$params.JS_DIR}/facebox/facebox.css" media="screen" type="text/css"/>
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/opinion.css" type="text/css" media="screen,projection" />
    <style type="text/css">
    div.opinion #submenu { background-color:#e9ddaf !important; }
    div.opinion #submenu > ul, div.opinion div.toolbar-bottom a, div.opinion div.utilities a.share-action, div.opinion .transparent-logo{
            background-color:#d3bc5f !important;
    }
    </style>
{/block}

{block name="footer-js" append}
	<script type="text/javascript" src="{$params.JS_DIR}facebox/facebox.js"></script>
	<script type="text/javascript" src="{$params.JS_DIR}videojs.js"></script>
	<script charset="utf-8" type="text/javascript">
		$(function(){
		  VideoJS.setup();
		})
	</script>

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


{block name="content"}
	<div class="container_ads">
        {include file="ads/ad_in_header.tpl" type1='1' type2='2' nocache}
    </div>
    <div class="wrapper clearfix">

		<div class="container container_with_border">

            <div id="header">
               {include file="base/partials/_frontpage_header.tpl"}
               {include file="base/partials/_frontpage_menu.tpl"}
            </div><!-- #header -->

            <div id="main_content" class="wrapper_content_inside_container opinion-index span-24">

				<div class="span-24">
                    <div class="layout-column span-16 inner-article">

						<div class="span-16 last title-subtitle-legend clearfix">
							<div class="span-3">
								{if $opinion->type_opinion neq 1 and $opinion->path_img}
									<a class="opinion-author" href="{$smarty.const.SITE_URL}{generate_uri 	content_type="opinion_author_frontpage"
																					title=$opinion->author_name_slug
																					id=$opinion->pk_author}">
										<img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$opinion->path_img}" width="110" alt="{$opinion->name}"/>
									</a>
								{/if}
							</div><!--opinion-image-->
							<h1 class="inner-article-title">{$opinion->title|clearslash}</h1>
							<div class="inner-article-subtitle">
								{* 0 - autor, 1 - editorial, 2 - director *}
								{if $opinion->type_opinion eq 0}
									<a class="opinion-author-name" href="{$smarty.const.SITE_URL}{generate_uri 	content_type="opinion_author_frontpage"
																												title=$opinion->author_name_slug
																												id=$opinion->fk_author}">{$opinion->name} </a>
								  | <span class="opinion-author-condition">{$opinion->condition|clearslash|truncate:100:"...":"true"}</span>
								{elseif $opinion->type_opinion eq 2}
									<a class="opinion-author-name" href="{$smarty.const.SITE_URL}{generate_uri 	content_type="opinion_author_frontpage"
																												title='director'
																												id=2}">el Director</a>
								{else}
									<a class="opinion-author-name" href="{$smarty.const.SITE_URL}{generate_uri 	content_type="opinion_author_frontpage"
																												title='editorial'
																												id=1}">la Editorial</a>
								{/if}
							</div>
							<div class="inner-article-legend">
								<span class="inner-article-place">nuevatribuna.es</span> |
								<span class="inner-article-publish-date">{articledate article=$opinion updated=$opinion->changed}</span>
							</div>
						</div><!--title-subtitle-legend-->

						<div class="span-16 inner-article-content clearfix">
							<div id="inner-article-body">
								<div class="span-12 inner-article-other-contents">
									<div>{$opinion->body|clearslash}</div>
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
						{include file="internal_widgets/block_comments.tpl" contentid=$contentId nocache}



					</div><!--inner-article-->
					{include file="opinion/partials/_opinion_inner_last_column.tpl"}

				</div>

            </div><!-- fin #main_content -->

        </div><!-- fin .container -->

    </div><!-- fin .wrapper -->
{/block}
