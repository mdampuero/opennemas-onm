{* Botonera *} 
<div id="menu-acciones-admin">
    <div class="subtitle">
		Previsualización                
	</div>
    
    <div class="steps">
		<img src="{$params.IMAGE_DIR}newsletter/4.gif" width="300" height="40" border="0" usemap="#map" />
		{include file="newsletter/wizard.png.map"}
	</div>
    
	<ul>
	    <li>
            <a href="#" class="admin_add" title="Siguiente">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/next.png" alt="" /><br />
				Siguiente
            </a>
	    </li>
        <li>
            <a href="#" class="admin_add" title="Atrás">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" alt="" /><br />
				Atrás
            </a>
	    </li>
	</ul>
</div>

<div class="margin:0 auto; width:70%">
    <div class="form">
        <form name="searchForm" id="searchForm" method="post" action="#">
            <p>
                <label>Asunto:</label>
                <input type="text" name="subject" id="subject" size="80"
                       value="[{$smarty.const.SITE_FULLNAME}] Boletín de noticias {$smarty.now|date_format:"%d/%m/%Y"}" />
            </p>
            
            {* Valores asistente *}
            <input type="hidden" id="action"     name="action"     value="send" />
            <input type="hidden" id="postmaster" name="postmaster" value="" />
        </form>
    </div>
    
    <p>
        <label>Previsualización del boletín:</label>
    </p>
    <div id="preview">
        {* include file="newsletter/preview.html.tpl" *}
        {$htmlContent}
    </div>
</div>

<script type="text/javascript">
var postData = {strip}{$smarty.request.postmaster|default:"null"}{/strip};

{literal}
document.observe('dom:loaded', function() {
    // Set postmaster value
    $('postmaster').value = Object.toJSON(postData);
    
    // Attach click event to button
	var botonera = $$('div#menu-acciones-admin ul li a');
    botonera[0].observe('click', function() {                
		$('searchForm').action.value = 'send';
		$('searchForm').submit();
	});
    
    botonera[1].observe('click', function() {                
		$('searchForm').action.value = 'listAccounts';
		$('searchForm').submit();
	});
    
    $('map').select('area').each(function(tagArea) {
		tagArea.observe('click', function(evt) {
			Event.stop(evt);
            
			var attr = this.getAttribute('action');			
			var form = $('searchForm');			
			
            form.action.value = attr;
			form.submit();		
		});						
	});
});
{/literal}
</script>
