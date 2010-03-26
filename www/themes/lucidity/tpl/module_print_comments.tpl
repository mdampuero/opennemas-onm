{*
    OpenNeMas project
    @theme      Lucidity
*}
 
{section name=c loop=$comments}
<a name="{$comments[c]->id}"></a>
<div class="list-comments span-16">
    <div class="comment-wrapper">
        <div class="comment-number">{math x=$smarty.section.c.iteration y=$paginacion->_currentPage equation='x+(y-1)*9'}</div>
        <div class="comment-content span-14 prepend-2">
            <strong>{$comments[c]->title|clearslash}</strong>
            {$comments[c]->body|clearslash}
        </div>
        <div class="">
            <div class="span-5">{insert name="voteComment" id=$comments[c]->id page="article" type="vote"}</div>
            <div class="span-10">  escrito por
                <span class="comment-author">{$comments[c]->author|clearslash}</span>                                    
                <span class="comment-time">{humandate  created=$comments[c]->created} </span>
            </div>
        </div>
    </div>
</div>
{/section}