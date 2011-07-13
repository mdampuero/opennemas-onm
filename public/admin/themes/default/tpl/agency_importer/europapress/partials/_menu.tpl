<div id="menu-acciones-admin" class="clearfix">
    <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}EuropaPress importer :: List of articles{/t}</h2></div>
    <ul>
        <li>
            <a href="{$smarty.server.PHP_SELF}?action=sync" class="admin_add" value="{t}Sync with server{/t}" title="{t}Sync with server{/t}">
            <img border="0" src="{$params.IMAGE_DIR}sync.png" title="{t}Sync list  with server{/t}" alt="{t}Sync with server{/t}" ><br />{t}Sync with server{/t}
            </a>
        </li>
        <li>
            <a href="{$smarty.server.PHP_SELF}" class="admin_add" value="{t}Sync with server{/t}" title="{t}Reload list{/t}">
            <img border="0" src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" title="{t}Sync list  with server{/t}" alt="{t}Reload list{/t}" ><br />{t}Reload list{/t}
            </a>
        </li>

        <li>
            <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" value="{t}Config Europapress module{/t}" title="{t}Reload list{/t}">
            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config Europapress module{/t}" alt="{t}Config Europapress module{/t}" ><br />{t}Config{/t}
            </a>
        </li>
    </ul>
</div>
