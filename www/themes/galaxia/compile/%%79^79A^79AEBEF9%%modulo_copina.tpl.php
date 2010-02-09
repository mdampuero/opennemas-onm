<?php /* Smarty version 2.6.18, created on 2010-01-14 02:29:11
         compiled from modulo_copina.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'comments', 'modulo_copina.tpl', 7, false),)), $this); ?>
<div id="COpina" class="COpina">
	<div class="CContenedorOpina">
        <div class="CCabeceraOpina"></div>
        <div class="CComentarios">
            <div class="CContenedorComentarios">
            <?php if (preg_match ( '/opinion\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'comments', 'id' => $this->_tpl_vars['opinion']->id)), $this); ?>

            <?php elseif (preg_match ( '/article\.php/' , $_SERVER['SCRIPT_NAME'] ) || ( preg_match ( '/preview_content\.php/' , $_SERVER['SCRIPT_NAME'] ) || ( $_REQUEST['action'] == 'article' ) )): ?>
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'comments', 'id' => $this->_tpl_vars['article']->id)), $this); ?>

            <?php else: ?>
              
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'comments', 'id' => $this->_tpl_vars['item']->id, 'where' => 'pc')), $this); ?>

            <?php endif; ?>
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
                    <?php if (! isset ( $_SESSION['pc_user'] )): ?>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "boxAuth/default.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    <?php else: ?>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "boxAuth/conecta.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    <?php endif; ?>                                        
                </div>
                
                <?php if (preg_match ( '/article\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
                    <input type="hidden" id="id" name="id" value="<?php echo $this->_tpl_vars['article']->id; ?>
"/>
                    <input type="hidden" id="category" name="category" value="<?php echo $this->_tpl_vars['article']->category; ?>
"/>
                <?php elseif (preg_match ( '/opinion\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
                    <input type="hidden" id="id" name="id" value="<?php echo $this->_tpl_vars['opinion']->id; ?>
"/>
                    <input type="hidden" id="category" name="category" value="4"/>
                <?php elseif (preg_match ( '/planconecta\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
                    <input type="hidden" id="id" name="id" value="<?php echo $this->_tpl_vars['item']->id; ?>
"/>
                    <input type="hidden" id="category" name="category" value="9"/>
                    <input type="hidden" id="where" name="where" value="pc"/>
                <?php endif; ?>
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
<?php echo '
<script type="text/javascript">
document.observe(\'dom:loaded\', function() {
    CommentForm = new CommentFormClass({box: \'authContainer\', fbApiKey: \''; ?>
<?php echo @FB_APP_APIKEY; ?>
<?php echo '\'});
    CommentForm.initFb(); // FB.init        
});
</script>
'; ?>
