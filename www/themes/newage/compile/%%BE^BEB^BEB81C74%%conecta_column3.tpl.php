<?php /* Smarty version 2.6.18, created on 2010-01-26 01:11:30
         compiled from conecta_column3.tpl */ ?>
<?php echo '
<style type="text/css">
.usuario-conecta {
    width: 180px; 
}

.usuario-conecta ul {
    list-style: disc url('; ?>
<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
<?php echo 'fotoVideoDia/flechitaAzul.gif) inside;
    margin-left: 10px;
    width: 163px;
}

.usuario-conecta li {
    margin-bottom: 2px;
    padding: 4px 0;
    /* border-bottom: 1px dashed #A0BCD4; */
    background-image: url('; ?>
<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
<?php echo 'fotoVideoDia/fileteDashedDeportesXPress.gif);
    background-position: bottom left;
    background-repeat: no-repeat;
}

.usuario-conecta ul a {
    font-size: 14px;
    color: #004B8E;
}

.usuario-conecta .cabeceraConectaUsuario {
    margin-top: 8px;
    text-align: left;
    font-size: 12px;
}

.usuario-conecta .logoutContainer {
    margin-top: 10px;
    text-align: right;
}

.usuario-conecta .logoutContainer a {
    color: #004B8E;
}
</style>
'; ?>


<div class="column3">
    
    <?php if (isset ( $_SESSION['pc_user'] )): ?>
        <div class="usuario-conecta">
            
            <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
planConecta/header_planconectafondo.gif" border="0" />                
            
            <p class="cabeceraConectaUsuario">
                Sesión iniciada con nick: <a href="/conecta/perfil/" title="Ver mi perfíl"><?php echo $_SESSION['nameuser']; ?>
</a>
            </p>
            
            <ul>                    
                <li><a href="/conecta/perfil/" title="Ver Perfíl">Editar perfíl</a></li>
                 <li><a href="/conecta/envio/" title="Participar">Partipar en Conect@</a></li>
                                <li><a href="/conecta/cambio/" title="Cambiar contraseña">Cambiar contraseña</a></li>
                <li><a href="/conecta/boletin/" title="Suscripción boletín">Suscripción boletín</a></li>
            </ul>
            
            <div class="logoutContainer">[ <a href="/conecta/logout/" title="Salir de Conect@">Cerrar Sesión</a> ]</div>
            
                                    
        </div>
        
        <!-- SEPARADOR HORIZONTAL -->
        <div class="separadorHorizontal"></div>
    <?php endif; ?>
    
    <!-- ****************** FOTO/VIDEO DIA **************** -->
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_column3_containerFotoVideoDiaMasListado.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>