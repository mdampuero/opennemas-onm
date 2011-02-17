<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$article->title|clearslash|escape:"html"}</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, noarchive, nofollow" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}print/article_printer.css" media="screen,print"/>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}print/article_printer-p.css" media="print"/>
</head>

<body>
    <div id="container">

        <div class="header">
            <div class="logo">
                <img src="{$params.IMAGE_DIR}logos/nuevatribuna-header.png" alt="" style="height:25px" />
                <div class="breadcrub">{breadcrub values=$breadcrub}</div>
            </div>
            <a id="imprimir" href="#imprimir" onclick="window.print();return false;" class="imprimir-link">Imprimir</a>
        </div>
        <hr class="new-separator" />
        <div class="noticia">

            <div>
                <h1>{$article->title|clearslash}</h1>

                <div class="firma_nota">
                    <div class="firma_nombre"><strong>{if $article->agency|count_words ne '0'}{$article->agency|clearslash}{else}{$smarty.const.SITE_FULLNAME}{/if}</strong> - {articledate article=$article created=$article->created}</div>
                </div>
                <hr class="new-separator" />
                <div class="resumo">
                    {if $photoInt->name}
                    <div class="onm-new-title-print">
                        <div class="CNoticia_foto">
                            <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" alt="" />
                        </div>
                        <div class="clear"></div>
                        <div class="creditos_nota">{$article->img2_footer|clearslash}</div>
                    </div>
                    {/if}

                    {$article->summary|clearslash}
                </div>
            </div>
            <div class="clearer"></div>

            <div class="cuerpo_article">
                {$article->body|clearslash}
            </div>

            <div>
                <small>Puede ver esta noticia en <a href="{$smarty.const.SITE_URL}">{$smarty.const.SITE}</a>:
                <a href="/{$article->uri|clearslash}"
                    title="{$article->title|clearslash|clean_for_html_attributes}">{$smarty.const.SITE_URL}{$article->uri|clearslash}</a></small>
            </div>
        </div>

        <hr class="new-separator" />
        <div>
            <div class="span-12 contact-dates">
                &copy; medio digital de información general<br/>
                    editado por <strong>Página 7 Comunicación S.L.</strong><br/>
                    C/ Noblejas, 5 Bajo - 28013 Madrid
            </div>
        </div>
    </div>
    <script  type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
    <script  type="text/javascript">
    {literal}
        $(document).ready(function() {
            window.print();
        });
    {/literal}
    </script>

</body>
</html>
