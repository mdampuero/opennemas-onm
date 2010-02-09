<?php /* Smarty version 2.6.18, created on 2010-01-25 21:38:24
         compiled from opinion_index_content.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'opinion_index_content.tpl', 15, false),array('modifier', 'clearslash', 'opinion_index_content.tpl', 15, false),array('modifier', 'date_format', 'opinion_index_content.tpl', 30, false),)), $this); ?>
<div class="zonaClasificacionContenidosPortadaPC">
    <?php if (( $_REQUEST['action'] == 'read' ) && ( ! empty ( $this->_tpl_vars['editorial'] ) )): ?>
    <div class="listadoEnlacesPlanConecta">
        <div class="filaPortadaPC">
            <div class="elementoListadoMediaPagPortadaPC">
                <div class="fotoElemOpinion"></div>
                <div class="contSeccionFechaListadoOpinion">
                    <div class="seccionMediaListado"><a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/1/Editorial.html">Editoriales:</a></div>
                    <div class="fechaMediaListado"></div>
                </div>
                <div class="contTextoElemMediaListadoOpinion">
                    <div class="textoElemMediaListadoPortadaPC">
                      <?php unset($this->_sections['ac']);
$this->_sections['ac']['name'] = 'ac';
$this->_sections['ac']['loop'] = is_array($_loop=$this->_tpl_vars['editorial']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	                        <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
flechitaMenu.gif" alt=""/> 
			                 <a href="<?php echo ((is_array($_tmp=@$this->_tpl_vars['editorial'][$this->_sections['ac']['index']]->permalink)) ? $this->_run_mod_handler('default', true, $_tmp, "#") : smarty_modifier_default($_tmp, "#")); ?>
" ><?php echo ((is_array($_tmp=$this->_tpl_vars['editorial'][$this->_sections['ac']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a> <br />		                
		               <?php endfor; endif; ?>                        
                    </div>
                </div>
            </div>
            <div class="fileteVerticalIntraMedia"></div>
            <div class="elementoListadoMediaPagPortadaPC">
                <div class="fotoElemOpinion">
                    <?php if ($this->_tpl_vars['dir']['photo']): ?>
                    <img src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['dir']['photo']; ?>
" alt="<?php echo $this->_tpl_vars['dir']['name']; ?>
"/>
                    <?php else: ?><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
opinion/editorial.jpg" alt="<?php echo $this->_tpl_vars['dir']['name']; ?>
"/>
                    <?php endif; ?>
                </div>
                <div class="contSeccionFechaListadoOpinion">
                    <div class="seccionMediaListado"><a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/2/<?php echo ((is_array($_tmp=$this->_tpl_vars['dir']['name'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
.html"> <?php echo $this->_tpl_vars['dir']['name']; ?>
</a>, director</div>
                    <div class="fechaMediaListado"><?php echo ((is_array($_tmp=$this->_tpl_vars['director']->created)) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%y") : smarty_modifier_date_format($_tmp, "%d/%m/%y")); ?>
</div>
                </div>
                <div class="contTextoElemMediaListadoOpinion">
                    <div class="textoElemMediaListadoPortadaPC">
                        <div class="flechitaTextoPC">
                          <img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
planConecta/flechitaTexto.gif"/>
                        </div>
                        <a href="<?php echo $this->_tpl_vars['director']->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['director']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="fileteHorizontalPC"></div>
    </div>
    <?php endif; ?>
    <div class="listadoEnlacesPlanConecta">
        <?php unset($this->_sections['ac']);
$this->_sections['ac']['name'] = 'ac';
$this->_sections['ac']['loop'] = is_array($_loop=$this->_tpl_vars['opinions']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['ac']['start'] = (int)0;
$this->_sections['ac']['show'] = true;
$this->_sections['ac']['max'] = $this->_sections['ac']['loop'];
$this->_sections['ac']['step'] = 1;
if ($this->_sections['ac']['start'] < 0)
    $this->_sections['ac']['start'] = max($this->_sections['ac']['step'] > 0 ? 0 : -1, $this->_sections['ac']['loop'] + $this->_sections['ac']['start']);
else
    $this->_sections['ac']['start'] = min($this->_sections['ac']['start'], $this->_sections['ac']['step'] > 0 ? $this->_sections['ac']['loop'] : $this->_sections['ac']['loop']-1);
if ($this->_sections['ac']['show']) {
    $this->_sections['ac']['total'] = min(ceil(($this->_sections['ac']['step'] > 0 ? $this->_sections['ac']['loop'] - $this->_sections['ac']['start'] : $this->_sections['ac']['start']+1)/abs($this->_sections['ac']['step'])), $this->_sections['ac']['max']);
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
            <?php if ($this->_sections['ac']['index'] % 2 == 0): ?>
            <div class="filaPortadaPC">
            <?php endif; ?>
            <div class="elementoListadoMediaPagPortadaPC">
                <div class="fotoElemOpinion">
                    <?php if ($this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['path_img']): ?>
                    <img src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['path_img']; ?>
" alt="<?php echo $this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['name']; ?>
"/>
                    <?php else: ?><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
opinion/editorial.jpg" alt="<?php echo $this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['name']; ?>
"/>
                    <?php endif; ?>
                </div>
                <div class="contSeccionFechaListadoOpinion">
                    <div class="seccionMediaListado"><a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/<?php echo $this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['pk_author']; ?>
/<?php echo ((is_array($_tmp=$this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['name'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
.html"><?php echo $this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['name']; ?>
</a></div>
                    <div class="fechaMediaListado"><?php echo ((is_array($_tmp=$this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%y") : smarty_modifier_date_format($_tmp, "%d/%m/%y")); ?>
</div>
                </div>
                <div class="contTextoElemMediaListadoOpinion">
                    <div class="textoElemMediaListadoPortadaPC">
                        <div class="flechitaTextoPC">
                          <img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
planConecta/flechitaTexto.gif"/>
                        </div>
                        <a href="<?php echo $this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['opinions'][$this->_sections['ac']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                    </div>
                </div>
            </div>
            <?php if ($this->_sections['ac']['index'] % 2 == 0): ?>
                <div class="fileteVerticalIntraMedia"></div>
            <?php endif; ?>
            <?php if ($this->_sections['ac']['index'] % 2 != 0 || $this->_sections['ac']['last']): ?>
                </div>
                <div class="fileteHorizontalPC"></div>
            <?php endif; ?>
        <?php endfor; endif; ?>
       
    </div>
    <?php if (count ( $this->_tpl_vars['opinions'] ) > 0): ?>			  
	   <p align="center"><?php echo $this->_tpl_vars['pagination']; ?>
</p>
	   <br>
	    <p align="center"><?php echo $this->_tpl_vars['paginate']->links; ?>
</p>
	<?php endif; ?>
</div>