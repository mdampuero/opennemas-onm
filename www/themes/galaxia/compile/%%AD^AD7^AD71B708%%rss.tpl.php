<?php /* Smarty version 2.6.18, created on 2010-01-14 01:07:54
         compiled from rss.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'rss.tpl', 22, false),array('modifier', 'date_format', 'rss.tpl', 28, false),array('modifier', 'string_format', 'rss.tpl', 40, false),)), $this); ?>
<?php echo '<?xml'; ?>
 version="1.0" encoding="utf-8"<?php echo '?>'; ?>

<rss version="2.0">
	<channel>
		<title>RSS XORNAL DE GALICIA :: <?php echo $this->_tpl_vars['title_rss']; ?>
</title>
		<link><?php echo $this->_tpl_vars['RSS_URL']; ?>
</link>
		<description>Noticias de xornal.com	</description>
		<lastBuildDate><?php  echo date("D, j M Y H:i:s", gmmktime()) . ' GMT';  ?></lastBuildDate>
		<generator>Xornal.com Web</generator>
		<category><?php echo $this->_tpl_vars['title_rss']; ?>
</category>
		
		
		<image>
			<url><?php echo $this->_tpl_vars['SITE_URL']; ?>
themes/xornal/images/xornal-logo.jpg</url>
			<title>Xornal.com - RSS</title>
			<link><?php echo $this->_tpl_vars['SITE_URL']; ?>
</link>
		</image>
		<?php if (preg_match ( '/OPINION/' , $this->_tpl_vars['title_rss'] )): ?>
			<?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['rss']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
			<item>
				<title><?php echo ((is_array($_tmp=$this->_tpl_vars['rss'][$this->_sections['c']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</title>
				<link><?php echo $this->_tpl_vars['SITE_URL']; ?>
<?php echo $this->_tpl_vars['rss'][$this->_sections['c']['index']]['permalink']; ?>
</link>
				<description><![CDATA[<?php echo ((is_array($_tmp=$this->_tpl_vars['rss'][$this->_sections['c']['index']]['body'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
]]></description>
				<enclosure url="<?php echo $this->_tpl_vars['SITE_URL']; ?>
<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['rss'][$this->_sections['c']['index']]['path_img']; ?>
" type="image/gif"/>	
	       		<guid isPermaLink="true"><?php echo $this->_tpl_vars['SITE_URL']; ?>
<?php echo $this->_tpl_vars['rss'][$this->_sections['c']['index']]['permalink']; ?>
</guid>
	       		<author><![CDATA[<?php echo ((is_array($_tmp=$this->_tpl_vars['rss'][$this->_sections['c']['index']]['name'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
]]></author>
	       		<pubDate><![CDATA[<?php echo ((is_array($_tmp=$this->_tpl_vars['rss'][$this->_sections['c']['index']]['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%a, %d %b %Y %H:%M:%S %z") : smarty_modifier_date_format($_tmp, "%a, %d %b %Y %H:%M:%S %z")); ?>
]]></pubDate>
	    	</item>
	    	
	    	<?php endfor; endif; ?>
	    <?php else: ?>
			<?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['rss']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
			<item>
				<title><?php echo ((is_array($_tmp=$this->_tpl_vars['rss'][$this->_sections['c']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</title>
				<link><?php echo $this->_tpl_vars['SITE_URL']; ?>
<?php echo $this->_tpl_vars['rss'][$this->_sections['c']['index']]->permalink; ?>
</link>
				<description><![CDATA[<?php echo ((is_array($_tmp=$this->_tpl_vars['rss'][$this->_sections['c']['index']]->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
]]></description>
	           <?php $_from = $this->_tpl_vars['photos']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['myId'] => $this->_tpl_vars['i']):
?>
	              <?php if ($this->_tpl_vars['myId'] == $this->_tpl_vars['rss'][$this->_sections['c']['index']]->id): ?>
					<enclosure url="<?php echo $this->_tpl_vars['SITE_URL']; ?>
<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['i']->path_file; ?>
<?php echo $this->_tpl_vars['i']->name; ?>
" length="<?php echo ((is_array($_tmp=$this->_tpl_vars['i']->size*1024)) ? $this->_run_mod_handler('string_format', true, $_tmp, "%d") : smarty_modifier_string_format($_tmp, "%d")); ?>
" type="image/<?php echo $this->_tpl_vars['i']->type_img; ?>
"/>
	              <?php endif; ?>
	            <?php endforeach; endif; unset($_from); ?>			
	       		<guid isPermaLink="true"><?php echo $this->_tpl_vars['SITE_URL']; ?>
<?php echo $this->_tpl_vars['rss'][$this->_sections['c']['index']]->permalink; ?>
</guid>
	       		<author><![CDATA[<?php echo ((is_array($_tmp=$this->_tpl_vars['rss'][$this->_sections['c']['index']]->agency)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
]]></author>
	       		<pubDate><![CDATA[<?php echo ((is_array($_tmp=$this->_tpl_vars['rss'][$this->_sections['c']['index']]->created)) ? $this->_run_mod_handler('date_format', true, $_tmp, "%a, %d %b %Y %H:%M:%S %z") : smarty_modifier_date_format($_tmp, "%a, %d %b %Y %H:%M:%S %z")); ?>
]]></pubDate>
	    	</item>
	    	
	    	<?php endfor; endif; ?>
	    <?php endif; ?>
	</channel>
</rss>
