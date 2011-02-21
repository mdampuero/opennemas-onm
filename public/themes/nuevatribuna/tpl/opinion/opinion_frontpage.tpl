{extends file='base/frontpage_layout.tpl'}

{block name="meta"}
    {$smarty.block.parent}
    <title>Articulos Opinion - Opinión de Galicia - {$smarty.const.SITE_TITLE}</title>
    <meta name="keywords" content="opinion, {$smarty.const.SITE_KEYWORDS}" />
    <meta name="description" content="Opinión: {$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />
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

{block name="footer-js" append}
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

            <div id="main_content" class="opinion-index wrapper_content_inside_container span-24">

                <div class="in-big-title span-24">

                    <h1>Opiniones</h1>
                    <p class="in-subtitle">Conozca las últimas opiniones de nuestros redactores y colaboradores</p>

                </div><!-- fin lastest-news -->

                <div class="span-16">
                    {include file="opinion/partials/_frontpage_index.tpl"}
                </div>

                {include file="opinion/partials/_frontpage_index_last_column.tpl"}

            </div><!-- fin #main_content -->

        </div><!-- fin .container -->

    </div><!-- fin .wrapper -->
{/block}
