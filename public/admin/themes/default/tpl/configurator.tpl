{extends file="base/admin.tpl"}

{block name="content"}
{$smarty.block.parent}
<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

    {* FORM  ********************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "form"}
        <style>
        label {
            width: 200px;
            display: block;
            float: left;
            text-align: right;
        }

        div.panel input {
            margin: 0;
            margin-left: 0.5em;
            width:300px;
            display: block;
            float: left;
        }

        div.spacer {
        }
        </style>

        <div id="menu-acciones-admin">
            <div style="float: left; margin-left: 10px; margin-top: 10px;"><h2>{$titulo_barra}</h2></div>

            <ul>
                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self', 'save', -1);"
                       title="{t}Save configuration{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}save.gif" alt="{t}Save configuration{/t}" /><br />{t}Save{/t}
                    </a>
                </li>
                <li>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                </li>

                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self', 'backup', -1);"
                       title="{t}Backup configuration{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}saveas.png" alt="{t}Backup{/t}" /><br />{t}Backup{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self', 'listfiles', -1);"
                       title="{t}Recover configuration{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}clearcache.png" alt="{t}Recover{/t}" /><br />{t}Recover{/t}
                    </a>
                </li>
            </ul>
        </div>

        <div id="warnings-validation"></div>

        <ul id="tabs">
            {foreach key=sect item=no from=$entries}
            <li>
                <a href="#{$sect}">{t}{$sect}{/t}</a>
            </li>
            {/foreach}
        </ul>
        {foreach key=sect item=items from=$entries}
        <div id="{$sect}" class="panel" style="width:98%">
            {foreach key=k item=v from=$items}
                <div class="clearer spacer">&nbsp;</div>
                <label for="{$k}">{$v.title}:</label>
                <input type="text" name="entries[{$k}]" id="{$k}" value="{$v.value}" size="{$len}" />
            {/foreach}
            <div class="clearer spacer"></div>

        </div>
        {/foreach}

        <script type="text/javascript">
        /* <![CDATA[ */
        document.observe('dom:loaded', function() {
            new Fabtabs('tabs');
        });
        /* ]]> */
        </script>
        {/if}
        {* LIST BACKUPS ************************************************************** *}
        {if !isset($smarty.request.action) || $smarty.request.action eq "listfiles"}

        {literal}
        <script type="text/javascript">
        getCheckedValue = function(radioObj) {
            if(!radioObj)
                return null;
            var radioLength = radioObj.length;
            if(radioLength == undefined)
                if(radioObj.checked)
                    return radioObj.value;
                else
                    return null;
            for(var i = 0; i < radioLength; i++) {
                if(radioObj[i].checked) {
                    return radioObj[i].value;
                }
            }

            return null;
        };

        isCheckedFilename = function() {
            var checked = false;

            if(getCheckedValue(document.forms[0].filename) != null) {
                enviar($('formulario').childNodes[0], '_self', 'recover', -1);
            } else {
                new MessageBoard({'info': ['{/literal}{t}Please select a backup to restore{/t}{literal}']}, {container: 'msgBox', type: 'growl'}).render();
            }
        };
        </script>
        {/literal}

        <div id="menu-acciones-admin">
            <div style="float: left; margin-left: 10px; margin-top: 10px;"><h2>{$titulo_barra}</h2></div>

            <ul>
                <li>
                    <a href="?action=form" class="admin_add" title="{t}Cancel{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" alt="{t}Cancel{/t}" /><br />{t}Cancel{/t}
                    </a>
                </li>

                <li>
                    <a href="#" class="admin_add" onClick="javascript:isCheckedFilename();"
                       title="{t}Recover configuration{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}clearcache.png" alt="{t}Recover{/t}" /><br />{t}Recover{/t}
                    </a>
                </li>
            </ul>
        </div>

        <div id="warnings-validation"></div>

        <h2>Select file to restore:</h2>

        <ul>
        {section name="bks" loop=$files}
            <li>
                <label>
                    <input type="radio" name="filename" value="{$files[bks].filename}" />
                    {$files[bks].time|date_format:"%d/%m/%Y %H:%M:%S"}
                </label>
            </li>
        {/section}
        </ul>

        {/if}

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id}" />
    </form>

</div><!-- fin wrapper -->
{/block}
