<?php /* Smarty version 2.6.18, created on 2010-04-22 12:52:20
         compiled from module_comments.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'numComments', 'module_comments.tpl', 5, false),array('insert', 'pagination_comments', 'module_comments.tpl', 18, false),array('insert', 'comments', 'module_comments.tpl', 23, false),)), $this); ?>
<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'numComments', 'id' => $this->_tpl_vars['content']->id, 'assign' => 'numComments')), $this); ?>


<div class="article-comments">
    <div class="title-comments">
        <?php if ($this->_tpl_vars['numComments'] > 0): ?>
        <h3><span><?php echo $this->_tpl_vars['numComments']; ?>
 Comentarios<span></h3>
        <?php else: ?>
        <h3><span>Sin Comentarios<span></h3>
        <?php endif; ?>
    </div>
    
    <div class="utilities-comments">
        <?php if ($this->_tpl_vars['numComments'] > 0): ?>
            <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'pagination_comments', 'total' => $this->_tpl_vars['numComments'])), $this); ?>

        <?php endif; ?>
    </div><!-- .utilities-comments -->        

    <div id="list-comments">
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'comments', 'id' => $this->_tpl_vars['content']->id)), $this); ?>

    </div>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_form_comments.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<script type="text/javascript">
/* <![CDATA[ */
var pkContent = '<?php echo $this->_tpl_vars['content']->id; ?>
';
<?php echo '
get_paginate_comments = function(page) {
    var url = "/comments.php?action=paginate_comments&id=" + pkContent + "&page=" + page;
    var previousContent = $("#list-comments").html();
    
    $("#list-comments").html(\'<img src="/themes/lucidity/images/loading.gif" border="0" />\');
    
    jQuery.ajax({
        url: url,
        type: "GET",
        success: function(data) {
            $("#list-comments").html(data);
        },
        error: function() {
            $("#list-comments").html(previousContent);
        }
    });        
};

$(\'.utilities-comments .pagination li a\').click(function(event){
    var current = $(this).attr(\'href\');
    
    if(current) {
        $(\'.utilities-comments .pagination li a\').each(function() {
            $(this).parent().removeClass(\'active\');
            
            if( $(this).attr(\'href\') == current ) {                
                $(this).parent().addClass(\'active\');
                
                var page = $(this).attr(\'href\').split(\'#\')[1];
                get_paginate_comments(page);
            } 
        });                        
    }
    
    event.preventDefault();
    event.stopPropagation();
});
'; ?>

/* ]]> */
</script>