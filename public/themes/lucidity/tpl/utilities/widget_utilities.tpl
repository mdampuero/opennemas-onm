{*
    OpenNeMas project
    @theme      Lucidity
*}

<div class="utilities span-6 last">
   <ul>
      <li>
        <div style="display: inline;" class="share-actions">
              <a href="#" class="{if $long eq "true"}share-actions-long{else}share-actions{/if} share-action" onclick="share();return false;" title="Compartir en las redes sociales"><span>Compartir en las redes sociales</span></a>
              <ul style="display:none;">
                <li><a class="addthis_button_twitter"><img alt="Share this post on Twitter" src="{$params.IMAGE_DIR}/utilities/toolsicon_anim.gif"> Compartir en twitter</a></li>
                <li><a class="addthis_button_facebook"><img alt="Share on Facebook" src="{$params.IMAGE_DIR}/utilities/facebook-share.gif"> Enviar en Facebook</a></li>
                <li><a class="addthis_button_more">Ver más opciones</a></li>
              </ul>
        </div>
      </li>
      <li><a href="{$sendform_url}" class="share-action utilities-send-by-email" rel="facebox" title="Enviar por email a un amigo"><span>Enviar por email</span></a></li>
      <li><img src="{$params.IMAGE_DIR}utilities/separator.png" alt="Email" onclick="javascript:sendbyemail('Título da nova')"/></li>
      <li><a href="{$print_url}" class="share-action utilities-print" title="Imprimir"><span>Imprimir</span></a></li>
      <li><img src="{$params.IMAGE_DIR}utilities/separator.png" alt="Email" /></li>
      <li><a href="" class="share-action utilities-increase-text"  onclick="increaseFontSize();return false;" title="Incrementar el tamaño del texto"><span>Incrementar el tamaño del texto</span></a></li>
      <li><img src="{$params.IMAGE_DIR}utilities/separator.png" alt="Email" /></li>
      <li><a href="" class="share-action utilities-decrease-text"  onclick="decreaseFontSize();return false;" title="Decrementar el tamaño del texto"><span>Reducir el tamaño del texto</span></a></li>
      <li><img src="{$params.IMAGE_DIR}utilities/separator.png" alt="Email" /></li>
</ul>
</div><!-- /utilities -->

{literal}
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c056c9a5f92f5b4"></script>
<script type="text/javascript">
        $(function (){
            $('a[rel*=facebox]').facebox();
        });
        </script>
{/literal}