<?php /* Smarty version 2.6.18, created on 2010-01-30 03:45:12
         compiled from conecta_CZonaVisionadoOpinion.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'conecta_CZonaVisionadoOpinion.tpl', 3, false),array('function', 'humandate', 'conecta_CZonaVisionadoOpinion.tpl', 11, false),)), $this); ?>
<div style="float:left;width:760px;margin-bottom:10px;">
    <div class="textoConectaXornal">Opini&oacute;n del Lector:
    <span style="font-size: 18px; font-weight: normal;"><?php echo ((is_array($_tmp=$this->_tpl_vars['opinionID'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</span>
    </div>
</div>
<div class="texto2FAQ">
    <?php $this->assign('id_author', $this->_tpl_vars['opinionID'][0]->fk_user); ?>
    <div class="CFirmaInfoMedia" style="color:#004B8D;">
         <div class="CTextoEnviadaPor"><img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticia/flecha_destacado.gif"/>
            Enviada por <b><?php echo $this->_tpl_vars['conecta_users'][$this->_tpl_vars['id_author']]->nick; ?>
</b></div>
         <div class="CNombreInfoMedia"><?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['opinionID'][0],'created' => $this->_tpl_vars['opinionID'][0]->created), $this);?>
</div>
    </div>
    <p style="clear:both; margin-top:20px;">
        <?php echo $this->_tpl_vars['opinionID'][0]->body; ?>

    </p>
    <br/>  
</div>

<div class="zonaClasificacionVideos">
    <div class="zonaPestanyasMedia">
        <!-- PESTANYA -->
        <div class="pestanyaSelecList">
            <div class="contInfoPestanyaGrande">
                <div class="flechaPestanyaSelecList"></div>
                <div class="textoPestanyaSelecList">
                    OTRAS OPINIONES
                </div>
            </div>
            <div class="cierrePestanyaSelecList"></div>
        </div>
    </div>
    <div class="listadoMedia" id="div_pc_<?php echo $this->_tpl_vars['accion']; ?>
">
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "conecta_Textos_listado.tpl", 'smarty_include_vars' => array('arraytextos' => $this->_tpl_vars['arrayopinions'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>

</div>