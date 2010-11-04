<div id="COpina" class="COpina">
	<div class="CContenedorOpina">
        <div class="CCabeceraOpina"></div>
        <div class="CComentarios">
            <div class="CContenedorComentarios">
            {if preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}
                {insert name="comments" id=$opinion->id}
            {elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) || (preg_match('/preview_content\.php/',$smarty.server.SCRIPT_NAME)||($smarty.request.action eq "article"))}
                {insert name="comments" id=$article->id}
            {else}
              
                {insert name="comments" id=$item->id where='pc'}
            {/if}
            </div>
        </div>
        <div class="CComentar">
            <a name="#envio-comentario"></a>
		    <form name="comentar" id="comentar" onSubmit="return false;">
                
                <div class="CColumna1Comentar">
                    <div class="CContenedorDatoComentarista">
                        <div class="CTextoComentar">T&iacute;tulo:</div>
                        <div class="CContainerDato"><input type="text" id="title" name="title"/></div>
                    </div>
                    <div class="CContenedorDatoComentarista">
                        <div class="CTextoComentar">
                            Comentario:
                        </div>
                        <div class="CMarcoZonaTextAreaComentario">
                            <div class="CZonaTextAreaComentario">
                                <textarea rows="" cols="" name="textareacomentario" id="textareacomentario" class="textareaComentario"></textarea>
                            </div>
                        </div>                        
                    </div>
                    
                    <div class="CContenedorDatoComentarista">
                        <div class="CZonaBotonEnviar">
                            <div class="CTextoEnviar">
                                <a href="javascript:void(0);" id="btnEnviar" style="display: none;">Enviar &raquo;</a>
                            </div>
                        </div>                        
                    </div>
                </div>
                
                <div class="CColumna2Comentar" id="authContainer">
                    {if !isset($smarty.session.pc_user)}
                        {include file="boxAuth/default.tpl"}
                    {else}
                        {include file="boxAuth/conecta.tpl"}
                    {/if}                                        
                </div>
                
                {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)}
                    <input type="hidden" id="id" name="id" value="{$article->id}"/>
                    <input type="hidden" id="category" name="category" value="{$article->category}"/>
                {elseif preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}
                    <input type="hidden" id="id" name="id" value="{$opinion->id}"/>
                    <input type="hidden" id="category" name="category" value="4"/>
                {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME)}
                    <input type="hidden" id="id" name="id" value="{$item->id}"/>
                    <input type="hidden" id="category" name="category" value="9"/>
                    <input type="hidden" id="where" name="where" value="pc"/>
                {/if}
		    </form>
            
            <div class="CContainerZonaTextoNormasComent">
                <hr />
                
                <div class="CTextoNormasComent">
                    Esta p&aacute;gina publica todo tipo de opiniones, r&eacute;plicas y sugerencias de inter&eacute;s general, siempre que sean respetuosas hacia las personas e intituciones.<br/>
                    Se aconseja un <strong>m&aacute;ximo de 15</strong> l&iacute;neas, que podr&aacute;n ser extractadas por nuestra Redacci&oacute;n.<br/>
                    Los autores deben hacer constar: <em>nombre</em> y <em>apellidos</em>, y <em>e-mail</em>.<br/>
                    Aquellos textos que no se ajusten a estos criterios podr&aacute;n ser retirados de la web.
                </div>
            </div>
            
		</div>
	</div>
</div>

<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/es_ES" type="text/javascript"></script> 
{literal}
<script type="text/javascript">
document.observe('dom:loaded', function() {
    CommentForm = new CommentFormClass({box: 'authContainer', fbApiKey: '{/literal}{$smarty.const.FB_APP_APIKEY}{literal}'});
    CommentForm.initFb(); // FB.init        
});
</script>
{/literal}
