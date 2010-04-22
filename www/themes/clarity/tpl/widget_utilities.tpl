{*
    OpenNeMas project
    @theme      Lucidity
*}

<div class="utilities span-6 last">
   <ul>
    <li><a href="" class="utilities-send-by-email"  onclick="return false;" title="Enviar por email a un amigo"><span>Enviar por email</span></a></li>
    <li><img src="{$params.IMAGE_DIR}utilities/separator.png" alt="Email" onclick="javascript:sendbyemail('Título da nova')"/></li>
    <li><a href="" class="utilities-print" onclick="javascript:window.print();return false;" title="Imprimir"><span>Imprimir</span></a></li>
    <li><img src="{$params.IMAGE_DIR}utilities/separator.png" alt="Email" /></li>
    <li><a href="" class="utilities-increase-text"  onclick="increaseFontSize();return false;" title="Incrementar el tamaño del texto"><span>Incrementar el tamaño del texto</span></a></li>
    <li><img src="{$params.IMAGE_DIR}utilities/separator.png" alt="Email" /></li>
    <li><a href="" class="utilities-decrease-text"  onclick="decreaseFontSize();return false;" title="Decrementar el tamaño del texto"><span>Reducir el tamaño del texto</span></a></li>
    <li><img src="{$params.IMAGE_DIR}utilities/separator.png" alt="Email" /></li>
    <li>
      <div style="display: inline;" class="share-actions">
            <a href="#" class="utilities-share" onclick="share();return false;" title="Compartir en las redes sociales"><span>Compartir en las redes sociales</span></a>
            <ul style="display:none;">
              <li><img alt="Share this post on Twitter" src="/themes/lucidity/images/utilities/toolsicon_anim.gif"> <a title="Compartir en Twiter" target="_blank" href="http://twitter.com/home?status={if !empty($article->title_int)}{$article->title_int|clearslash}{else}{$article->title|clearslash}{/if} {$smarty.const.SITE_URL}{$article->permalink}">Send to Twitter</a></li>
              <li><img alt="Share on Facebook" src="/themes/lucidity/images/utilities/facebook-share.gif"> <a title="Compartir en Facebook" href="http://www.facebook.com/sharer.php?u={$smarty.const.SITE_URL}{$article->permalink}&t={if !empty($article->title_int)}{$article->title_int|clearslash}{else}{$article->title|clearslash}{/if}">Share on Facebook</a></li>
            </ul>
      </div>
    </li>
</ul>
</div><!-- /utilities -->

