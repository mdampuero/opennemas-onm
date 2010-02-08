<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$article->title|clearslash|escape:"html"}</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
    
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}article_printer.css?cacheburst=1259853434"/>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}article_printer-p.css?cacheburst=1259853438" media="print" />
    
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, noarchive, nofollow" /> 
</head>

<body>
<div id="container">
    
    <div class="logoXornalYBanner">        
        <div class="logoXornal">
            <img src="{$params.IMAGE_DIR}xornal-logo.jpg" alt="Xornal.com - Xornal de Galicia"
                 height="70" />
        </div>
    </div>    

    <div class="noticia">        
        <div>
            <div class="breadcrub">{breadcrub values=$breadcrub}</div>
            
            <a href="#imprimir" onclick="window.print();return false;" class="imprimir-link">Imprimir</a>
        </div>
        
        <div>  
            <h2>{$article->title|clearslash}</h2>
            
            <div class="firma_nota">
                <div class="firma_nombre">{if $article->agency|count_words ne '0'}{$article->agency|clearslash}{else}Xornal de Galicia{/if}</div>
                <div class="separadorFirma"></div>
                {* Fecha de creación, NO de modificación e la impresión https://redmine.openhost.es/issues/show/956 *}
                {articledate article=$article created=$article->created}
            </div>
            
            <div class="resumo">                
                {if $photoInt->name}
                <div class="CNoticiaContenedorFoto">
                    <div class="CNoticia_foto">
                        <img src="{$MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" alt="" />
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
    </div>

    <div class="url">
        {* TODO: fix URI *}
        <p><sub>[URL] http://www.xornal.com{$article->permalink}</sub></p>
                
        <p>
            <strong>© XORNAL.COM, Fundado en 1999 como "EL PRIMER DIARIO ELECTRÓNICO DE GALICIA"</strong> <br />
            R/ Galileo Galilei, 4B (Polígono A Grela). <br />
            Redacción: redaccion@xornaldegalicia.com, Publicidad: publicidade@xornaldegalicia.com
        </p>
    </div>
</div>

{literal}
<script type="text/javascript">
Event.observe(window, 'load', function() {
    window.print();
});
</script>
{/literal}

</body>
</html>