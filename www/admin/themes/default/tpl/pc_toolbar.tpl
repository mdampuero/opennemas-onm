{* Toolbar Plan conecta *}
{if preg_match('/pc_letter\.php/',$smarty.server.SCRIPT_NAME)}
        &nbsp;&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'new', 0);"
			onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>N</u>ueva noticia');"
			accesskey="N" tabindex="1"><img src="{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif" border="0" align="absmiddle" /></a>&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'list', 0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>L</u>istado de noticias');" accesskey="L" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/estructura.gif" border="0" align="absmiddle" /></a>&nbsp;

		<img src="{php}echo($this->image_dir);{/php}iconos/separator.gif" border="0" align="absmiddle" />&nbsp;
		<a href="javascript:void(0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=370;this.T_BORDERCOLOR='#637F63';return escape('<p>Para añadir un nuevo contenido pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + N</p><p>Para obtener todo el listado pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/estructura.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + L</p>');"><img src="{php}echo($this->image_dir);{/php}iconos/ayuda.gif" border="0" align="absmiddle" /></a>
{/if}

{if preg_match('/pc_opinion\.php/',$smarty.server.SCRIPT_NAME)}
        &nbsp;&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'new', 0);"
			onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>N</u>ueva opinion');"
			accesskey="N" tabindex="1"><img src="{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif" border="0" align="absmiddle" /></a>&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'list', 0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>L</u>istado de opiniones');" accesskey="L" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/estructura.gif" border="0" align="absmiddle" /></a>&nbsp;

		<img src="{php}echo($this->image_dir);{/php}iconos/separator.gif" border="0" align="absmiddle" />&nbsp;
		<a href="javascript:void(0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=370;this.T_BORDERCOLOR='#637F63';return escape('<p>Para añadir publicidad pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + N</p><p>Para obtener todo el listado pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/estructura.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + L</p>');"><img src="{php}echo($this->image_dir);{/php}iconos/ayuda.gif" border="0" align="absmiddle" /></a>
{/if}



{if preg_match('/pc_photo\.php/',$smarty.server.SCRIPT_NAME)}
        &nbsp;&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'new', 0);"
			onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>N</u>ueva publicidad');"
			accesskey="N" tabindex="1"><img src="{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif" border="0" align="absmiddle" /></a>&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'list', 0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>L</u>istado de publicidades');" accesskey="L" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/estructura.gif" border="0" align="absmiddle" /></a>&nbsp;

		<img src="{php}echo($this->image_dir);{/php}iconos/separator.gif" border="0" align="absmiddle" />&nbsp;
		<a href="javascript:void(0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=370;this.T_BORDERCOLOR='#637F63';return escape('<p>Para añadir publicidad pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + N</p><p>Para obtener todo el listado pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/estructura.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + L</p>');"><img src="{php}echo($this->image_dir);{/php}iconos/ayuda.gif" border="0" align="absmiddle" /></a>
{/if}

{if preg_match('/pc_video\.php/',$smarty.server.SCRIPT_NAME)}
        &nbsp;&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'new', 0);"
			onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>N</u>uevo evento');"
			accesskey="N" tabindex="1"><img src="{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif" border="0" align="absmiddle" /></a>&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'list', 0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>L</u>istado de eventos');" accesskey="L" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/estructura.gif" border="0" align="absmiddle" /></a>&nbsp;

		<img src="{php}echo($this->image_dir);{/php}iconos/separator.gif" border="0" align="absmiddle" />&nbsp;
		<a href="javascript:void(0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=370;this.T_BORDERCOLOR='#637F63';return escape('<p>Para añadir publicidad pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + N</p><p>Para obtener todo el listado pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/estructura.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + L</p>');"><img src="{php}echo($this->image_dir);{/php}iconos/ayuda.gif" border="0" align="absmiddle" /></a>
{/if}

{** USERS Plan Conecta *********************************}
{if preg_match('/pc_user\.php/',$smarty.server.SCRIPT_NAME)}
        &nbsp;&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'new', 0);"
			onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>N</u>uevo usuario');"
			accesskey="N" tabindex="1"><img src="{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif" border="0" align="absmiddle" /></a>&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'list', 0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>L</u>istado de usuarios');" accesskey="L" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/estructura.gif" border="0" align="absmiddle" /></a>&nbsp;

		<img src="{php}echo($this->image_dir);{/php}iconos/separator.gif" border="0" align="absmiddle" />&nbsp;
		<a href="javascript:void(0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=370;this.T_BORDERCOLOR='#637F63';return escape('<p>Para añadir un nuevo usuario pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + N</p><p>Para obtener todo el listado pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/estructura.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + L</p>');"><img src="{php}echo($this->image_dir);{/php}iconos/ayuda.gif" border="0" align="absmiddle" /></a>
{/if}

