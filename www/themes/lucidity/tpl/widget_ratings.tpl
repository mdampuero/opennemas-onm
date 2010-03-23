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
            {insert name="rating" id=$video->id page="article" type="vote"}
        {else}
            {insert name="rating" id=$article->id page="article" type="vote"}
        {/if}

       {if preg_match('/video\.php/',$smarty.server.SCRIPT_NAME) }
           - <span>{insert name="numComments" id=$video->id}  Comentarios<span>
       {else}
           - <span>{insert name="numComments" id=$article->id}  Comentarios<span>
       {/if}
    </div>
</div><!-- /vote-bloc -->
