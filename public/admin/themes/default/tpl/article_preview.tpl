<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="description" content="description"/>
<meta name="keywords" content="keywords"/>
<meta name="author" content="author"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/bannerPublicidad.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/default.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/galiciaTitulares.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/home_noticias.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/menuSeccion.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/noticia.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/noticia_noticiasMasVistas.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/noticiasRecomendadas.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/paginador.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/pestanyas.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/pie.css"/>
<link rel="stylesheet" type="text/css" href="/themes/xornal/css/style.css"/>

<div class="CNoticia">
  <div class="CNoticiaMargen">
	  <div class="apertura_nota">
	  <div class="antetitulo_nota">{$article->subtitle|clearslash}</div>
	  <div class="firma_nota">
		  <div class="firma_nombre">{$article->agency|clearslash}</div>
		  <div class="separadorFirma"></div>
		  <div class="firma_diario">Xornal de Galicia</div>
		  <div class="separadorFirma"></div>
		  <div class="firma_fecha">{$article->created|date_format:"%d/%m/%y "}</div>
		  <div class="separadorFirma"></div>
		  <div class="firma_hora">{$article->created|date_format:"%H:%M"} h.</div>
	  </div>
	  </div>
	  <h2>{$article->title|clearslash}</h2>
	  <div class="subtitulo_nota">{$article->subtitle|clearslash}</div>
  </div>

  <div class="CNoticiaContenedorFoto">
	  <!--div class="CCabeceraVideo"></div-->
	  {if $laphoto2->name}
	  <div class="CNoticia_foto"><img src="{$MEDIA_IMG_PATH_WEB}{$laphoto2->path_file}{$laphoto2->name}" alt="" /></div>
	  {/if}
  </div>
  <div class="creditos_nota">Foto: EFE</div>
  <div class="CNoticiaMargen">
	  <div class="CContenedorMenuNota">
          <div class="menu_nota">
              <div class="menu_nota_enviar"><a href="#">Enviar noticia</a></div>
              <div class="separadorFirma"></div>
              <div class="menu_nota_imprimir"><a href="#">Imprimir</a></div>
              <div class="separadorFirma"></div>
              <div class="menu_nota_correccion"><a href="#">Enviar correcci&oacute;n</a></div>
          </div>
	  </div>
	  {$article->body|clearslash}
	  <br />
  </div>
</div>
</body>
</html>