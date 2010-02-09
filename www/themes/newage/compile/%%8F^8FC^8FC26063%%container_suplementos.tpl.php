<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:46
         compiled from container_suplementos.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'container_suplementos.tpl', 15, false),array('modifier', 'clearslash', 'container_suplementos.tpl', 15, false),)), $this); ?>
<a href="/seccion/suplementos/">
    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
logos/suplementos_xornal.gif" alt="Contexto" style="margin-top: 20px; margin-bottom: 5px;margin-left: 5px;" />
</a>
<div class="separadorHorizontal"></div>
<div style="display:inline;float:left;position:relative;">
    <?php if (isset ( $this->_tpl_vars['frontpage_contexto_img'] ) && preg_match ( '/\.jpg$/' , $this->_tpl_vars['frontpage_contexto_img'] )): ?>
    <div style="margin-right:5px;text-align: center;">
        <a href="/seccion/suplementos/contexto/" title="Contexto">
            <img src="/media/images/kiosko/<?php echo $this->_tpl_vars['frontpage_contexto_img']; ?>
" border="0" alt="Contexto Frontpage"/>
        </a>
    </div>
    <?php endif; ?>
    <div class="CContendorSuplementos">
        <img alt="" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif"/>
        <a style="color:#004B8E;" href="<?php echo ((is_array($_tmp=@$this->_tpl_vars['titulares_contexto']->permalink)) ? $this->_run_mod_handler('default', true, $_tmp, "#") : smarty_modifier_default($_tmp, "#")); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_contexto']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
    </div>
</div>
<div class="fileteFotoVideoDia"></div>
<div style="margin-top:10px;display:inline;float:left;position:relative;">
    <?php if (isset ( $this->_tpl_vars['frontpage_estratexias_img'] ) && preg_match ( '/\.jpg$/' , $this->_tpl_vars['frontpage_estratexias_img'] )): ?>
    <div style="margin-right:5px;text-align: center;">
        <a href="/seccion/suplementos/estratexias/" title="Estratexias">
            <img src="/media/images/kiosko/<?php echo $this->_tpl_vars['frontpage_estratexias_img']; ?>
" border="0" alt="Estratexias Frontpage"/>
        </a>
    </div>
    <?php endif; ?>
    <div class="CContendorSuplementos">
        <img alt="" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif"/>
        <a style="color:#004B8E;" href="<?php echo ((is_array($_tmp=@$this->_tpl_vars['titulares_estratexias']->permalink)) ? $this->_run_mod_handler('default', true, $_tmp, "#") : smarty_modifier_default($_tmp, "#")); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_estratexias']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
    </div>
</div>