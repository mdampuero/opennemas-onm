<div class="column2">   <!-- PIEZA ESPECIAL NOTICIA -->
      <!-- ****************** NOTICIAS XPRESS **************** -->
    <div style="margin-top:10px;" class="containerNoticiasXPress">
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
			<div class="fileteNoticiaXPress" style="margin-top:0px;">
			  <img src="{$params.IMAGE_DIR}noticiasXPress/fileteDashedNoticiasXPress.gif" alt=""/></div>
		    </div>
		</div>
	      {/section}
	      <!-- FIN NOTICIA XPRESS -->
              <!-- LINK A MAS NOTICIASXPRESS-->
        <div class="CContenedorPaginado">
            <div class="link_mas_nota">+ NoticiasXpress</div>
            <div class="CPaginas">
                {if $pages_home_express->links}
                    {$pages_home_express->links}
                {/if}
            </div>
        </div>
	  <!-- LINK A MAS NOTICIASXPRESS-->
	  </div>
	  
    </div>
         
    <!-- ****************** FIN NOTICIAS XPRESS ************ -->
    <div class="separadorHorizontal"></div>

      <!-- ****************** PUBLICIDAD ******************* -->
      <div class="contBannerYTextoPublicidad">
      {* renderbanner banner=$banner5 photo=$photo5 cssclass="contBannerPublicidad" width="295" height="295"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' *}
      {insert name="renderbanner" type=5 cssclass="contBannerPublicidad" width="295" height="295"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'}
      </div>    
      <!-- **************** FIN PUBLICIDAD ***************** -->      
      <div class="separadorHorizontal"></div>

      <!-- ******************  Humor Grafico (album) **************** -->
      {* <div class="containerNoticiasXPress">
	  <div class="cabeceraHumorGrafico"></div>
      <br />
      <a href="{$SITE_URL}/album/2009/02/28/foto/2009022800165849906.html">
          <img width="295" src="{$SITE_URL}/media/images/album/20090228/2009022800132963510.jpg" style="margin-top: 10px;"/></a>
        <div class="creditos2"><i>Grafico: Pepe Carreiro</i></div>
      </div>
      <div class="containerNoticiasXPress">
      <br />
      <a href="{$SITE_URL}/album/2009/02/28/foto/2009022800195296046.html">
          <img width="295" src="{$SITE_URL}/media/images/album/20090228/2009022800132962918.jpg" style="margin-top: 10px;"/></a>
        <div class="creditos2"><i>Grafico: Orballo</i></div>
      </div>
      <div class="containerNoticiasXPress">
      <br />
      <a href="{$SITE_URL}/album/2009/02/28/foto/2009022801133119074.html">
          <img width="295" src="{$SITE_URL}/media/images/album/20090228/2009022800140299842.jpg" style="margin-top: 10px;"/></a>
        <div class="creditos2"><i>Grafico: Rufus</i></div>
      </div> *}
      <!-- ****************** FIN  Humor Grafico************ -->
  </div>