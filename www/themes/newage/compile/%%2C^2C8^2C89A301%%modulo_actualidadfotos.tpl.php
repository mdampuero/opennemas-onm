<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:46
         compiled from modulo_actualidadfotos.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'modulo_actualidadfotos.tpl', 7, false),)), $this); ?>
<div class="actualidadFotos">
    <div class="cabeceraActualidadFotos"><a href="<?php echo $this->_tpl_vars['lastAlbumContent']->permalink; ?>
"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
actualidadVideosFotos/logoActualidadFotos.gif" alt="Actualidad Fotos"></a></div>
    <div class="zonaVisualizacionFotos">
        <div class="CPiezaActualidadFotosHome">
            <div class="CCuerpoPiezaFotoXornal">
                <div class="CContainerFotoActualidadFotos">
                    <a title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lastAlbumContent']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" href="<?php echo $this->_tpl_vars['lastAlbumContent']->permalink; ?>
">
                        <img style="height: 250px;" alt=" <?php echo ((is_array($_tmp=$this->_tpl_vars['lastAlbumContent']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
album/crops/<?php echo $this->_tpl_vars['lastAlbumContent']->id; ?>
.jpg"/>
                    </a>
                </div>
                <div class="CPieFotoPiezaFotoXornal">
                    <div class="CFlechaGrisPieGenteXornal"></div>
                        <a title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lastAlbumContent']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" href="<?php echo $this->_tpl_vars['lastAlbumContent']->permalink; ?>
">
                            <?php echo ((is_array($_tmp=$this->_tpl_vars['lastAlbumContent']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>

                        </a>
                </div>
            </div>
        </div>
    </div>
    <div class="linkMasMedia"><a href="<?php echo $this->_tpl_vars['lastAlbumContent']->permalink; ?>
">+ Foto galerías</a></div>
</div>