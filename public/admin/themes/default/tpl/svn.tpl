{extends file="base/admin.tpl"}

{block name="admin_menu"}
	<div id="menu-acciones-admin" class="clearfix">
		<div style="float:left;margin-left:15px;margin-top:10px;"><h2>{$titulo_barra}</h2></div>
        <ul>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'status', 0);" onmouseover="return escape('<u>S</u>tatus');" accesskey="S" tabindex="4" title="Get the status of the OpenNeMaS system">
					<img border="0" src="{$params.IMAGE_DIR}checkout.png" title="Status" alt="Statis"><br />Status
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'update', 0);" onmouseover="return escape('<u>U</u>pdate');" accesskey="U" tabindex="3">
					<img border="0" src="{$params.IMAGE_DIR}update-system.png" title="Update" alt="Update OpenNeMas from the update server"><br />Update
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'co', 0);" onmouseover="return escape('<u>C</u>heckout');" accesskey="C" tabindex="2">
					<img border="0" src="{$params.IMAGE_DIR}checkout.png" title="Checkout" alt="Fetch the lastest version of OpenNeMas from update server"><br />Checkout
				</a>
			</li>
			{* <li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'info', 0);" onmouseover="return escape('<u>I</u>nfo');" accesskey="N" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}info.png" title="Info" alt="Info"><br />Info
				</a>
			</li> *}
            <li>
				<a href="#" class="admin_add" onclick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>L</u>ist');" accesskey="L" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}list.png" title="List" alt="List files in the system"><br />List
				</a>
			</li>
		</ul>
	</div>
{/block}

{block name="content"}
<div style="width:70%; margin:0 auto;">
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">


    {block name="admin_menu"}{/block}
    <table class="adminheading" style="margin-top:20px">
        <tr>
            <th nowrap>Please fill the information to connect to the update server and click on the above buttons.</th>
        </tr>
    </table>

    <table class="adminlist" style="padding:50px 100px">

        <tr>
            <td>
                <label for="username" >User Name:</label><br>
                <input type="text" id="svn_username" name="svn_username" title="Username"
                    value="{$username}" class="required" size="100" />
                <br>
                <label for="password" >Password:</label><br>
                <input type="password" id="svn_password" name="svn_password" title="Password"
                    value="{$password}" class="required" size="100" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="repository" >Server URL:</label><br>
                <input type="text" id="repository" name="repository" title="Svn-server"
                    value="{$repository}" class="required" size="100" readonly=readonly />
            </td>
        </tr>
        <tr >
            <td>
                <label for="destination">Destination folder:</label><br>
                <input type="text" id="destination" name="destination" title="Destination"
                    value="{$destination}" class="required" size="100" readonly=readonly />
            </td>
        </tr>
    </table>
    <div>
    {if isset($return) && !empty($return)}
        <div style="background:#F7F7F7; border:1px solid #D7D7D7; padding:1em; margin:10px auto; overflow:auto;">
            <h3>
                {if $action == 'co'}
                    Installing OpenNeMas on your server
                {elseif $action == 'info'}
                    Getting information from the server
                {elseif $action == 'update'}
                    Updating OpenNeMas to the lastest version
                {elseif $action == 'list'}
                    Listing files for your project
                {/if}
            </h3>
            <b>Acting performed: {$checkout}</b><br />
            <pre style="padding:1em; overflow:auto;">
            {foreach from=$return item=foo}
            {$foo}
            {/foreach}
            </pre>
        </div>
    {/if}
    </div>

</div>
<input type="hidden" id="action" name="action" value="" />
</form>
{/block}
