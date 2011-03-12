{extends file='base/frontpage_layout.tpl'}

{block name="meta" append}
<title>Subscripción - Boletin Diario - {$smarty.const.SITE_TITLE} </title>
<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />

<meta property="og:title" content="{$article->title|clearslash}" />
<meta property="og:description" content="{$article->summary|strip_tags|clearslash}" />
<meta property="og:image" content="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" />
{/block}


{block name='header-css'}
{$smarty.block.parent}
<link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.CSS_DIR}parts/video-js.css" type="text/css" media="screen,projection" />
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
                                <h1 class="inner-article-title">Suscripción al boletín de noticias</h1>
                                <div class="inner-article-subtitle">
                                    Si quiere recibir las últimas noticias y opiniones diariamente en su correo puede hacerlo
                                    dándose de alta en nuestro boletín de noticias. Rellene el formulario y pulse enviar.
                                    <br/><br/>
                                    Si desea dejar de recibir el boletín de noticias rellene el formulario y asegúrese de marcar la opción "Darme de baja".
                            </div>
                        </div>
                            <div class="span-16 inner-article-content span-16 last clearfix">
                                <div id="inner-article-body" class="clearfix">
                                    <div class="inner-article-other-contents">
                                        <div>
                                            <form action="../controllers/subscripcionBoletin.php" method="post">
                                                <table border="0" cellspacing="2" cellpadding="1" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td width="25%" style="text-align:right;"><strong>Nombre:</strong></td>
                                                            <td width="75%" style="text-align:left;"><input name="name" size="16" type="text" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td width="25%" style="text-align:right;"><strong>Correo electrónico:</strong></td>
                                                            <td width="75%" align="center"><input name="email" size="16" type="text" /></td>
                                                        </tr>
                                                        <tr align="center">
                                                            <td width="25%" style="text-align:right;"><strong></strong></td>
                                                            <td>
                                                                <input name="boletin" type="radio" value="alta" selected/> Darme de alta en boletín
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>
                                                                <input name="boletin" type="radio" value="baja" /> Darme de baja en boletín
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:right; vertical-align:top;"><strong></strong></td>
                                                            <td>
                                                                <script type="text/javascript"
                                                                      src="http://www.google.com/recaptcha/api/challenge?k={$smarty.const.RECAPTCHA_PUBLIC_KEY}"></script>
                                                                <noscript>
                                                                 <iframe src="http://www.google.com/recaptcha/api/noscript?k={$smarty.const.RECAPTCHA_PUBLIC_KEY}"
                                                                     height="300" width="500" frameborder="0"></iframe><br>
                                                                 <textarea name="recaptcha_challenge_field" rows="3" cols="40">
                                                                 </textarea>
                                                                 <input type="hidden" name="recaptcha_response_field"
                                                                     value="manual_challenge">
                                                                </noscript>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="25%" align="right"><strong></strong></td>
                                                            <td width="75%" align="center"><input id="submit" name="submit" type="submit" value="Enviar" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan=2><div id="sendMessage"></div></td>
                                                        </tr>
                                                        <input type="hidden" name="action" value="submit" />
                                                        <br />
                                                    </tbody>
                                                </table>
                                            </form>
                                        </div>
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