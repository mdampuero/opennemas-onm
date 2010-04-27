<?php /* Smarty version 2.6.18, created on 2010-04-22 13:24:37
         compiled from widget_column_video.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'widget_column_video.tpl', 13, false),array('modifier', 'escape', 'widget_column_video.tpl', 13, false),)), $this); ?>


    <div class="video-preview">
        <h3 class="widget-title">Vídeo <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
bullets/bars-red.png" /></h3>
            <div class="video-content clearfix span-8 ">
                <?php if ($this->_tpl_vars['video']->author_name == 'youtube'): ?>
                    <a href="<?php echo $this->_tpl_vars['video']->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['video']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                       <object width="270" height="189">
                            <param value="http://www.youtube.com/v/<?php echo $this->_tpl_vars['video']->videoid; ?>
" name="movie" />
                            <param value="true" name="allowFullScreen" />
                            <param value="always" name="allowscriptaccess">
                            <embed width="270" height="189" src="http://www.youtube.com/v/<?php echo $this->_tpl_vars['video']->videoid; ?>
" />
                        </object>
                    </a>
                 <?php else: ?>
                  <a href="<?php echo $this->_tpl_vars['video']->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                    <object width="270" height="189">
                        <param name="allowfullscreen" value="true" />
                        <param name="allowscriptaccess" value="always" />
                        <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $this->_tpl_vars['video']->videoid; ?>
&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" />
                        <embed src="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $this->_tpl_vars['video']->videoid; ?>
&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="270" height="189"></embed>
                    </object>
                    </a>
                  <?php endif; ?>
            </div>
            <div class="video-explanation">
                  <h3><a href="<?php echo $this->_tpl_vars['video']->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['video']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                       <?php echo ((is_array($_tmp=$this->_tpl_vars['video']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
 </a>
                  </h3>
                  <p class="in-subtitle">  <?php echo ((is_array($_tmp=$this->_tpl_vars['video']->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</p>
            </div>
    </div><!-- .main-video -->

 
 