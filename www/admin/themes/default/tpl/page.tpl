{include file="header.tpl"}

<table class="adminform" height="100%">
<tbody>
<tr><td>

<div id="treemenu">
{$tree}    
</div>

{literal}
<script type="text/javascript">
jQuery(function () {
    var nodeRef = null;
    
    moveNode = function(node_id, parent_id, weight) {
        var actionUrl = "/admin/pages/ajax/move/" + node_id + "/" + parent_id + '/' + weight + '/';
        $.ajax({
            type: "POST",
            url: actionUrl,            
            success: function(data) {
                
            },
            error: function(req, status, error) {
                
            }
        });
    };
    
    deleteNode = function(node_id) {
        var actionUrl = "/admin/pages/ajax/delete/" + node_id + "/";
        $.ajax({
            type: "POST",
            url: actionUrl,            
            success: function(data) {
                
            },
            error: function(req, status, error) {
                
            }
        });
    };
    
    renameNode = function(node_id, title) {                
        var actionUrl = "/admin/pages/ajax/rename/" + node_id + "/" + title + '/';
        $.ajax({
            type: "POST",
            url: actionUrl,
            success: function(data) {
                
            },
            error: function(req, status, error) {
                
            }
        });
    };
    
    createNode = function(ref, parent_id, weight, title) {
        var actionUrl = "/admin/pages/ajax/create/" + parent_id + '/' + weight + '/' + title + '/';
        nodeRef = ref;
        $.ajax({
            type: "POST",
            url: actionUrl,
            success: function(data) {
                $(nodeRef).attr('id', 'node-' + data);                
                nodeRef = null;
            },
            error: function(req, status, error) {
                
            }
        });
    };

    
    jQuery('#treemenu').tree({
        ui : {
            theme_name: 'default'
        },
        plugins : {
            contextmenu : { }
        },
        rules : {
            // only nodes of type root can be top level nodes
            valid_children : [ "root" ]
        },
        types : {
            // all node types inherit the "default" node type
            "default" : {
                deletable :  true,
                renameable : true,
                draggable :  true
            },
            "root" : {
                draggable : false,
                valid_children : [ 'standard', 'external', 'shortcut', 'not_in_menu' ],
                icon : { 
                    image : "/admin/themes/default/images/icons/page_white_world.png"
                }
            },
            "standard" : {
                icon : { 
                    image : "/admin/themes/default/images/icons/page_white.png"
                }
            },        
            "external" : {
                icon : { 
                    image : "/admin/themes/default/images/icons/page_white_link.png"
                }
            },
            "shortcut" : {
                icon : { 
                    image : "/admin/themes/default/images/icons/page_white_go.png"
                }
            },                
            "not_in_menu" : {
                icon : { 
                    image : "/admin/themes/default/images/icons/page_white_zip.png"
                }
            }
        },
        callback: {  //skeleton of all required callbacks
            onmove: function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
                var parent_id = 0;
                var weight    = 0;
                var node_id   = NODE.id.replace(/node\-/, '');                
                
                switch(TYPE) {
                    case 'inside':
                        parent_id = $(REF_NODE).attr('id').replace(/node\-/, '');
                        weight = 0;
                    break;
                    
                    case 'before':
                    case 'after':                        
                        parent_id = $(NODE).parent().parent().attr('id').replace(/node\-/, '');
                        var items = $(NODE).parent().children('li');
                        console.log(items);
                        
                        while(items.get(weight) &&
                              items.get(weight).id != $(NODE).attr('id')) {
                            weight++;
                        }
                        
                        console.log(TYPE + ' w:' + weight + ' p:' + parent_id);
                        console.log(NODE);
                    break;
                }
                
                moveNode(node_id, parent_id, weight);
            },
            
            oncreate: function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
                var parent_id = 0;
                var weight    = 0;
                var title     = $(NODE).get(0).innerText || $(NODE).get(0).textContent || "untitled";
                title = $.trim(title);
                
                switch(TYPE) {
                    case 'inside':
                        parent_id = $(REF_NODE).attr('id').replace(/node\-/, '');
                        weight = 0;
                    break;
                    
                    case 'before':
                    case 'after':                        
                        parent_id = $(NODE).parent().parent().attr('id').replace(/node\-/, '');
                        var items = $(NODE).parent().children('li');
                        
                        while(items.get(weight) &&
                              items.get(weight) != NODE) {
                            weight++;
                        }
                        
                        console.log(TYPE + ' w:' + weight + ' p:' + parent_id + ' NODE:' + NODE);
                    break;
                }
                
                createNode(NODE, parent_id, weight, title);                
            },
            
            ondelete: function(NODE, TREE_OBJ, RB) {
                var node_id = NODE.id.replace(/node\-/, '');
                deleteNode(node_id);
            },
            
            beforedelete: function(NODE, TREE_OBJ) {
                // check if it's a leaf node
                
                if($(NODE).find('li').size() > 0) {
                    alert('No es posible eliminar un árbol con hijos');
                    return false;
                }
                
                return true;
            },
            
            onrename: function(NODE, LANG, TREE_OBJ, RB) {                
                var node_id = NODE.id.replace(/node\-/, '');                
                var title   = $(NODE).get(0).innerText || $(NODE).get(0).textContent || "untitled";
                title = $.trim(title);
                
                renameNode(node_id, title);
            }
        }

    });
});
</script>
{/literal}

</td></tr>
</tbody>
</table>

{include file="footer.tpl"}