{if !empty($arraytextos)}
    {section name=c loop=$arraytextos}
        {if $arraytextos[c]->id neq $opinionID[0]->id}
            <div class="elementoListadoMediaPag">

                <div class="contSeccionFechaListado" style="width:660px;margin-left:0px;">
                    <div class="seccionMediaListado"><a href="{$arraytextos[c]->permalink}" style="color:#004B8D;">{$arraytextos[c]->title|clearslash}</a></div>
                    <div class="fechaMediaListado">
                            {assign var=id_author value=$arraytextos[c]->fk_user}
                            Enviada por <b>{$conecta_users[$id_author]->nick} </b> {humandate article=$arraytextos[c] created=$arraytextos[c]->created}
                    </div>
                </div>
                <div class="contTextoElemMediaListado">
                    <div class="textoElemMediaListado">
                        <a href="{$arraytextos[c]->permalink}">{$arraytextos[c]->body|clearslash|truncate:250|strip_tags}</a>
                    </div>
                </div>
                <div class="fileteIntraMedia"></div>
            </div>
        {/if}
    {/section}
    <div class="posPaginadorGaliciaTitulares">
        {if $pages->links}
            <div class="CContenedorPaginado">
                {if $arraytextos[0]->content_type eq '3'} <div class="link_paginador">+ Cartas</div>{else}<div class="link_paginador">+ Opiniones</div>{/if}
                <div class="CPaginas">
                    {$pages->links}
                </div>
            </div>
        {/if}
    </div>
{/if}