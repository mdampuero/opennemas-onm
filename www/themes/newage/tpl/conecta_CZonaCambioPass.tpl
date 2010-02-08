<div class="CZonaRegistrarse">
    {if isset($message) }
       <div style="float:right;border:1px;background-color:#AAA;padding:10px;"><b>{$message}</b></div>
    {/if}
    
    <div class="textoConectaXornal">Cambio de contraseña</div>
    <div class="contenedorZonaRegistro">
        <br/>
        <p>
            Introduzce tu contraseña actual como verificación y a continuación la nueva contraseña dos veces.
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
                    <th>Contraseña actual:</th>
                    <td><input type="password" name="passOld" id="passOld" class="required validate-password-notnick" autocomplete="off" /></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                
                <tr>
                    <td colspan="4" style=" background-repeat: repeat-x; background-position: center; background-image:url({$params.IMAGE_DIR}fotoVideoDia/fileteDashedDeportesXPress.gif);"> &nbsp; </td>
                </tr>
                
                <tr>
                    <th>Nueva Contraseña:</th>
                    <td><input type="password" name="passDA" id="passDA" class="required validate-password-notnick" autocomplete="off" /></td>
                
                    <th>Repetir Contraseña:</th>
                    <td><input type="password" name="repPasswordDA" id="repPasswordDA" class="required revalidate-password" autocomplete="off" /></td>
                </tr> 
                <tr>
                    <td colspan="4" align="right">
                        <div style="clear: both; padding-top: 10px;"></div>
                        
                        <input type="image" src="{$params.IMAGE_DIR}envio_noticia/botonGuardar.gif" alt="Guardar"
                                style="cursor:pointer; float: right;" title="Actualizar su nueva constraseña de Conect@" />
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
