<?php /* Smarty version 2.6.18, created on 2010-01-21 09:37:52
         compiled from conecta_CZonaVisionadoMedia_photos.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'conecta_CZonaVisionadoMedia_photos.tpl', 4, false),array('modifier', 'escape', 'conecta_CZonaVisionadoMedia_photos.tpl', 4, false),array('modifier', 'upper', 'conecta_CZonaVisionadoMedia_photos.tpl', 4, false),array('function', 'humandate', 'conecta_CZonaVisionadoMedia_photos.tpl', 18, false),)), $this); ?>
<div class="CZonaVisionadoMedia">
    <?php $this->assign('id_author', $this->_tpl_vars['photoID'][0]->fk_user); ?>
    <div class="CVisorMedia">        
         <a href="<?php echo $this->_tpl_vars['MEDIA_CONECTA_WEB']; ?>
<?php echo $this->_tpl_vars['photoID'][0]->path_file; ?>
" class="lightwindow" rel='xornal[album]' title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['photoID'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" caption="<?php echo ((is_array($_tmp=$this->_tpl_vars['accion'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
" author="<?php echo $this->_tpl_vars['conecta_users'][$this->_tpl_vars['id_author']]->nick; ?>
">
            <img style="max-width:434px;max-height:320px;" src="<?php echo $this->_tpl_vars['MEDIA_CONECTA_WEB']; ?>
<?php echo $this->_tpl_vars['photoID'][0]->path_file; ?>
">
         </a>
    </div>
    <div class="CMarcoVisorInfoMedia">
        <div class="CVisorInfoMedia">
            <div class="CContainerInfoMedia">
                <div class="CContainerSeccionFechaInfoMedia">
                    <div class="CSeccionInfoMedia"><?php echo $this->_tpl_vars['accion']; ?>
</div>
                </div>
                <div class="CTitularInfoMedia"><?php echo ((is_array($_tmp=$this->_tpl_vars['photoID'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
                <div class="separadorHorizontal"></div>
                <div class="CFirmaInfoMedia">                                       
                     <div class="CTextoEnviadaPor">Enviada por <b><?php echo $this->_tpl_vars['conecta_users'][$this->_tpl_vars['id_author']]->nick; ?>
 </b> </div>
                     <div class="CNombreInfoMedia"> <?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['photoID'][0],'created' => $this->_tpl_vars['photoID'][0]->created), $this);?>
</div>
                </div>
                <br />
                <div class="CTextoInfoMedia">
                    <div class="CFlechitaTexto"></div>
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['photoID'][0]->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="zonaClasificacionVideos">
    <div class="zonaPestanyasMedia">
        <!-- PESTANYA -->
        <div class="pestanyaSelecList">
            <div class="contInfoPestanyaGrande">
                <div class="flechaPestanyaSelecList"></div>
                <div class="textoPestanyaSelecList">
                   FOTOGRAFÍAS PASADAS
                </div>
            </div>
            <div class="cierrePestanyaSelecList"></div>
        </div>
    </div>
    <div class="listadoMedia" id="div_pc_<?php echo $this->_tpl_vars['accion']; ?>
">
         <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "conecta_Fotos_listado.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>
   
</div>