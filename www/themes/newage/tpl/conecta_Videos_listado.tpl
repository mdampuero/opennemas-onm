 {if !empty($arrayVideos)}
    {section name=c loop=$arrayVideos}
        {if $arrayVideos[c]->id neq $videoID[0]->id}
            <div class="elementoListadoMediaPag">
                <div class="fotoElemMediaListado">
                    <img style="width:78px;height:94px;" src="http://i4.ytimg.com/vi/{$arrayVideos[c]->code}/default.jpg">
                </div>
                <div class="contSeccionFechaListado">
                    <div class="seccionMediaListado"><a href="{$arrayVideos[c]->permalink}" style="color:#004B8D;">{$arrayVideos[c]->title|clearslash}</a></div>
                    <div class="fechaMediaListado">
                           {assign var=id_author value=$arrayVideos[c]->fk_user}
                            Enviada por <b>{$conecta_users[$id_author]->nick} </b> {humandate article=$arrayVideos[c] created=$arrayVideos[c]->created}

                    </div>
                </div>
                <div class="contTextoElemMediaListado">
                    <div class="textoElemMediaListado">
                        <a href="{$arrayVideos[c]->permalink}">{$arrayVideos[c]->description|clearslash|truncate:250}</a>
                    </div>
                </div>
                <div class="fileteIntraMedia"></div>
            </div>
        {/if}
    {/section}
    <div class="posPaginadorGaliciaTitulares">
      {if $pages->links}
            <div class="CContenedorPaginado">
                <div class="link_paginador">+ Videos</div>
                <div class="CPaginas">
                    {$pages->links}
                </div>
            </div>
      {/if}
    </div>

{/if}