<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:47
         compiled from index_gente.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'index_gente.tpl', 11, false),array('modifier', 'escape', 'index_gente.tpl', 11, false),)), $this); ?>
<div class="CContenedorPiezaGenteXornal">
    <div class="CCabeceraPiezaGenteXornal"><a href="/seccion/sociedad/gente/"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
genteXornal/genteXornal.gif" alt="Gente xornal" /></a></div>
    <div class="CPiezaGenteXornal">
        <div class="CCuerpoPiezaGenteXornal">
            <div class="CContainerFotoPiezaGenteXornal">
                <a href="<?php echo $this->_tpl_vars['titular_gente']->permalink; ?>
"><img alt="<?php echo $this->_tpl_vars['titular_gente_img']->description; ?>
" src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['titular_gente_img']->path_file; ?>
<?php echo $this->_tpl_vars['titular_gente_img']->name; ?>
" width="252" height="250"/></a></div>
                <div class="CBandaAzulPiezaGenteXornal">
                <div class="CFlechaBlancaFondoAzul"></div>
                <div class="CTextoBandaAzulGenteXornal"><a href="<?php echo $this->_tpl_vars['titular_gente']->permalink; ?>
">Haz click para ver la informaci&oacute;n</a></div>
            </div>
            <div class="CPieFotoPiezaGenteXornal"><div class="CFlechaGrisPieGenteXornal"></div><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['titular_gente']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>
        </div>
    </div>
    <div class="CPiezaGenteXornalMasLinks"><a href="/seccion/sociedad/gente/">Gente de xornal</a></div>
</div>
        