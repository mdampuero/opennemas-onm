{include file="header.tpl"}

<table class="adminform" height="100%">
<tbody>
<tr><td>

<div id="asdf">
{$tree}    
</div>

{literal}
<script type="text/javascript">
$(function () {
  jQuery('#asdf').tree({
    ui : {
        theme_name: 'classic'
    },
    rules : {
        // only nodes of type root can be top level nodes
        valid_children : [ "root" ]
	},
    types : {
        // all node types inherit the "default" node type
        "default" : {
            deletable : false,
            renameable : false
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
    }
  });
});
</script>
{/literal}

</td></tr>
</tbody>
</table>

{include file="footer.tpl"}