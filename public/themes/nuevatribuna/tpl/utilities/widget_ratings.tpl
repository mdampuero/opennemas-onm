{*
    OpenNeMas project
    @theme      Lucidity
*}
<div class="vote-block">
    <div class="vote block clearfix">
        {if preg_match('/videos\.php/',$smarty.server.SCRIPT_NAME)}
            {insert name="rating" id=$video->id page="video" type="vote"}  <span class="num-comments">-  {insert name="numComments" id=$video->id}  Comentarios</span>
        {elseif preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME)}
            {insert name="rating" id=$contentId page="video" type="vote"} <span class="num-comments">- {insert name="numComments" id=$contentId}  Comentarios</span>
        {elseif preg_match('/opinion(.*)\.php/',$smarty.server.SCRIPT_NAME)}
            {insert name="rating" id=$opinion->id page="article" type="vote"} <span class="num-comments">- {insert name="numComments" id=$opinion->id}  Comentarios</span>
        {elseif preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME)}
            {insert name="rating" id=$poll->id page="poll" type="vote"} <span class="num-comments">- {insert name="numComments" id=$poll->id}  Comentarios</span>
        {else}
            {insert name="rating" id=$article->id page="article" type="vote"} <span class="num-comments">- {insert name="numComments" id=$article->id}  Comentarios</span>
        {/if}
    </div>
</div><!-- /vote-bloc -->
