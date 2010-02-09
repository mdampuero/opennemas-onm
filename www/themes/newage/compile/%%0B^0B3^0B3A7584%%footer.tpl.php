<?php /* Smarty version 2.6.18, created on 2010-01-30 05:51:22
         compiled from mobile/footer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'rendermenu', 'mobile/footer.tpl', 8, false),)), $this); ?>
	
	</div> 
	<div id="pie">
		<div id="secciones">
			Secciones Xornal.com:<br />
            
            <?php echo smarty_function_rendermenu(array('ccm' => $this->_tpl_vars['ccm'],'section' => $this->_tpl_vars['section']), $this);?>
            
		</div>
	</div>
    
    <div id="www">
        <a href="/mobile/redirect_web/" title="Ver Xornal.com">ver web</a>
    </div>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mobile/modulo_analytics.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </body>
</html>