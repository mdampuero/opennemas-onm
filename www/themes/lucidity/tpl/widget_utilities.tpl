{*
    OpenNeMas project
    @theme      Lucidity
*}

<div class="utilities span-6 last">
    <ul>
        <li><img src="{$params.IMAGE_DIR}/utilities/send-to-email.png" alt="Email" /></li>
        <li><img src="{$params.IMAGE_DIR}/utilities/separator.png" alt="Email" onclick="javascript:sendbyemail('Show nicer file listings with Apache autoindex module','http://www.mabishu.com/blog/2010/02/17/show-nicer-file-listings-with-apache-autoindex-module/')"/></li>
        <li><img src="{$params.IMAGE_DIR}/utilities/print.png" alt="Print" onclick="javascript:window.print()"/></li>
        <li><img src="{$params.IMAGE_DIR}/utilities/separator.png" alt="Email" /></li>
        <li><img src="{$params.IMAGE_DIR}/utilities/increase-text.png" alt="Increase text" onclick="increaseFontSize()"  /></li>
        <li><img src="{$params.IMAGE_DIR}/utilities/separator.png" alt="Email" /></li>
        <li><img src="{$params.IMAGE_DIR}/utilities/decrease-text.png" alt="Decrease text" onclick="decreaseFontSize()"  /></li>
        <li><img src="{$params.IMAGE_DIR}/utilities/separator.png" alt="Email" /></li>
        <li> <div style="display: inline;" class="actions"><img src="{$params.IMAGE_DIR}/utilities/share.png" alt="Share" />
                <ul style="display: none;">
                  <li><img alt="Send by email to your friends" src="http://www.mabishu.com/wp-content/themes/mabishu-v3/images/icons/tools_email.gif"> <a title="Send by email this article" onclick="sendbyemail('Show nicer file listings with Apache autoindex module','http://www.mabishu.com/blog/2010/02/17/show-nicer-file-listings-with-apache-autoindex-module/')" href="#"> Send by email</a></li>
                  <li><img alt="Print this post" src="http://www.mabishu.com/wp-content/themes/mabishu-v3/images/icons/tools_print.gif"> <a title="Print this article with your printer" href="javascript:window.print()">Print this article</a></li>
                  <li><img alt="Share this post on Twitter" src="http://www.mabishu.com/wp-content/themes/mabishu-v3/images/icons/toolsicon_anim.gif"> <a target="_blank" href="http://twitter.com/home?status=Show nicer file listings with Apache autoindex module%20http://ir.pe/1i9x">Send to Twitter</a></li>                           <li><img alt="Share on Facebook" src="http://www.mabishu.com/wp-content/themes/mabishu-v3/images/icons/facebook-share.gif"> <a title="Click here to share on Facebook" href="http://www.facebook.com/sharer.php?u=http://www.mabishu.com/blog/2010/02/17/show-nicer-file-listings-with-apache-autoindex-module/&amp;t=Show nicer file listings with Apache autoindex module">Share on Facebook</a></li>
                </ul>
            </div>
        </li>
    </ul>
</div><!-- /utilities -->
{*Copied Mabishu 
        <div style="display: inline;" class="actions">
                        <span>~ do some action</span>
                        <ul style="display: none;">
                          <li><img alt="Send by email to your friends" src="http://www.mabishu.com/wp-content/themes/mabishu-v3/images/icons/tools_email.gif"> <a title="Send by email this article" onclick="sendbyemail('Show nicer file listings with Apache autoindex module','http://www.mabishu.com/blog/2010/02/17/show-nicer-file-listings-with-apache-autoindex-module/')" href="#"> Send by email</a></li>
                          <li><img alt="Print this post" src="http://www.mabishu.com/wp-content/themes/mabishu-v3/images/icons/tools_print.gif"> <a title="Print this article with your printer" href="javascript:window.print()">Print this article</a></li>
                          <li><img alt="Share this post on Twitter" src="http://www.mabishu.com/wp-content/themes/mabishu-v3/images/icons/toolsicon_anim.gif"> <a target="_blank" href="http://twitter.com/home?status=Show nicer file listings with Apache autoindex module%20http://ir.pe/1i9x">Send to Twitter</a></li>                           <li><img alt="Share on Facebook" src="http://www.mabishu.com/wp-content/themes/mabishu-v3/images/icons/facebook-share.gif"> <a title="Click here to share on Facebook" href="http://www.facebook.com/sharer.php?u=http://www.mabishu.com/blog/2010/02/17/show-nicer-file-listings-with-apache-autoindex-module/&amp;t=Show nicer file listings with Apache autoindex module">Share on Facebook</a></li>
                                                  </ul>
                    </div>
*}
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
    </script>
{/literal}