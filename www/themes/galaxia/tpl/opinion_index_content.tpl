<div class="zonaClasificacionContenidosPortadaPC">
    {if ($smarty.request.action eq "read") && (!empty($editorial))}
    <div class="listadoEnlacesPlanConecta">
        <div class="filaPortadaPC">
            <div class="elementoListadoMediaPagPortadaPC">
                <div class="fotoElemOpinion"></div>
                <div class="contSeccionFechaListadoOpinion">
                    <div class="seccionMediaListado"><a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/1/Editorial.html">Editoriales:</a></div>
                    <div class="fechaMediaListado"></div>
                </div>
                <div class="contTextoElemMediaListadoOpinion">
                    <div class="textoElemMediaListadoPortadaPC">
                      {section name="ac" loop=$editorial}
	                        <img src="{$params.IMAGE_DIR}flechitaMenu.gif" alt=""/> 
			                 <a href="{$editorial[ac]->permalink|default:"#"}" >{$editorial[ac]->title|clearslash}</a> <br />		                
		               {/section}                        
                    </div>
                </div>
            </div>
            <div class="fileteVerticalIntraMedia"></div>
            <div class="elementoListadoMediaPagPortadaPC">
                <div class="fotoElemOpinion">
                    { if $dir.photo}
                    <img src="{$MEDIA_IMG_PATH_WEB}{$dir.photo}" alt="{$dir.name}"/>
                    {else}<img src="{$params.IMAGE_DIR}opinion/editorial.jpg" alt="{$dir.name}"/>
                    {/if}
                </div>
                <div class="contSeccionFechaListadoOpinion">
                    <div class="seccionMediaListado"><a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/2/{$dir.name|clearslash}.html"> {$dir.name}</a>, director</div>
                    <div class="fechaMediaListado">{$director->created|date_format:"%d/%m/%y"}</div>
                </div>
                <div class="contTextoElemMediaListadoOpinion">
                    <div class="textoElemMediaListadoPortadaPC">
                        <div class="flechitaTextoPC">
                          <img alt="imagen" src="{$params.IMAGE_DIR}planConecta/flechitaTexto.gif"/>
                        </div>
                        <a href="{$director->permalink}">{$director->title|clearslash}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="fileteHorizontalPC"></div>
    </div>
    {/if}
    <div class="listadoEnlacesPlanConecta">
        {section name=ac loop=$opinions start=0}
            {if $smarty.section.ac.index % 2 == 0}
            <div class="filaPortadaPC">
            {/if}
            <div class="elementoListadoMediaPagPortadaPC">
                <div class="fotoElemOpinion">
                    { if $opinions[ac].path_img}
                    <img src="{$MEDIA_IMG_PATH_WEB}{$opinions[ac].path_img}" alt="{$opinions[ac].name}"/>
                    {else}<img src="{$params.IMAGE_DIR}opinion/editorial.jpg" alt="{$opinions[ac].name}"/>
                    {/if}
                </div>
                <div class="contSeccionFechaListadoOpinion">
                    <div class="seccionMediaListado"><a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/{$opinions[ac].pk_author}/{$opinions[ac].name|clearslash}.html">{$opinions[ac].name}</a></div>
                    <div class="fechaMediaListado">{$opinions[ac].created|date_format:"%d/%m/%y"}</div>
                </div>
                <div class="contTextoElemMediaListadoOpinion">
                    <div class="textoElemMediaListadoPortadaPC">
                        <div class="flechitaTextoPC">
                          <img alt="imagen" src="{$params.IMAGE_DIR}planConecta/flechitaTexto.gif"/>
                        </div>
                        <a href="{$opinions[ac].permalink}">{$opinions[ac].title|clearslash}</a>
                    </div>
                </div>
            </div>
            {if $smarty.section.ac.index % 2 == 0}
                <div class="fileteVerticalIntraMedia"></div>
            {/if}
            {if $smarty.section.ac.index % 2 != 0 || $smarty.section.ac.last}
                </div>
                <div class="fileteHorizontalPC"></div>
            {/if}
        {/section}
       
    </div>
    {if count($opinions) gt 0}			  
	   <p align="center">{$pagination}</p>
	   <br>
	    <p align="center">{$paginate->links}</p>
	{/if}
</div>