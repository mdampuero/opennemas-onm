<?php /* Smarty version 2.6.18, created on 2010-01-14 01:41:18
         compiled from modulo_carousel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'modulo_carousel.tpl', 7, false),array('modifier', 'escape', 'modulo_carousel.tpl', 7, false),array('modifier', 'truncate', 'modulo_carousel.tpl', 15, false),array('modifier', 'default', 'modulo_carousel.tpl', 54, false),array('function', 'imageresolution', 'modulo_carousel.tpl', 33, false),)), $this); ?>
<div class="clear"></div>

<div id="carousel" class="carousel-container">	
    <div class="carousel-pre">        		
		<?php if (isset ( $this->_tpl_vars['carousel_director'] )): ?>
        <div class="foto">
			<a href="<?php echo $this->_tpl_vars['carousel_director']->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['carousel_director']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
				<img alt="José Luis Gómez" src="/media/images/authors/jose-luis-gomez/2009022801490250024.gif"
					 height="65" /></a>
        </div>
		
				<span>José Luis Gómez</span><br />
		<a href="<?php echo $this->_tpl_vars['carousel_director']->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['carousel_director']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
			<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['carousel_director']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('truncate', true, $_tmp, 20, "...") : smarty_modifier_truncate($_tmp, 20, "...")); ?>
</a>

		<?php endif; ?>
    </div>        
    
    <div class="carousel">
        <div class="carousel-left">
			<a href="#previous">
				<img src="/themes/xornal/images/carousel/carousel-left.gif" border="0" alt="Left arrow" /></a>
		</div>
		<div class="carousel-center">
			<ul>
                <?php $this->assign('carousel_opiniones', $this->_tpl_vars['carousel_data']->items); ?>
				<?php unset($this->_sections['co']);
$this->_sections['co']['name'] = 'co';
$this->_sections['co']['loop'] = is_array($_loop=$this->_tpl_vars['carousel_opiniones']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['co']['show'] = true;
$this->_sections['co']['max'] = $this->_sections['co']['loop'];
$this->_sections['co']['step'] = 1;
$this->_sections['co']['start'] = $this->_sections['co']['step'] > 0 ? 0 : $this->_sections['co']['loop']-1;
if ($this->_sections['co']['show']) {
    $this->_sections['co']['total'] = $this->_sections['co']['loop'];
    if ($this->_sections['co']['total'] == 0)
        $this->_sections['co']['show'] = false;
} else
    $this->_sections['co']['total'] = 0;
if ($this->_sections['co']['show']):

            for ($this->_sections['co']['index'] = $this->_sections['co']['start'], $this->_sections['co']['iteration'] = 1;
                 $this->_sections['co']['iteration'] <= $this->_sections['co']['total'];
                 $this->_sections['co']['index'] += $this->_sections['co']['step'], $this->_sections['co']['iteration']++):
$this->_sections['co']['rownum'] = $this->_sections['co']['iteration'];
$this->_sections['co']['index_prev'] = $this->_sections['co']['index'] - $this->_sections['co']['step'];
$this->_sections['co']['index_next'] = $this->_sections['co']['index'] + $this->_sections['co']['step'];
$this->_sections['co']['first']      = ($this->_sections['co']['iteration'] == 1);
$this->_sections['co']['last']       = ($this->_sections['co']['iteration'] == $this->_sections['co']['total']);
?>
								<li>
					<a href="<?php echo $this->_tpl_vars['carousel_opiniones'][$this->_sections['co']['index']]->permalink; ?>
" title=""
		carousel:title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['carousel_opiniones'][$this->_sections['co']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
@@@, por <?php echo $this->_tpl_vars['carousel_opiniones'][$this->_sections['co']['index']]->author; ?>
<?php if (! empty ( $this->_tpl_vars['carousel_opiniones'][$this->_sections['co']['index']]->condition )): ?> (<?php echo $this->_tpl_vars['carousel_opiniones'][$this->_sections['co']['index']]->condition; ?>
)<?php endif; ?>">
						<img src="/media/images/<?php echo $this->_tpl_vars['carousel_opiniones'][$this->_sections['co']['index']]->photo; ?>
" alt="<?php echo $this->_tpl_vars['carousel_opiniones'][$this->_sections['co']['index']]->author; ?>
" <?php echo smarty_function_imageresolution(array('image' => $this->_tpl_vars['carousel_opiniones'][$this->_sections['co']['index']]->photo,'width' => '60','height' => '60'), $this);?>
 /></a>
				</li>            
				<?php endfor; endif; ?>
			</ul>
		</div>
        <div class="carousel-right">
			<a href="#next">
				<img src="/themes/xornal/images/carousel/carousel-right.gif" border="0" alt="Right arrow" /></a>
		</div>
		
		<!--<div class="clear"></div>-->
		
		<div class="carousel-message"></div>
    </div>

    <div class="carousel-post">
		<div class="editorial">
			<strong>editoriales</strong> <br />
			
			<?php unset($this->_sections['ed']);
$this->_sections['ed']['name'] = 'ed';
$this->_sections['ed']['loop'] = is_array($_loop=$this->_tpl_vars['carousel_editorial']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['ed']['show'] = true;
$this->_sections['ed']['max'] = $this->_sections['ed']['loop'];
$this->_sections['ed']['step'] = 1;
$this->_sections['ed']['start'] = $this->_sections['ed']['step'] > 0 ? 0 : $this->_sections['ed']['loop']-1;
if ($this->_sections['ed']['show']) {
    $this->_sections['ed']['total'] = $this->_sections['ed']['loop'];
    if ($this->_sections['ed']['total'] == 0)
        $this->_sections['ed']['show'] = false;
} else
    $this->_sections['ed']['total'] = 0;
if ($this->_sections['ed']['show']):

            for ($this->_sections['ed']['index'] = $this->_sections['ed']['start'], $this->_sections['ed']['iteration'] = 1;
                 $this->_sections['ed']['iteration'] <= $this->_sections['ed']['total'];
                 $this->_sections['ed']['index'] += $this->_sections['ed']['step'], $this->_sections['ed']['iteration']++):
$this->_sections['ed']['rownum'] = $this->_sections['ed']['iteration'];
$this->_sections['ed']['index_prev'] = $this->_sections['ed']['index'] - $this->_sections['ed']['step'];
$this->_sections['ed']['index_next'] = $this->_sections['ed']['index'] + $this->_sections['ed']['step'];
$this->_sections['ed']['first']      = ($this->_sections['ed']['iteration'] == 1);
$this->_sections['ed']['last']       = ($this->_sections['ed']['iteration'] == $this->_sections['ed']['total']);
?>
				<img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
flechitaMenu.gif" border="0" alt="Flecha Menu" />
				<a href="<?php echo ((is_array($_tmp=@$this->_tpl_vars['carousel_editorial'][$this->_sections['ed']['index']]->permalink)) ? $this->_run_mod_handler('default', true, $_tmp, "#") : smarty_modifier_default($_tmp, "#")); ?>
">
					<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['carousel_editorial'][$this->_sections['ed']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><br />
			<?php endfor; endif; ?>
		
		<div class="autores">
			<select onchange="carr.redirect(this.options[this.selectedIndex].value, this.options[this.selectedIndex].text);">
				<option value="0" selected="selected">Seleccione Autor</option>
				<option value="1">Editorial</option>
				<option value="2">Director</option>
	
				<?php unset($this->_sections['ca']);
$this->_sections['ca']['name'] = 'ca';
$this->_sections['ca']['loop'] = is_array($_loop=$this->_tpl_vars['carousel_autores']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['ca']['show'] = true;
$this->_sections['ca']['max'] = $this->_sections['ca']['loop'];
$this->_sections['ca']['step'] = 1;
$this->_sections['ca']['start'] = $this->_sections['ca']['step'] > 0 ? 0 : $this->_sections['ca']['loop']-1;
if ($this->_sections['ca']['show']) {
    $this->_sections['ca']['total'] = $this->_sections['ca']['loop'];
    if ($this->_sections['ca']['total'] == 0)
        $this->_sections['ca']['show'] = false;
} else
    $this->_sections['ca']['total'] = 0;
if ($this->_sections['ca']['show']):

            for ($this->_sections['ca']['index'] = $this->_sections['ca']['start'], $this->_sections['ca']['iteration'] = 1;
                 $this->_sections['ca']['iteration'] <= $this->_sections['ca']['total'];
                 $this->_sections['ca']['index'] += $this->_sections['ca']['step'], $this->_sections['ca']['iteration']++):
$this->_sections['ca']['rownum'] = $this->_sections['ca']['iteration'];
$this->_sections['ca']['index_prev'] = $this->_sections['ca']['index'] - $this->_sections['ca']['step'];
$this->_sections['ca']['index_next'] = $this->_sections['ca']['index'] + $this->_sections['ca']['step'];
$this->_sections['ca']['first']      = ($this->_sections['ca']['iteration'] == 1);
$this->_sections['ca']['last']       = ($this->_sections['ca']['iteration'] == $this->_sections['ca']['total']);
?>
				<option value="<?php echo $this->_tpl_vars['carousel_autores'][$this->_sections['ca']['index']]->id; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['carousel_autores'][$this->_sections['ca']['index']]->name)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</option>
				<?php endfor; endif; ?>
			</select>
		</div>
		</div>
    </div>	
</div>

<!--<div class="clear"></div>-->

<script type="text/javascript">
/* <![CDATA[ */<?php echo '
var carr = null;
//document.observe(\'dom:loaded\', function() {	
	carr = new OpenNeMas.Carousel(\'carousel\', {isLastest: '; ?>
<?php if ($this->_tpl_vars['carousel_data']->isLastest): ?>true<?php else: ?>false<?php endif; ?><?php echo '});
//});
'; ?>
/* ]]> */
</script>