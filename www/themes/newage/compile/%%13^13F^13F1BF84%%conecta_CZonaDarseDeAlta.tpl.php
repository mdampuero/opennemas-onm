<?php /* Smarty version 2.6.18, created on 2010-01-27 11:26:24
         compiled from conecta_CZonaDarseDeAlta.tpl */ ?>
<div class="CZonaRegistrarse">
    <?php if (isset ( $this->_tpl_vars['message'] )): ?>
       <div style="float:right;border:1px;background-color:#AAA;padding:10px;">
        <h4><?php echo $this->_tpl_vars['message']; ?>
</h4>
        <ul style="list-style: circle; float: none; margin-left: 20px;">
         <?php unset($this->_sections['errs']);
$this->_sections['errs']['name'] = 'errs';
$this->_sections['errs']['loop'] = is_array($_loop=$this->_tpl_vars['errors']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['errs']['show'] = true;
$this->_sections['errs']['max'] = $this->_sections['errs']['loop'];
$this->_sections['errs']['step'] = 1;
$this->_sections['errs']['start'] = $this->_sections['errs']['step'] > 0 ? 0 : $this->_sections['errs']['loop']-1;
if ($this->_sections['errs']['show']) {
    $this->_sections['errs']['total'] = $this->_sections['errs']['loop'];
    if ($this->_sections['errs']['total'] == 0)
        $this->_sections['errs']['show'] = false;
} else
    $this->_sections['errs']['total'] = 0;
if ($this->_sections['errs']['show']):

            for ($this->_sections['errs']['index'] = $this->_sections['errs']['start'], $this->_sections['errs']['iteration'] = 1;
                 $this->_sections['errs']['iteration'] <= $this->_sections['errs']['total'];
                 $this->_sections['errs']['index'] += $this->_sections['errs']['step'], $this->_sections['errs']['iteration']++):
$this->_sections['errs']['rownum'] = $this->_sections['errs']['iteration'];
$this->_sections['errs']['index_prev'] = $this->_sections['errs']['index'] - $this->_sections['errs']['step'];
$this->_sections['errs']['index_next'] = $this->_sections['errs']['index'] + $this->_sections['errs']['step'];
$this->_sections['errs']['first']      = ($this->_sections['errs']['iteration'] == 1);
$this->_sections['errs']['last']       = ($this->_sections['errs']['iteration'] == $this->_sections['errs']['total']);
?>
         <li><?php echo $this->_tpl_vars['errors'][$this->_sections['errs']['index']]; ?>
</li>
         <?php endfor; endif; ?>
        </ul>    
       </div>
    <?php endif; ?>
    
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
                <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticia/flecha_destacado.gif" alt="imagen"/>
                Registro para darse de alta
            </span>
        </div>
        
        <div class="CEntradasDAlta">
            
            <table class="registro" style="clear: both;">
                <tr>                    
                    <th>Nick:</th>
                    <td>
                        <input type="text" name="nickDA" id="nickDA" class="required validate-alpha validate-min-length check-nick"
                            value="<?php echo $_REQUEST['nickDA']; ?>
" />
                    </td>
                        
                    <th>Correo electrónico:<a href="#aviso"><sup>(*)</sup></a></th>
                    <td>
                        <input type="text" name="emailDA"  id="emailDA" class="required validate-email check-email"
                            value="<?php echo $_REQUEST['emailDA']; ?>
"/>
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
                            value="<?php echo $_REQUEST['nombreDA']; ?>
" />
                    </td>
                </tr>

                <tr>
                    <th>Primer apellido:</th>
                    <td colspan="3">
                        <input type="text" name="apellidoDA"  id="apellidoDA" size="40" class="required validate-alpha"
                            value="<?php echo $_REQUEST['apellidoDA']; ?>
"/>
                    </td>
                </tr>
                    
                <tr>
                    <th>Segundo apellido:</th>
                    <td colspan="3">
                        <input type="text" name="segApellidoDA" id="segApellidoDA" size="40" style="border:1px solid #7f7f7f;"
                            value="<?php echo $_REQUEST['segApellidoDA']; ?>
" />
                    </td>
                </tr>                    

                <tr>
                    <th>DNI o Pasaporte: <br /><sub>(DNI:99999999L, NIE:X9999999L)</sub></th>
                    <td>
                        <input type="text" name="dniDA" id="dniDA" class="validate-pasaporte" size="15" maxlength="15" style="border:1px solid #7f7f7f;"
                                value="<?php echo $_REQUEST['dniDA']; ?>
" />
                    </td>
                
                    <th>Fecha de nacimiento: <br /><sub>(dd/mm/yyyy)</sub></th>
                    <td>
                        <input type="text" name="fechaNacimientoDA" id="fechaNacimientoDA" size="12" maxlength="10"
                            class="validate-date-au" style="border:1px solid #7f7f7f;"
                            value="<?php echo $_REQUEST['fechaNacimientoDA']; ?>
" />
                    </td>
                </tr>

                <tr>
                    <th>Teléfono móvil: <br /><sub>(999-999-999)</sub></th>
                    <td><input type="text" name="movilDA" id="movilDA"  size="14" style="border:1px solid #7f7f7f;"
                        value="<?php echo $_REQUEST['movilDA']; ?>
" /></td>
                
                    <th>Teléfono fijo: <br /><sub>(999-999-999)</sub></th>
                    <td><input type="text" name="telefDA" id="telefDA"  size="14" style="border:1px solid #7f7f7f;"
                        value="<?php echo $_REQUEST['telefDA']; ?>
" /></td>
                </tr>
                    
                <tr>
                    <th>País de residencia:</th>
                    <td><input type="text" name="paisDA" id="paisDA" class ="validate-alpha" style="border:1px solid #7f7f7f;"
                        value="<?php echo $_REQUEST['paisDA']; ?>
"  /></td>
                
                    <th>Ciudad/población:</th>
                    <td><input type="text" name="poblacionDA" id="poblacionDA" class ="validate-alpha" style="border:1px solid #7f7f7f;"
                        value="<?php echo $_REQUEST['poblacionDA']; ?>
"  /></td>
                </tr>

                <tr>
                    <td colspan="4" align="right">
                        <div style="clear: both;"></div>
                        
                        <input type="image" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
envio_noticia/botonRegistrar.gif"
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
                                Acepto las condiciones de uso de este <a href="<?php echo @SITE_URL; ?>
" title="Xornal.com">portal</a>
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

<script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
registro-validations.js"></script>
<script language="javascript" type="text/javascript">
/*<![CDATA[*/         
    new Validation('rexistro');  
/*]]>*/
</script>
<noscript>Para realizar el registro en Conect@ es necesario tener un navegador con soporte javascript habilitado. Disculpe las molestias.</noscript>
