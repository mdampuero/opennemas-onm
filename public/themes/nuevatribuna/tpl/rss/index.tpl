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

<style type="text/css">
    #categories-list  {

    }
    #categories-list li{
        list-style:none !important;
        background:#efefef;
        border:1px solid #ddd;
        padding:5px;
        margin:4px;
        width:40%;
        float:left;
    }
    #categories-list li:hover {
        background:#ececec;
        border:1px solid #ececec;
    }
    #categories-list img {
        vertical-align:middle;
    }
</style>
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
                                    <h1 class="inner-article-title">Sindicación de contenidos mediante RSS</h1>
                                <div class="inner-article-subtitle">La suscripci&#243;n a trav&#233;s del formato
                                <a class="enlace" href="http://es.wikipedia.org/wiki/RSS">RSS</a> te permite acceder a los contenidos
                                de <b>nuevatribuna.es</b> en tiempo real y sin necesidad de entrar constantemente en nuestra portada.<br/>
                                Puedes utilizar programas espec&#237;ficos para la lectura de este tipo de formato o, si lo prefieres,
                                puedes hacer uso de agregadores web (como <a class="enlace" href="http://www.bloglines.com/">Bloglines</a>,
                                <a class="enlace" href="http://www.google.es/ig">Google</a>,
                                <a class="enlace" href="http://es.my.yahoo.com/">My Yahoo!</a>,
                                <a class="enlace" href="http://www.live.com/">Live</a> o
                                <a class="enlace" href="http://www.netvibes.com/">Netvibes</a>)
                                que no requieren instalaci&#243;n.
                            </div><!--title-subtitle-legend-->
                            <div class="span-16 inner-article-content  clearfix">
                                <div id="inner-article-body">
                                    <div class="span-16 last" style="border-top:1px solid #CCCCCC; padding-top:5px;">

                                        <h3>&#191;A qu&#233; contenidos de nuevatribuna.es puedes suscribirte a trav&#233;s de RSS?</h2>
                                        {if count($categoriesTree) > 0}
                                        <h3>Categor&iacute;as:</h3>
                                        <ul id="categories-list" class="clearfix">
                                            {foreach from=$categoriesTree item=category}
                                                <li><img src="{$params.IMAGE_DIR}bullets/feed_32.png" alt="RSS de la categor&iacute;a {$category->title|strtolower|ucfirst}"> <a href="{$params.const.SITE_URL}/rss/{$category->name}">RSS de {$category->title|strtolower|ucfirst}</a></li>
                                            {/foreach}
                                        </ul>
                                        {else}
                                        No hay categorias disponibles ahora mismo.
                                        {/if}

                                        {if count($opinionAuthors) > 0}
                                        <h3>Autores de opini&oacute;n:</h3>
                                        <ul id="categories-list" class="clearfix">
                                            {foreach from=$opinionAuthors item=author}
                                                <li><img src="{$params.IMAGE_DIR}bullets/feed_32.png"
                                                         alt="RSS del autor {$authro->name|strtolower|ucfirst}">
                                                        <a href="{$params.const.SITE_URL}/rss/opinion/{$author->pk_author}">RSS de {$author->name|ucfirst}</a></li>
                                            {/foreach}
                                        </ul>
                                        {else}
                                        No hay categorias disponibles ahora mismo.
                                        {/if}
                                    </div>
                                </div>
                            </div>

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

