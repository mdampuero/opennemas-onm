<div class="CZonaRegistrarse">
    {if isset($message) }
       <div style="float:right;border:1px;background-color:#AAA;padding:10px;"><b>{$message}</b></div>
    {/if}
    
    <div class="textoConectaXornal">Perfíl</div>
    <div class="contenedorZonaRegistro">
        <br/>
        <p>
            <img src="{$params.IMAGE_DIR}envio_noticia/info.png" border="0" alt="" style="vertical-align: middle;" />
            Tu cuenta de correo y tu <em>nick</em> no pueden actualizarse. <br />
            <ul style="margin-left: 40px; padding: 8px 10px; list-style-type: circle;">
                <li>Nick: <strong>{$user->nick}</strong></li>
                <li>Correo electrónico: <strong>{$user->email}</strong></li>
            </ul>
        </p>                                    
        
        <div class="fileteHorizontalRegistro"></div>
    </div>    
</div>
                        
<div class="CZonaDarseDeAlta" style="margin-bottom: 100px;">
	<form name="rexistro" id="rexistro" method="post" action="#" >
        <div class="CCabeceraFormulario">
            <span class="CDestacadoRegistro">
                {$user->firstname}{$user->lastname|string_format:" %s"}, {$user->name}
            </span>
        </div>
        
        <div class="CEntradasDAlta">            
            
            <table class="registro" style="clear: both;">
                <tbody>
                <tr>
                    <th>Nombre:</th>
                    <td colspan="2">
                        <input type="text" name="nombreDA" id="nombreDA" size="20" class="required validate-alpha"
                            value="{$user->name}" />
                    </td>
                    <td rowspan="3" valign="middle">
                        <img src="{gravatar email=$user->email size="80"}&d={$smarty.const.SITE_URL}{$params.IMAGE_DIR}envio_noticia/blank.gif"
                            alt="" style="vertical-align: middle" onerror="this.style.display='none';" />
                    </td>
                </tr>

                <tr>
                    <th>Primer apellido:</th>
                    <td colspan="2">
                        <input type="text" name="apellidoDA"  id="apellidoDA" size="30" class="required validate-alpha"
                            value="{$user->firstname}"/>
                    </td>
                </tr>
                    
                <tr>
                    <th>Segundo apellido:</th>
                    <td colspan="2">
                        <input type="text" name="segApellidoDA" id="segApellidoDA" size="30" style="border:1px solid #7f7f7f;"
                            value="{$user->lastname}" />
                    </td>
                </tr>                    

                <tr>
                    <th>DNI: <br /><sub>(99.999.999-A)</sub></th>
                    <td>
                        <input type="text" name="dniDA" id="dniDA" class="validate-pasaporte" size="15" maxlength="15" style="border:1px solid #7f7f7f;"
                                value="{$user->dni|default:""}" />
                    </td>
                
                    <th>Fecha de nacimiento: <br /><sub>(dd/mm/yyyy)</sub></th>
                    <td>
                        <input type="text" name="fechaNacimientoDA" id="fechaNacimientoDA" size="12" class="validate-date-au" style="border:1px solid #7f7f7f;"
                            value="{if isset($user->date_nac) && !preg_match('/^0000/', $user->date_nac) }{$user->date_nac|date_format:"%d/%m/%Y"}{/if}" />
                    </td>
                </tr>

                <tr>
                    <th>Teléfono móvil:  <br /><sub>(999-999-999)</sub></th>
                    <td><input type="text" name="movilDA" id="movilDA" {* class="validate-phone" *} size="15" style="border:1px solid #7f7f7f;"
                        value="{$user->movil}" /></td>
                
                    <th>Teléfono fijo:  <br /><sub>(999-999-999)</sub></th>
                    <td><input type="text" name="telefDA" id="telefDA" {* class="validate-phone" *} size="15" style="border:1px solid #7f7f7f;"
                        value="{$user->phone}" /></td>
                </tr>
                    
                <tr>
                    <th>País de residencia:</th>
                    <td><input type="text" name="paisDA" id="paisDA" class ="validate-alpha" style="border:1px solid #7f7f7f;"
                        value="{$user->country}"  /></td>
                
                    <th>Ciudad/población:</th>
                    <td><input type="text" name="poblacionDA" id="poblacionDA" class ="validate-alpha" style="border:1px solid #7f7f7f;"
                        value="{$user->city}"  /></td>
                </tr>

                <tr>
                    <td colspan="4" align="right">
                        <div style="clear: both; padding-top: 10px;"></div>
                        
                        <input type="image" src="{$params.IMAGE_DIR}envio_noticia/botonGuardar.gif" alt="Guardar"
                                style="cursor:pointer; float: right;" title="Actualizar su perfíl en Conect@" />
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
