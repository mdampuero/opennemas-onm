<div class="CZonaRegistrarse"> 
    {if isset($message) }
       <div style="float:right;border:1px;background-color:#AAA;padding:10px;"><b>{$message}</b></div>
    {/if}

    <div class="textoConectaXornal">Registrarse</div>
    <div class="contenedorZonaRegistro">
        <p>¿Cómo quieres participar en las páginas de Conect@? Tienes varias formas de hacerlo:</p>
        <div class="CZonaSerONoSerRegistro">
            <div class="CZonaSer">
            	<form name="login" id="login" method="post" action="#" > 	            
                <div class="CCabeceraFormulario">
                    <span class="CDestacadoRegistro">
                        <img src="{$params.IMAGE_DIR}noticia/flecha_destacado.gif" alt="imagen"/>
                        Soy usuario de Conect@
                    </span>
                </div>
                <div class="CListaEntradasSoyUsuario">
                    <div class="CEntradaForm">
                        <div class="CTextoEntradaEMail">E-mail:</div>
                        <div class="CInputEntradaEMail">
                            <input type="text" name="email" class="required validate-email" value="{$login_email}" />
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    
                    
                    <div class="CInterlineadoForm"></div>
                    <div class="CEntradaForm">
                        <div class="CTextoEntradaEMail">Contraseña:</div>
                        <div class="CInputEntradaEMail"><input type="password" name="password" class="required" autocomplete="off" /></div>
                    </div>                    

                    {* <div class="CBotonRegistro">
                       <input type="hidden" name="action" value='login'/> 
      				   <input type="hidden" name="op" value='login'/>          
       			       <a href="/conecta/login/" style="cursor:pointer;" onClick="$('login').submit(); return false;">Conect@</a>
       			    </div> *}
                    
                    <div class="CInterlineadoForm"></div> {* Estilos bastardos *}
                    
                    <div class="CEntradaForm">
                        {* <div style="float:left;">
                            <a href="/conecta/olvido/">&raquo; Olvide la contraseña</a>
                        </div> *}
                        
                        <div style="float:right; padding: 4px 8px;">
                            <input type="hidden" name="action" value="login" /> 
                            <input type="hidden" name="op" value="login" />          
                            
                            <script type="text/javascript">
                            /* <![CDATA[ */
                                document.write('<a href="/conecta/login/" id="loginLink" style="Entrar en Conect@"></a>');
                            /* ]]> */
                            </script>
                            <noscript>
                                <input type="submit" value="Entrar en Conect@" />
                            </noscript>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="redirect" value="{$smarty.request.redirect}" />
			</form>                
		   </div>
            
            <div class="CFileteVerticalReg"></div>
            
            <div class="CZonaNoSer">
                <div class="CCabeceraFormulario">
                    <span class="CDestacadoRegistro">
                        <img src="{$params.IMAGE_DIR}noticia/flecha_destacado.gif" alt="imagen"/>
                        NO soy usuario de Conect@
                    </span>
                </div>
                <div class="CInterlineadoForm"></div>            
                <div class="CListadoPuntosNoSoy">
                    <div class="CEntradaForm">
                        {* 1. *} Quiero <a href="/conecta/rexistro/" title="Cumplimentar el formulario de registro">registrarme</a> ahora
                    </div>
                    {* <!--div class="CInterlineadoForm"></div-->
                    <div class="CEntrada2Form">
                        2. No quiero registrarme. Prefiero facilitar mis datos personales cada vez que participe en Conect@
                    </div> 
                    <div class="CBotonRegistro"><a href="#">Conect@</a></div> *}
                </div>
                
                <div class="CInterlineadoForm"></div>
                
                <div class="CCabeceraFormulario">
                    <span class="CDestacadoRegistro">
                        <img src="{$params.IMAGE_DIR}noticia/flecha_destacado.gif" alt="imagen"/>
                        Olvidé la contraseña
                    </span>
                </div>
                <div class="CListaEntradasSoyUsuario">
                    <div class="CEntradaForm">
                        <div style="float:left;">
                            Solicitar una <a href="/conecta/olvido/" title="Generar una nueva contraseña">contraseña nueva</a>.
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        {*<!--div class="fileteHorizontalRegistro"></div-->*}
    </div>
</div>

{literal}
<script language="javascript" type="text/javascript">
/*<![CDATA[*/         
    var loginForm = new Validation('login');
    document.observe("dom:loaded", function() {
        var loginLink = $('loginLink')                
        
        $('loginLink').observe('click', function(event) {
            Event.stop(event);
            
            if( loginForm.validate() ) {
                $('login').submit(); 
            } else {
                //var rows = $$('.CEntradaForm');
                //rows[0].setStyle({height: '50px'});
                $$('.validation-advice').each( function(item) {
                    item.parentNode.parentNode.setStyle({height: '50px'});
                });
            }
        });        
        loginLink.insert( 'Entrar en Conect@' );
        
        // Resaltar botón de login
        new Effect.Highlight(loginLink.parentNode, { duration: 2.0, startcolor: '#ffff99', endcolor: '#ffffff' });    
    });
    
/*]]>*/
</script>
{/literal}