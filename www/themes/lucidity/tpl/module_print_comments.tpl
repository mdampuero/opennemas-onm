{*
    OpenNeMas project
    @theme      Lucidity
*}
 
   {section name=c loop=$comments}
    <a name="{$comments[c]->id}"></a>
           <div class="list-comments span-16">
            <div class="comment-wrapper">
                    <div clas="comment-number">{math x=$smarty.section.c.iteration y=$paginacion->_currentPage equation='x+(y-1)*9'}</div>
                    <div class="comment-content span-14 append-1">
                            <strong>{$comments[c]->title|clearslash}</strong>
                            {$comments[c]->body|clearslash}
                    </div>
                    <div class="">
                            <div class="span-5">{insert name="voteComment" id=$comments[c]->id page="article" type="vote"}</div>
                            <div class="span-10">  escrito por
                                    <span class="comment-author">{$comments[c]->author|clearslash}</span>
                                    hace
                                    <span class="comment-time">{$comments[c]->created}( 7 horas 59 minutos)</span>
                            </div>
                    </div>
            </div>
    </div>
     {/section}