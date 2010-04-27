<div id="user_box" style="width:auto;">    
    <div id="name-box" style="float:left; margin-right:5px;">
      <strong>
        {t}Welcome{/t}
        <a href="/admin/user.php?action=read&id={$smarty.session.userid}" target="centro">
            {$smarty.session.username}
        </a>
        
        {if $smarty.session.isAdmin}
            <img src="{$params.IMAGE_DIR}key.png" border="0" align="absmiddle"
                title="{t}You have Admin privileges{/t}" alt="" />
        {/if}
      </strong>
    </div><!--end name-box-->
    
    {acl isAllowed="BACKEND_ADMIN"}
    <div style="padding-right:4px; float:left;" nowrap="nowrap">						
        <div id="user_activity" title="{t}Online users on backend{/t}">
            {$num_sessions}
        </div>
    </div>
    {/acl}

    <div id="session-actions" style="float:left;">
      <a href="javascript:salir();" class="logout" title="{t}Logout{/t}">
          <img src="{$params.IMAGE_DIR}logout.png" border="0" align="absmiddle" alt="" /> {t}Logout{/t}
      </a>
    </div><!--end session-actions -->
</div>

{if !is_null($mailbox) }
<div id="user_mailbox">                            
    <a href="https://www.google.com/accounts/ServiceLoginAuth?service=mail&Email={$smarty.session.email}&continue=https%3A%2f%2fmail.google.com%2fmail"
       title="{t}Goto GMail{/t} &lt;{$smarty.session.email}&gt;" target="_blank">
            <span>{$mailbox.total}</span>
            <img src="{$params.IMAGE_DIR}gmail_ico.png" border="0" align="absmiddle" />
    </a>
</div>
{/if}