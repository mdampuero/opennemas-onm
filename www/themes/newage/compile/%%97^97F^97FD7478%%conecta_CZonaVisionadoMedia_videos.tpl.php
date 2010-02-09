<?php /* Smarty version 2.6.18, created on 2010-01-26 22:25:32
         compiled from conecta_CZonaVisionadoMedia_videos.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'conecta_CZonaVisionadoMedia_videos.tpl', 12, false),array('modifier', 'clearslash', 'conecta_CZonaVisionadoMedia_videos.tpl', 14, false),array('function', 'humandate', 'conecta_CZonaVisionadoMedia_videos.tpl', 19, false),)), $this); ?>
<div class="CZonaVisionadoMedia">
    <div class="CVisorMedia">  
        <object width="425" height="344">
        <param name="movie" value="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videoID'][0]->code; ?>
&amp;hl=es&amp;fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
        <embed src="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videoID'][0]->code; ?>
&amp;hl=es&amp;fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object>
    </div>
    <div class="CMarcoVisorInfoMedia">
        <div class="CVisorInfoMedia">
            <div class="CContainerInfoMedia">
                <div class="CContainerSeccionFechaInfoMedia">
                    <div class="CSeccionInfoMedia"><?php echo $this->_tpl_vars['videoID'][0]->category_name; ?>
</div>
                    <div class="CFechaInfoMedia"><?php echo ((is_array($_tmp=$this->_tpl_vars['videoID'][0]->created)) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%y") : smarty_modifier_date_format($_tmp, "%d/%m/%y")); ?>
</div>
                </div>
                <div class="CTitularInfoMedia"><?php echo ((is_array($_tmp=$this->_tpl_vars['videoID'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
                <div class="separadorHorizontal"></div>
                <?php $this->assign('id_author', $this->_tpl_vars['videoID'][0]->fk_user); ?>
                <div class="CFirmaInfoMedia">
                     <div class="CTextoEnviadaPor">Enviada por <b><?php echo $this->_tpl_vars['conecta_users'][$this->_tpl_vars['id_author']]->nick; ?>
 </b> </div>
                     <div class="CNombreInfoMedia"> <?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['videoID'][0],'created' => $this->_tpl_vars['videoID'][0]->created), $this);?>
</div>
                </div>
                <div class="CTextoInfoMedia">
                    <div class="CFlechitaTexto"></div>
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['videoID'][0]->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>

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
                <div class="textoPestanyaSelecList">VIDEOS PASADOS</div>
            </div>
            <div class="cierrePestanyaSelecList"></div>
        </div>
    </div>
    <div class="listadoMedia" id="div_pc_<?php echo $this->_tpl_vars['accion']; ?>
">
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "conecta_Videos_listado.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>
    
</div>