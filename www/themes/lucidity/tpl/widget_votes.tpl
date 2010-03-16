{*
    OpenNeMas project
    @theme      Lucidity
*}
    
<div class="vote-block span-10 ">
    <div class="vote">
        Vote
        <ul class="voting">
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
         - <span>{insert name="numComments" id=$article->id}  Comentarios<span>
    </div>
</div><!-- /vote-bloc -->
 