 {if !empty($arrayPhotos)}
    {section name=c loop=$arrayPhotos}
        {if $arrayPhotos[c]->id neq $photoID[0]->id}
	        <div class="elementoListadoMediaPag">
	            <div class="CfotoElemMediaListado">
	                <img style="width:78px;height:94px;" src="{$smarty.const.MEDIA_CONECTA_WEB}{$arrayPhotos[c]->path_file}">
	            </div>
	            <div class="contSeccionFechaListado">
	                <div class="seccionMediaListado"><a href="{$arrayPhotos[c]->permalink}" style="color:#004B8D;">{$arrayPhotos[c]->title|clearslash}</a></div>
	                <div class="fechaMediaListado">
                            {assign var=id_author value=$arrayPhotos[c]->fk_user}
                            Enviada por <b>{$conecta_users[$id_author]->nick} </b> {humandate article=$arrayPhotos[c] created=$arrayPhotos[c]->created}
                        </div>
	            </div>
	            <div class="contTextoElemMediaListado">
	                <div class="textoElemMediaListado">
	                    <a href="{$arrayPhotos[c]->permalink}">{$arrayPhotos[c]->description|clearslash}</a>
	                </div>
	            </div>
	            <div class="fileteIntraMedia"></div>
	        </div>
        {/if}
     {/section}
     <div class="posPaginadorGaliciaTitulares">
         {if $pages->links}
            <div class="CContenedorPaginado">
                    <div class="link_paginador">+ Fotos</div>
                    <div class="CPaginas">
                        {$pages->links}
                    </div>
            </div>       
        {/if}
     </div>
{/if}