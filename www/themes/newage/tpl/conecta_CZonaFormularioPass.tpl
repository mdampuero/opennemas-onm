<div class="CZonaRegistrarse">
    {if isset($message) }
        <div style="float:right; border:1px; background-color:#AAA; padding:10px;"><b>{$message}</b></div>
    {/if}
    
    <div class="textoConectaXornal">Solicitar nueva contraseña</div>
    <div class="contenedorZonaRegistro">
        <br/>
        <p>
            Introduzca el nick y la cuenta de correo con la que realizó el registro.
        </p>        
        <div class="fileteHorizontalRegistro"></div>
    </div>    
</div>
                        
<div class="CZonaDarseDeAlta" style="margin-bottom: 100px;">
	<form name="rexistro" id="rexistro" method="post" action="#" >
        
        <div class="CEntradasDAlta">
            
            <table class="registro" style="clear: both;">
                <tbody>
                <tr>                    
                    <th>Nick:</th>
                    <td>
                        <input type="text" name="nickDA" id="nickDA" class="required validate-alpha"
                            value="{$smarty.request.nickDA}" />
                    </td>
                        
                    <th>Correo electrónico:</th>
                    <td>
                        <input type="text" name="emailDA"  id="emailDA" class="required validate-email" 
                            value="{$smarty.request.emailDA}"/>
                    </td>
                </tr>
                    
                <tr>
                    <td colspan="4" align="right">
                        <div style="clear: both; padding-top: 10px;"></div>
                        
                        <input type="image" src="{$params.IMAGE_DIR}envio_noticia/botonSolicitar.gif" alt="Guardar"
                                style="cursor:pointer; float: right;" title="Generar una nueva constraseña de Conect@" />
                    </td>
                </tr>
                </tbody>
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
<noscript>Para actualizar sus datos en Conect@ es necesario tener un navegador con soporte javascript habilitado. Disculpe las molestias.</noscript>
