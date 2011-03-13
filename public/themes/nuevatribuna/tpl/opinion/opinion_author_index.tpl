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


{block name="header-css"}
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

{block name="footer-js"}
    {$smarty.block.parent}
	<script type="text/javascript" src="{$params.JS_DIR}facebox/facebox.js"></script>
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


{block name="content" append}
<div class="container_ads">
        {include file="ads/ad_in_header.tpl" type1='1' type2='2' nocache}
    </div>
    <div class="wrapper clearfix">

		<div class="container container_with_border">

            <div id="header">
               {include file="base/partials/_frontpage_header.tpl"}
               {include file="base/partials/_frontpage_menu.tpl"}
            </div><!-- #header -->
            <div id="main_content" class="opinion-index span-24 wrapper_content_inside_container">
                <div class="span-16">
                    <div class="in-big-title no-background">

						<div class="span-3">
							{if $opinion->type_opinion neq 1 and $opinions[0].path_img}
								<a class="opinion-author" href="{$smarty.const.SITE_URL}{generate_uri 	content_type="opinion_author_frontpage"
																				title=$opinions[0].author_name_slug
																				id=$opinions[0].pk_author}">
									<img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$opinions[0].path_img}" width="110" alt="{$opinion->name}"/>
								</a>
							{/if}
						</div><!--opinion-image-->

						{if $author_id eq 1}
							{assign var="author_name" value="editorial"}
						{elseif $author_id eq 2}
							{assign var="author_name" value="director"}
						{else}
							{assign var="author_name" value=$opinions[0].author_name_slug}
						{/if}

						<h1>
						{if $author_id eq 1}
							Artículos de <a class="CNombreAuthorLink" href="{$smarty.const.SITE_URL}{generate_uri content_type="opinion_author_frontpage" title="editorial" id=1}">Editorial</a>
						{elseif $author_id eq 2}
							Cartas del <a class="CNombreAuthorLink" href="{$smarty.const.SITE_URL}{generate_uri content_type="opinion_author_frontpage" title="title" id=2}">Director</a>
						{else}
							Opinión <br>
							<a class="CNombreAuthorLink" href="{$smarty.const.SITE_URL}{generate_uri 	content_type="opinion_author_frontpage"
																													title=$opinions[0]['name']
																													id=$opinions[0].pk_author}">{$opinions[0]['name']}</a>
						{/if}
						</h1>


					</div><!-- fin lastest-news -->
					<br>
					<hr />
					<div class="opinion-listing-for-author">
						{section name=ac loop=$opinions}

							<div class="ListadoTitlesAuthor">
								<h3 class='title-opinion-on-list'><a href="{$smarty.const.SITE_URL}{generate_uri
																						content_type="opinion"
																						id=$opinions[ac].id
																						date=$opinions[ac].created
																						title=$opinions[ac].title
																						category_name=$opinions[ac].author_name_slug}">{$opinions[ac].title|clearslash}</a></h3>
								<div class="date-opinion-on-list">
									 {articledate updated=$opinions[ac].changed}
								</div>
								<div class="CtextoAuthorlist">
									{$opinions[ac].body|clearslash|truncate:250|strip_tags}
									<p class='moretoread'><a href="{$smarty.const.SITE_URL}{generate_uri
																						content_type="opinion"
																						id=$opinions[ac].id
																						date=$opinions[ac].created
																						title=$opinions[ac].title
																						category_name=$opinions[ac].author_name_slug}"> Siga leyendo &raquo; </a></p>
								</div>
							</div>

						{/section}
						<div class="pagination center clearfix">{$pagination_list->links}</div>
					</div>

                </div>

                {include file="opinion/partials/_frontpage_index_last_column.tpl"}

            </div><!-- fin #main_content -->
        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->
{/block}