{if preg_match('/pc_privileges\.php/',$smarty.server.SCRIPT_NAME)}
        &nbsp;&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'new', 0);"
			onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>N</u>uevo permiso');"
			accesskey="N" tabindex="1"><img src="{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif" border="0" align="absmiddle" /></a>&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'list', 0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>L</u>istado de permisos ');" accesskey="L" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/estructura.gif" border="0" align="absmiddle" /></a>&nbsp;

		<img src="{php}echo($this->image_dir);{/php}iconos/separator.gif" border="0" align="absmiddle" />&nbsp;
		<a href="javascript:void(0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=370;this.T_BORDERCOLOR='#637F63';return escape('<p>Para añadir un nuevo permiso pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + N</p><p>Para obtener todo el listado pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/estructura.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + L</p>');"><img src="{php}echo($this->image_dir);{/php}iconos/ayuda.gif" border="0" align="absmiddle" /></a>
{/if}

{if preg_match('/pc_user_groups\.php/',$smarty.server.SCRIPT_NAME)}
        &nbsp;&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'new', 0);"
			onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>N</u>uevo grupo de usuarios');"
			accesskey="N" tabindex="1"><img src="{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif" border="0" align="absmiddle" /></a>&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'list', 0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>L</u>istado de los grupos de usuarios ');" accesskey="L" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/estructura.gif" border="0" align="absmiddle" /></a>&nbsp;

		<img src="{php}echo($this->image_dir);{/php}iconos/separator.gif" border="0" align="absmiddle" />&nbsp;
		<a href="javascript:void(0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=370;this.T_BORDERCOLOR='#637F63';return escape('<p>Para añadir un nuevo grupo de usuarios pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + N</p><p>Para obtener todo el listado pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/estructura.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + L</p>');"><img src="{php}echo($this->image_dir);{/php}iconos/ayuda.gif" border="0" align="absmiddle" /></a>
{/if}




{if preg_match('/category\.php/',$smarty.server.SCRIPT_NAME)}
        &nbsp;&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'new', 0);"
			onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>N</u>ueva categoria');"
			accesskey="N" tabindex="1"><img src="{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif" border="0" align="absmiddle" /></a>&nbsp;
		<a href="#" onclick="enviar(this, '_self', 'list', 0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>L</u>istado de eventos');" accesskey="L" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/estructura.gif" border="0" align="absmiddle" /></a>&nbsp;

		<img src="{php}echo($this->image_dir);{/php}iconos/separator.gif" border="0" align="absmiddle" />&nbsp;
		<a href="javascript:void(0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=370;this.T_BORDERCOLOR='#637F63';return escape('<p>Para añadir publicidad pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/nuevo_contenido.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + N</p><p>Para obtener todo el listado pulsa en <img src=\'{php}echo($this->image_dir);{/php}iconos/estructura.gif\' border=\'0\' align=\'absmiddle\' /> o ALT + L</p>');"><img src="{php}echo($this->image_dir);{/php}iconos/ayuda.gif" border="0" align="absmiddle" /></a>
{/if}


{if preg_match('/pc_mediamanager\.php/',$smarty.server.SCRIPT_NAME)}
    &nbsp;&nbsp;&nbsp;
    <form action="{$smarty.server.SCRIPT_NAME}">
    <a href="#" onclick="enviar(this, '_self', '', 0);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>S</u>ubir una carpeta ');" accesskey="S" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/subir.gif" border="0" align="absmiddle" /></a>&nbsp;
    <input type="hidden" name="path" value="{$path}../" />
    <input type="hidden" name="listmode" value="{$listmode}" />
    <input type="hidden" name="action" value="" />
    </form>
    &nbsp;&nbsp;
    <form action="{$smarty.server.SCRIPT_NAME}">
    <a href="#" onclick="new_folder(this);" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('Nueva <u>C</u>arpeta ');" accesskey="C" tabindex="2"><img src="{php}echo($this->image_dir);{/php}iconos/nueva_carpeta.gif" border="0" align="absmiddle" /></a>&nbsp;
    <input type="hidden" name="path" value="{$path}" />
    <input type="hidden" name="foldername" value="" /><input type="hidden" name="listmode" value="{$listmode}" />
    <input type="hidden" name="action" value="newDir" />
    </form>

    <img src="{php}echo($this->image_dir);{/php}iconos/separator.gif" border="0" align="absmiddle" />&nbsp;
    <a href="http://www.youtube.com/profile_videos?user=galimundo" target="_blank" onmouseover="this.T_BGCOLOR='#EAEAEA';this.T_FONTCOLOR='#425542';this.T_WIDTH=150;this.T_BORDERCOLOR='#637F63';return escape('<u>V</u>&iacute;deos en YouTube');" accesskey="V" tabindex="2"><img src="{php}echo($this->image_dir);{/php}mediamanager/youtube-mini.gif" border="0" align="absmiddle" /></a>&nbsp;

{/if}
