<div class="CContenedorEnviarA">
<div class="CCabeceraEnviarA">Compartir noticia:</div>
{if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) }
  <div class="CCuerpoEnviarA">
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/google.gif" alt="Google"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}">Google</a></div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://meneame.net/submit.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/meneame.gif" alt="Meneame"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://meneame.net/submit.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}">Men&eacute;ame</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://del.icio.us/post?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/delicious.gif" alt="del.icio.us"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://del.icio.us/post?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}">del.icio.us</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://www.technorati.com/faves?add=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/technorati.gif" alt="Technorati"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://www.technorati.com/faves?add=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}">Technorati</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/yahoo.gif" alt="Yahoo!"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}">Yahoo!</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://chuza.org/submit.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/chuza.gif" alt="Chuza"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://chuza.org/submit.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}">Chuza</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://www.digg.com/submit?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/digg.gif" alt="Digg"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
		<a href="http://www.digg.com/submit?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}">Digg</a>
	    </div>
	  </div>	  
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://www.stumbleupon.com/refer.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/stumble.gif" alt="Stumble"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://www.stumbleupon.com/refer.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}">Stumble</a>
	    </div>
	  </div>
  </div>
{elseif preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME) }
  <div class="CCuerpoEnviarA">
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/google.gif" alt="Google"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}">Gooogle</a></div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://meneame.net/submit.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/meneame.gif" alt="Men�ame"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://meneame.net/submit.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}">Menéame</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://del.icio.us/post?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/delicious.gif" alt="del.icio.us"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://del.icio.us/post?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}">del.icio.us</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://www.technorati.com/faves?add=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/technorati.gif" alt="Technorati"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://www.technorati.com/faves?add=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}">Technorati</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/yahoo.gif" alt="Yahoo!"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}">Yahoo!</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://chuza.org/submit.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/chuza.gif" alt="Chuza"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://chuza.org/submit.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}">Chuza</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://www.digg.com/submit?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/digg.gif" alt="Digg"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
		<a href="http://www.digg.com/submit?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}">Digg</a>
	    </div>
	  </div>
	  <div class="CDestinoEnviarA">
	    <div class="CLogoDestinoEnviarA">
	      <a href="http://www.stumbleupon.com/refer.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}"><img src="{$params.IMAGE_DIR}enviarA/stumble.gif" alt="Stumble"/></a>
	    </div>
	    <div class="CTextoDestinoEnviarA">
	      <a href="http://www.stumbleupon.com/refer.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}">Stumble</a>
	    </div>
	  </div>
  </div>
{/if}
</div>
