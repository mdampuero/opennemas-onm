{*
    OpenNeMas project
    @theme      Lucidity
*}

<div class="utilities span-6 last">
   <ul>
    <li><a href="" class="utilities-send-by-email" title="Enviar por email a un amigo"><span>Enviar por email</span></a></li>
    <li><img src="images/utilities/separator.png" alt="Email" onclick="javascript:sendbyemail('Título da nova')"/></li>
    <li><a href="" class="utilities-print" onclick="javascript:window.print()" title="Imprimir"><span>Imprimir</span></a></li>
    <li><img src="images/utilities/separator.png" alt="Email" /></li>
    <li><a href="" class="utilities-increase-text"  onclick="increaseFontSize()" title="Incrementar el tamaño del texto"><span>Incrementar el tamaño del texto</span></a></li>
    <li><img src="images/utilities/separator.png" alt="Email" /></li>
    <li><a href="" class="utilities-decrease-text"  onclick="decreaseFontSize()" title="Decrementar el tamaño del texto"><span>Reducir el tamaño del texto</span></a></li>
    <li><img src="images/utilities/separator.png" alt="Email" /></li>
    <li><a href="" class="utilities-share"  onclick="share()" title="Compartir en las redes sociales"><span>Compartir en las redes sociales</span></a>
     <div style="display: inline;" class="actions"><img src="{$params.IMAGE_DIR}/utilities/share.png" alt="Share" />
                <ul style="display: none;">
                  <li><img alt="Send by email to your friends" src="{$params.IMAGE_DIR}icons/tools_email.gif"> <a title="Send by email this article" onclick="sendbyemail('Show nicer file listings with Apache autoindex module','http://www.mabishu.com/blog/2010/02/17/show-nicer-file-listings-with-apache-autoindex-module/')" href="#"> Send by email</a></li>
                  <li><img alt="Print this post" src="{$params.IMAGE_DIR}icons/tools_print.gif"> <a title="Print this article with your printer" href="javascript:window.print()">Print this article</a></li>
                  <li><img alt="Share this post on Twitter" src="{$params.IMAGE_DIR}icons/toolsicon_anim.gif"> <a target="_blank" href="http://twitter.com/home?status=Show nicer file listings with Apache autoindex module%20http://ir.pe/1i9x">Send to Twitter</a></li>                           
                  <li><img alt="Share on Facebook" src="{$params.IMAGE_DIR}icons/facebook-share.gif"> <a title="Click here to share on Facebook" href="http://www.facebook.com/sharer.php?u=http://www.mabishu.com/blog/2010/02/17/show-nicer-file-listings-with-apache-autoindex-module/&amp;t=Show nicer file listings with Apache autoindex module">Share on Facebook</a></li>
                </ul>
            </div>
        </li>
    </ul>
</div><!-- /utilities -->


{literal}  <script type="text/javascript">
jQuery(document).ready(function(){

  $lock=false;
  jQuery("div.actions").hover(
    function () {
      if (!$lock){
        $lock=true;
        jQuery(this).children("ul").fadeIn("fast");
      }
      $lock=false;
    },
    function () {
      if (!$lock){
        $lock=true;
        jQuery(this).children("ul").fadeOut("fast");
      }
      $lock=false;
    }
  );
}
    </script>
{/literal}