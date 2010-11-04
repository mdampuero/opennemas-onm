<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$opinion->title|clearslash|escape:"html"}</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

    <script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}prototype.js"></script>
    
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}article_printer.css"/>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}article_printer-p.css" media="print" />
    
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
            <a href="#imprimir" onclick="window.print();return false;" class="imprimir-link">Imprimir</a>
        </div>        
        <div>  
            <h2>{$opinion->title|clearslash}</h2>
            
            <div class="firma_nota">
                <div class="firma_nombre">{$author}</div>
                <div class="separadorFirma"></div>
                {articledate article=$opinion created=$opinion->created updated=$opinion->changed}
            </div>
        </div>
        <div class="clearer"></div>
        
        <div class="cuerpo_article">                                
            {$opinion->body|clearslash}
        </div>            
    </div>

    <div class="url">
        {* TODO: fix URI *}
        <sub>[URL] http://www.xornal.com{$opinion->permalink}</sub>
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