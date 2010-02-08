<!-- ****************** NOTICIAS XPRESS **************** -->
<div style="margin-top:10px" class="containerNoticiasXPress">
  <div class="cabeceraNoticiasXPress"></div>
  <div id='div_articles_home_express' class="listaNoticiasXPress">
      <!-- NOTICIA XPRES -->
      {section name=exp loop=$articles_home_express}
    <div class="noticiaXPress">
        <div class="contHoraNoticiaXPress">
        <div class="horaNoticiaXPress">{$articles_home_express[exp]->created|date_format:"%H:%M"}</div>
        <div class="iconoRayoXPress"></div>
        </div>
        <div class="contTextoFilete">
        <div class="textoNoticiaXPress">
          <a href="{$articles_home_express[exp]->permalink}">{$articles_home_express[exp]->title|clearslash}</a></div>
        <div class="fileteNoticiaXPress">
          <img src="{$params.IMAGE_DIR}noticiasXPress/fileteDashedNoticiasXPress.gif" alt=""/></div>
        </div>
    </div>
      {/section}
   
	  <!-- LINK A MAS NOTICIASXPRESS-->
	    <div class="CContenedorPaginado">
	        <div class="link_mas_nota">+ NoticiasXpress</div>
	        <div class="CPaginas"> {$pages_home_express->links}          
	        </div>
	    </div>
	  <!-- LINK A MAS NOTICIASXPRESS-->
	  
	    <!-- FIN NOTICIA XPRESS -->
  </div>
</div>
<!-- ****************** FIN NOTICIAS XPRESS ************ -->