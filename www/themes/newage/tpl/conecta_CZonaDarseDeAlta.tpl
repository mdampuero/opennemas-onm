<div class="CZonaRegistrarse">
    {if isset($message) }
       <div style="float:right;border:1px;background-color:#AAA;padding:10px;">
        <h4>{$message}</h4>
        <ul style="list-style: circle; float: none; margin-left: 20px;">
         {section name="errs" loop=$errors}
         <li>{$errors[errs]}</li>
         {/section}
        </ul>    
       </div>
    {/if}
    
    <div class="textoConectaXornal">Registrarse</div>
    <div class="contenedorZonaRegistro">
        <br/>
        <p>
            ¿Quieres ser colaborador de Xornal de Galicia? A partir de ahora puedes participar en la redacción del diario a través de la sección Conect@.¿Cómo? Muy sencillo. Para enviar noticias, fotos, etc. sólo tienes que
            registrarte rellenando el siguiente formulario. Una vez registrado, el usuario podrá enviar sus noticias, fotos, etc simplemente accediendo a través de un correo electrónico y una contraseña. Ahora, los lectores de Xornal de Galicia se convierten en periodistas.
        </p>
        <br/>
        <p>
            Las noticias, fotos, vídeos, textos que envíen los lectores pasarán a una cola de espera que gestionará la sección de Opinión.
            Los redactores de Xornal de Galicia se encargarán de moderar los contenidos y subirlos a la web.
        </p>
        <br/>
    
        <div class="fileteHorizontalRegistro"></div>
    </div>    
</div>
                        
<div class="CZonaDarseDeAlta" style="margin-bottom: 100px;">
	<form name="rexistro" id="rexistro" method="POST" action="#" >
        <div class="CCabeceraFormulario">
            <span class="CDestacadoRegistro">
                <img src="{$params.IMAGE_DIR}noticia/flecha_destacado.gif" alt="imagen"/>
                Registro para darse de alta
            </span>
        </div>
        
        <div class="CEntradasDAlta">
            
            <table class="registro" style="clear: both;">
                <tr>                    
                    <th>Nick:</th>
                    <td>
                        <input type="text" name="nickDA" id="nickDA" class="required validate-alpha validate-min-length check-nick"
                            value="{$smarty.request.nickDA}" />
                    </td>
                        
                    <th>Correo electrónico:<a href="#aviso"><sup>(*)</sup></a></th>
                    <td>
                        <input type="text" name="emailDA"  id="emailDA" class="required validate-email check-email"
                            value="{$smarty.request.emailDA}"/>
                    </td>
                </tr>
                
                <tr>
                    <th>Contraseña: <a href="#aviso"><sup>(*)</sup></a></th>
                    <td><input type="password" name="passDA" id="passDA" class="required validate-password"/></td>
                
                    <th>Repetir Contraseña:</th>
                    <td><input type="password" name="repPasswordDA" id="repPasswordDA" class="required revalidate-password"/></td>
                </tr>

                <tr>
                    <th>Nombre:</th>
                    <td colspan="3">
                        <input type="text" name="nombreDA" id="nombreDA" size="30" class="required validate-alpha"
                            value="{$smarty.request.nombreDA}" />
                    </td>
                </tr>

                <tr>
                    <th>Primer apellido:</th>
                    <td colspan="3">
                        <input type="text" name="apellidoDA"  id="apellidoDA" size="40" class="required validate-alpha"
                            value="{$smarty.request.apellidoDA}"/>
                    </td>
                </tr>
                    
                <tr>
                    <th>Segundo apellido:</th>
                    <td colspan="3">
                        <input type="text" name="segApellidoDA" id="segApellidoDA" size="40" style="border:1px solid #7f7f7f;"
                            value="{$smarty.request.segApellidoDA}" />
                    </td>
                </tr>                    

                <tr>
                    <th>DNI o Pasaporte: <br /><sub>(DNI:99999999L, NIE:X9999999L)</sub></th>
                    <td>
                        <input type="text" name="dniDA" id="dniDA" class="validate-pasaporte" size="15" maxlength="15" style="border:1px solid #7f7f7f;"
                                value="{$smarty.request.dniDA}" />
                    </td>
                
                    <th>Fecha de nacimiento: <br /><sub>(dd/mm/yyyy)</sub></th>
                    <td>
                        <input type="text" name="fechaNacimientoDA" id="fechaNacimientoDA" size="12" maxlength="10"
                            class="validate-date-au" style="border:1px solid #7f7f7f;"
                            value="{$smarty.request.fechaNacimientoDA}" />
                    </td>
                </tr>

                <tr>
                    <th>Teléfono móvil: <br /><sub>(999-999-999)</sub></th>
                    <td><input type="text" name="movilDA" id="movilDA" {* class="validate-phone"*} size="14" style="border:1px solid #7f7f7f;"
                        value="{$smarty.request.movilDA}" /></td>
                
                    <th>Teléfono fijo: <br /><sub>(999-999-999)</sub></th>
                    <td><input type="text" name="telefDA" id="telefDA" {* class="validate-phone"*} size="14" style="border:1px solid #7f7f7f;"
                        value="{$smarty.request.telefDA}" /></td>
                </tr>
                    
                <tr>
                    <th>País de residencia:</th>
                    <td><input type="text" name="paisDA" id="paisDA" class ="validate-alpha" style="border:1px solid #7f7f7f;"
                        value="{$smarty.request.paisDA}"  /></td>
                
                    <th>Ciudad/población:</th>
                    <td><input type="text" name="poblacionDA" id="poblacionDA" class ="validate-alpha" style="border:1px solid #7f7f7f;"
                        value="{$smarty.request.poblacionDA}"  /></td>
                </tr>

                <tr>
                    <td colspan="4" align="right">
                        <div style="clear: both;"></div>
                        
                        <input type="image" src="{$params.IMAGE_DIR}envio_noticia/botonRegistrar.gif"
                                style="cursor:pointer; float: right;" title="Registrarse en Conect@" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4">                        
                        <strong>BASES LEGALES:</strong>
                        <p align="justify">
                            <label>
                                <input type="checkbox" class="check-baseslegales" name="check_conditions" value="1"
                                    style="border:1px solid #7f7f7f;" />
                                Acepto las condiciones de uso de este <a href="{$smarty.const.SITE_URL}" title="Xornal.com">portal</a>
                                y confirmo haber leído, aceptado y comprendido la política de privacidad existente.
                            </label>
                        </p>
            
                        <p align="justify">            
                            <strong>Aclaración:</strong> La Dirección de Xornal de Galicia se reserva el derecho a publicar, editar y cortar los contenidos remitidos por los participantes por razones de espacio y claridad. No se publicarán contenidos contrarios a la ley, la moral o al orden público, que infrinjan derechos de propiedad intelectual, industrial o contengan cualquier vicio, defecto, virus informático o rutina de software similar. 
                        </p>
                    </td>
                </tr>
                        
                <tr>                    
                    <td colspan="4">
                        <a name="aviso"></a>
                        <strong>(*) El correo electrónico y la contraseña serán tus claves.</strong>                        
                    </td>                    
                </tr>
            </table>                                    
            
        </div>
	</form>                                

</div>

{* Validaciones personalizadas: validate-password, revalidate-password, validate-phone, validate-dni, check-nick, check-email *}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}registro-validations.js"></script>
<script language="javascript" type="text/javascript">
/*<![CDATA[*/         
    new Validation('rexistro');  
/*]]>*/
</script>
<noscript>Para realizar el registro en Conect@ es necesario tener un navegador con soporte javascript habilitado. Disculpe las molestias.</noscript>

