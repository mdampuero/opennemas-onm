{extends file='layout.tpl'}

{block name='body-content'}


{toolbar_button toolbar="toolbar-top"
        icon="svncheckout" type="submit" text="Checkout"
        name="command" value="co"}

{toolbar_button toolbar="toolbar-top"
        icon="svnstatus" type="submit" text="Status"
        name="command" value="status"}

{toolbar_button toolbar="toolbar-top"
        icon="svnupdate" type="submit" text="Update"
        name="command" value="update"}

{toolbar_button toolbar="toolbar-top"
        icon="svninfo" type="submit" text="Info"
        name="command" value="info"}

{toolbar_button toolbar="toolbar-top"
        icon="svnlist" type="submit" text="List"
        name="command" value="list"}

{*toolbar_button toolbar="toolbar-top"
        icon="default" type="submit" text="Netstat"
        name="command" value="netstat" *}

<form action="{baseurl}/{url route="systemupdater-index-index"}" method="post">

        <table class="adminform" border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
        <tr>
            <td style="padding:10px;" align="left" valign="top">
        
            {flashmessenger}        
                
            <div id="menu-acciones-admin">
                <div style="float: left; margin-left: 10px; margin-top: 10px;">
                    <h2>{t}System Updater web client{/t}</h2>
                </div>
                
                {toolbar name="toolbar-top"}
            </div>
            
            <table class="adminheading">
                <tr>
                    <th nowrap>{t}Subversion configuration{/t}</th>
                </tr>
            </table>
            
            <table class="adminlist">
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="10%">
                        <label for="title" >{t}User{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="90%" colspan='3'>
                        <input type="text" id="username" name="scm_username" title="Username"
                            value="{$cfg.scm_username}" class="required" size="100" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="10%">
                        <label for="title" >{t}Password{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="90%" colspan='3'>
                        <input type="password" id="password" name="scm_password" title="Password"
                            value="{$cfg.scm_password}" class="required" size="100" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="10%">
                        <label for="title">{t}Svn repository{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="90%" colspan='3'>
                        <input type="text" id="repository" name="scm_repository" title="Svn-server"
                            value="{$cfg.scm_repository}" class="required" size="100" disabled="disabled" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="10%">
                        <label for="title">{t}Folder destination{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="90%" colspan='3'>
                        <input type="text" id="destination" name="scm_destination" title="Destination"
                            value="{$cfg.scm_destination}" class="required" size="100" disabled="disabled" />
                    </td>
                </tr>
                <tr><td colspan="2"><br />
        
                </td></tr>
                </table>
            
                {if isset($output) && !empty($output)}
                    <h3>{$title}</h3>
                    
                    <strong>{$cmd}</strong>
                    
                    <blockquote>
                        <pre style="background:#F7F7F7 none repeat scroll 0 0;border:1px solid #D7D7D7;padding:0.5em;margin:0.5em 1em;overflow:auto;">{$output|escape:html}</pre>
                    </blockquote>        
                {/if}
        
            </td>
        </tr>
        </table>

</form>


{/block}