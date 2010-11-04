{* Botonera *} 
<div id="menu-acciones-admin">
	<div class="subtitle">
		Proceso de envio
	</div>
	
	<div class="steps">
		<img src="{$params.IMAGE_DIR}newsletter/5.gif" width="300" height="40" border="0" />		
	</div>
	
	<ul>
        <li>
            <a href="#" class="admin_add" title="Atrás" style="display: none;">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" alt="" /><br />
				Atrás
            </a>
	    </li>
	</ul>	
</div>

<div class="form">
	<form name="searchForm" id="searchForm" method="post" action="#">              
		{* Valores asistente *}
		<input type="hidden" id="action"     name="action"     value="preview" />
		<input type="hidden" id="postmaster" name="postmaster" value="" />
	</form>
</div>
	
<p></p>