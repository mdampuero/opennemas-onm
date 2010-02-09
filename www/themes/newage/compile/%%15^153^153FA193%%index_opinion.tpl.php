<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:47
         compiled from index_opinion.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'index_opinion.tpl', 11, false),array('modifier', 'clearslash', 'index_opinion.tpl', 11, false),)), $this); ?>
<div class="containerOpinion">
    <a href="/seccion/opinion/"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
opinion/logoOpinion.gif" alt="Opinion Xornal" /></a>
    <div class="listaPiezasOpinion">
        <div class="parPiezasOpinion">
          <div class="piezaOpinionPrimeraFila">
            <div class="cabeceraPiezaOpinion">Editoriales:</div>
            <div class="cuerpoPiezaOpinionPrimera">
                <div class="textoPiezaOpinionPrimera">
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
"><?php echo ((is_array($_tmp=$this->_tpl_vars['editorial'][$this->_sections['ac']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                    <br /><br />
                    <?php endfor; endif; ?>
                </div>
            </div>
            </div>
            <div class="separadorVerticalDirectorOpinion"></div>
            <div class="piezaOpinionPrimeraFila">
                <div class="cabeceraPiezaOpinion"><a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/2/<?php echo ((is_array($_tmp=$this->_tpl_vars['cartadirector']->name)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
.html"><?php echo $this->_tpl_vars['cartadirector']->name; ?>
</a><br/>Director</div>
                <div class="cuerpoPiezaOpinion">
                    <div class="fotoPiezaOpinion">
                     <?php if ($this->_tpl_vars['cartadirector']->foto): ?>
                        <img src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['cartadirector']->foto; ?>
" alt="<?php echo $this->_tpl_vars['cartadirector']->name; ?>
" height="70"/>
                      <?php else: ?> <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
opinion/fondoFraseOpinion.gif" alt="<?php echo $this->_tpl_vars['cartadirector']->name; ?>
"/>
                      <?php endif; ?>
                    </div>
                    <div class="textoPiezaOpinion"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
flechitaMenu.gif" alt=""/>
                        <a href="<?php echo ((is_array($_tmp=@$this->_tpl_vars['cartadirector']->permalink)) ? $this->_run_mod_handler('default', true, $_tmp, "#") : smarty_modifier_default($_tmp, "#")); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['cartadirector']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
                </div>
            </div>
            <div class="separadorHorizontalOpinion"></div>
        </div>
        <div class="parPiezasOpinion">
          <?php unset($this->_sections['ac']);
$this->_sections['ac']['name'] = 'ac';
$this->_sections['ac']['loop'] = is_array($_loop=$this->_tpl_vars['opiniones']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
          <div class="piezaOpinion">
                        <div class="cuerpoPiezaOpinion">
              <div class="fotoPiezaOpinion">
                  <?php if ($this->_tpl_vars['opiniones'][$this->_sections['ac']['index']]->photo): ?> 
                    <img style="padding-right:4px;" align="left" src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['opiniones'][$this->_sections['ac']['index']]->photo; ?>
" alt="<?php echo $this->_tpl_vars['opiniones'][$this->_sections['ac']['index']]->name; ?>
"/>
                  <?php endif; ?>
                <a class="contSeccionListadoPortadaPCAuthor" href="/opinions/opinions_do_autor/<?php echo $this->_tpl_vars['opiniones'][$this->_sections['ac']['index']]->fk_author; ?>
/<?php echo ((is_array($_tmp=$this->_tpl_vars['opiniones'][$this->_sections['ac']['index']]->name)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
.html"> <?php echo $this->_tpl_vars['opiniones'][$this->_sections['ac']['index']]->name; ?>
</a>
              </div>
              <div class="textoPiezaOpinion"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
flechitaMenu.gif" alt=""/>
                <a href="<?php echo ((is_array($_tmp=@$this->_tpl_vars['opiniones'][$this->_sections['ac']['index']]->permalink)) ? $this->_run_mod_handler('default', true, $_tmp, "#") : smarty_modifier_default($_tmp, "#")); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['opiniones'][$this->_sections['ac']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
            </div>
          </div>
            <?php if ($this->_sections['ac']['index'] % 2 != 0): ?>
            <div class="separadorHorizontalOpinion"></div>
            <?php else: ?><div class="separadorVerticalOpinion"></div>
            <?php endif; ?>
          <?php endfor; endif; ?>
        </div>
    </div>
</div>