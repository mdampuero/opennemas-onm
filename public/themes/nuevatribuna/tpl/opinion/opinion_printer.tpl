<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$opinion->title|clearslash|escape:"html"} - {$opinion->name|clearslash|escape:"html"} - {$smarty.const.SITE_FULLNAME}</title>
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
                <h1>{$opinion->title|clearslash}</h1>

                <div class="firma_nota">
                    <div class="firma_nombre">
                        <strong>
                            {$opinion->name|clearslash|escape:"html"}
                        </strong> -
                        {articledate article=$opinion created=$opinion->created updated=$opinion->changed}
                    </div>
                </div>
            </div>
            <div class="clearer"></div>

            <div class="cuerpo_article">
                {$opinion->body|clearslash}
            </div>



            <div>
                <small>Puede ver este artículo de opinión en <a href="{$smarty.const.SITE_URL}">{$smarty.const.SITE}</a>:
                <a href="{$smarty.const.SITE_URL}{generate_uri   content_type="opinion"
                            id=$opinion->id
                            date=$opinion->created
                            title=$opinion->title
                            category_name=$opinion->author_name_slug}"
                   title="{$opinion->title}">{$smarty.const.SITE_URL}{generate_uri  content_type="opinion"
                                                                                    id=$opinion->id
                                                                                    date=$opinion->created
                                                                                    title=$opinion->title
                                                                                    category_name=$opinion->author_name_slug}</a></small>
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
        $(document).ready(function() {
            window.print();
        });
    </script>
</body>
</html>
