<?php /* Smarty version 2.6.18, created on 2010-01-27 05:25:52
         compiled from opinion_author_opinions.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'opinion_author_opinions.tpl', 24, false),array('modifier', 'date_format', 'opinion_author_opinions.tpl', 71, false),array('modifier', 'truncate', 'opinion_author_opinions.tpl', 74, false),array('modifier', 'strip_tags', 'opinion_author_opinions.tpl', 74, false),)), $this); ?>
<div class="CContainerCabeceraOpinion">
    <div class="CContainerFotoComentarista">
        <?php if ($this->_tpl_vars['opinions'][0]['path_img']): ?>
             <img src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['opinions'][0]['path_img']; ?>
" alt="<?php echo $this->_tpl_vars['opinions'][0]['name']; ?>
" width="112"/>
        <?php else: ?>
            <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
opinion/editorial.jpg" alt="<?php echo $this->_tpl_vars['opinions'][0]['name']; ?>
" width="112"/>
        <?php endif; ?>
    </div>
    <div class="CContainerDatosYTitularCabOpinion">
        <div class="CDatosCabOpinion"> 
            <?php if ($this->_tpl_vars['author_id'] != 1): ?>
                                <div class="CNombreCabOpinion"> </div>
                <div class="CSeparadorVAzulCabOpinion"></div>
            <?php else: ?>
                <div class="CNombreCabOpinion">  <a class="CNombreCabOpinionLink" href="/opinions/opinions_do_autor/1/Editorial.html">Editorial</a> </div>
                <div class="CSeparadorVAzulCabOpinion"></div>
            <?php endif; ?>
            <div class="CRolCabOpinion"><?php echo $this->_tpl_vars['opinions'][0]['condition']; ?>
</div>
        </div>

        <div class="CTitularCabOpinion">
            <?php if ($this->_tpl_vars['author_id'] != 1): ?>
                <h2>  <a class="CNombreAuthorLink" href="/opinions/opinions_do_autor/<?php echo $this->_tpl_vars['opinions'][0]['pk_author']; ?>
/<?php echo ((is_array($_tmp=$this->_tpl_vars['opinions'][0]['name'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
.html"><?php echo $this->_tpl_vars['opinions'][0]['name']; ?>
</a> </h2>
            <?php else: ?>
                  <h2>  <a class="CNombreAuthorLink" href="/opinions/opinions_do_autor/1/Editorial.html">Editorial</a> </h2>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="divListadoTitlesAuthor">
    <?php unset($this->_sections['ac']);
$this->_sections['ac']['name'] = 'ac';
$this->_sections['ac']['loop'] = is_array($_loop=$this->_tpl_vars['opinions']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['ac']['show'] = true;
$this->_sections['ac']['max'] = $this->_sections['ac']['loop'];
$this->_sections['ac']['step'] = 1;
$this->_sections['ac']['start'] = $this->_sections['ac']['step'] > 0 ? 0 : $this->_sections['ac']['loop']-1;
if ($this->_sections['ac']['show']) {
    $this->_sections['ac']['total'] = $this->_sections['ac']['loop'];
    if ($this->_sections['ac']['total'] == 0)
        $this->_sections['ac']['show'] = false;
} else
    $this->_sections['ac']['total'] = 0;
if ($this->_sections['ac']['show']):

            for ($this->_sections['ac']['index'] = $this->_sections['ac']['start'], $this->_sections['ac']['iteration'] = 1;
                 $this->_sections['ac']['iteration'] <= $this->_sections['ac']['total'];
                 $this->_sections['ac']['index'] += $this->_sections['ac']['step'], $this->_sections['ac']['iteration']++):
$this->_sections['ac']['rownum'] = $this->_sections['ac']['iteration'];
$this->_sections['ac']['index_prev'] = $this->_sections['ac']['index'] - $this->_sections['ac']['step'];
$this->_sections['ac']['index_next'] = $this->_sections['ac']['index'] + $this->_sections['ac']['step'];
$this->_sections['ac']['first']      = ($this->_sections['ac']['iteration'] == 1);
$this->_sections['ac']['last']       = ($this->_sections['ac']['iteration'] == $this->_sections['ac']['total']);
?>
        <div class="ListadoTitlesAuthor">
            <div class="flechitaTextoPC">
                <img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
planConecta/flechitaTexto.gif"/>
            </div>
            <a class="CNombreAuthorLink" href="<?php echo $this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
            <div class="CFechaAuthorlist">
                <?php echo ((is_array($_tmp=$this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%y %H:%M") : smarty_modifier_date_format($_tmp, "%d/%m/%y %H:%M")); ?>

            </div>
             <div class="CtextoAuthorlist">
                <?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['body'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 250) : smarty_modifier_truncate($_tmp, 250)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
<a class="CAutorSigue" href="<?php echo $this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['permalink']; ?>
"> &raquo;Sigue </a>
            </div>
        </div>

    <?php endfor; endif; ?>
    <p align="center"><?php echo $this->_tpl_vars['pagination_list']->links; ?>
</p>
</div>