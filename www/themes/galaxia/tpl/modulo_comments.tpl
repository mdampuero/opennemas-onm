            <div class="CComentario" id="div_comments">                          
                {section name=c loop=$comments}
                    <div class="CComentario">
                        <a name="{$comments[c]->id}"></a>
                       {if $paginacion}
                           <div class="CNumeroComentario">{math x=$smarty.section.c.iteration y=$paginacion->_currentPage equation='x+(y-1)*9'}</div>
                       {/if}
                        <div class="CInfoComentario">
                            <div class="CTitularComentario">{$comments[c]->title|clearslash}</div>
                            <div class="CDatosComentario">
                            <div class="CNombreComentarista">{$comments[c]->author|clearslash}</div>
                            <div class="CFechaComentario">{$comments[c]->created}</div>
                            </div>
                            <div class="CTextoComentario">{$comments[c]->body|clearslash}</div>
                        </div>
                         {insert name="voteComment" id=$comments[c]->id page="article" type="vote"}
                    </div>
                {/section}
                 <p align="center"> {$paginacion->links}</p>
           </div>