{* Old version without social network authentication *}
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
		    <form name="comentar" id="comentar" onSubmit="return false">
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
                </div>
                <div class="CColumna2Comentar">
                    <div class="CContenedorDatoComentarista">
                        <div class="CTextoComentar">Nombre:</div>
                        <div class="CContainerDato">
                            <input type="text" id="nombre" name="nombre"/>
                        </div>
                    </div>
                    <div class="CContenedorDatoComentarista">
                        <div class="CTextoComentar">Email: (no se visualizar&aacute;)</div>
                        <div class="CContainerDato">
                            <input type="text" id="email" name="email"/>
                        </div>
                    </div>
                    <div class="CContenedorDatoComentarista">
                        <div class="CTextoComentar">Introduce la palabra de la imagen</div>
                        <div class="CTextoInfoKaptcha">C&oacute;digo de verificaci&oacute;n  para prevenir env&iacute;os autom&aacute;ticos.</div>
                        {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)}
                        <div class="CImagenKaptcha"><img alt="kaptcha" src="/artigo/captcha/{$smarty.now|date_format:'%Y/%m/%d'}/{$category_name}/{$subcategory_name}/text-captcha/{$article->id}.jpg"/></div>
                        <input type="hidden" id="id" name="id" value="{$article->id}"/>
                        <input type="hidden" id="category" name="category" value="{$article->category}"/>
                        {elseif preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}
                        <div class="CImagenKaptcha"><img alt="kaptcha" src="/opinions/captcha/{$opinion->id}.jpg"/></div>
                        <input type="hidden" id="id" name="id" value="{$opinion->id}"/>
                        <input type="hidden" id="category" name="category" value="4"/>
                        {elseif preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME)}
                         <div class="CImagenKaptcha"><img alt="kaptcha" src="/opinions/captcha/{$item->id}.jpg"/></div>
                        <input type="hidden" id="id" name="id" value="{$item->id}"/>
                        <input type="hidden" id="category" name="category" value="9"/>
                        <input type="hidden" id="where" name="where" value="pc"/>
                        {/if}
                        <div class="CContainerDato"><input type="text" id="security_code" name="security_code"/></div>
                    </div>
                    <div class="CContenedorDatoComentarista">
                        <div class="CZonaBotonEnviar">
                            <div class="CTextoEnviar">
                                <a href="#" onClick="javascript:save_comment(); return false">Enviar</a>
                            </div>
                        </div>
                        <div class="separadorHorizontalNoticia"></div>
                    </div>

                </div>
		    </form>
           <div class="CContainerZonaTextoNormasComent">
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