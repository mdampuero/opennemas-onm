{*
    OpenNeMas project
    @theme      Lucidity
*}
    
<div class="vote-block span-10 ">
    <div class="vote">      
  {*      <ul class="voting">
                <li><img src="{$params.IMAGE_DIR}/utilities/e-star.png" alt="Email" /></li>
                <li><img src="{$params.IMAGE_DIR}/utilities/e-star.png" alt="Email" /></li>
                <li><img src="{$params.IMAGE_DIR}/utilities/e-star.png" alt="Email" /></li>
                <li><img src="{$params.IMAGE_DIR}/utilities/e-star.png" alt="Email" /></li>
                <li><img src="{$params.IMAGE_DIR}/utilities/e-star.png" alt="Email" /></li>
        </ul>
        Resultados
        <ul class="voting">
                <li><img src="{$params.IMAGE_DIR}/utilities/f-star.png" alt="Email" /></li>
                <li><img src="{$params.IMAGE_DIR}/utilities/f-star.png" alt="Email" /></li>
                <li><img src="{$params.IMAGE_DIR}/utilities/s-star.png" alt="Email" /></li>
                <li><img src="{$params.IMAGE_DIR}/utilities/e-star.png" alt="Email" /></li>
                <li><img src="{$params.IMAGE_DIR}/utilities/e-star.png" alt="Email" /></li>
        </ul>
        
*}

        {if preg_match('/video\.php/',$smarty.server.SCRIPT_NAME) }
            {insert name="rating" id=$video->id page="video" type="vote"}
             - <span>{insert name="numComments" id=$video->id}  Comentarios<span>
         {elseif preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME) }
            {insert name="rating" id=$album->id page="video" type="vote"}
             - <span>{insert name="numComments" id=$album->id}  Comentarios<span>
        {else}
            {insert name="rating" id=$article->id page="article" type="vote"}
               - <span>{insert name="numComments" id=$article->id}  Comentarios<span>
        {/if}

        
    </div>
</div><!-- /vote-bloc -->
