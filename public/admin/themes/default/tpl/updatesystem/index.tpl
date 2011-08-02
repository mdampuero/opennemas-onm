{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}System update{/t}</h2></div>
        <ul class="old-button">
			<li>
				<a href="{$smarty.server.PHP_SELF}?action=status" tabindex="4" title="{t}Get the status of the OpenNemas system{/t}">
					<img border="0" src="{$params.IMAGE_DIR}checkout.png" title="Status" alt="{t}Status{/t}"><br />{t}Status{/t}
				</a>
			</li>
			<li>
				<a href="{$smarty.server.PHP_SELF}?action=update" tabindex="3">
					<img border="0" src="{$params.IMAGE_DIR}update-system.png" title="Update" alt="{t}Update OpenNemas from the update server{/t}"><br />{t}Update{/t}
				</a>
			</li>
			<li>
				<a href="{$smarty.server.PHP_SELF}?action=co" tabindex="2">
					<img border="0" src="{$params.IMAGE_DIR}checkout.png" title="Checkout" alt="{t}Fetch the lastest version of OpenNemas from update server{/t}"><br />{t}Checkout{/t}
				</a>
			</li>
            <li>
				<a href="{$smarty.server.PHP_SELF}?action=list" onmouseover="return escape('{t}List{/t}');" accesskey="L" tabindex="1">
					<img border="0" src="{$params.IMAGE_DIR}list.png" title="List" alt={t}"List files from repository system{/t}"><br />{t}List{/t}
				</a>
			</li>
		</ul>
    </div>
</div>
<div class="wrapper-content">
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">

    <table class="adminheading">
        <tr>
            <th nowrap>{t}Please fill the information to connect to the update server.{/t}</th>
        </tr>
    </table>

    <table class="adminform" style="padding:50px 100px">
        <tr>
            <td>
                <label for="username" >{t}User Name:{/t}</label><br>
                <input type="text" id="scm_username" name="scm_username" title="{t}Username{/t}" value="{$username}" class="required" size="100" />
                <br>
                <label for="password" >{t}Password:{/t}</label><br>
                <input type="password" id="scm_password" name="scm_password" title="{t}Password{/t}" value="{$password}" class="required" size="100" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="repository" >{t}Server URL:{/t}</label><br>
                <input type="text" id="repository" name="repository" title="{t}Server URL{/t}"
                    value="{$repository}" class="required" size="100" readonly=readonly />
            </td>
        </tr>
        <tr >
            <td>
                <label for="destination">{t}Destination folder:{/t}</label><br>
                <input type="text" id="destination" name="destination" title="{t}Destination{/t}"
                    value="{$destination}" class="required" size="100" readonly=readonly />
            </td>
        </tr>
    </table>
    <div>
    {if isset($return) && !empty($return)}
        <div style="background:#F7F7F7; border:1px solid #D7D7D7; padding:1em; margin:10px auto; overflow:auto;">
            <h3>
                {if $action == 'co'}
                    {t}Download OpenNemas on your server{/t}
                {elseif $action == 'info'}
                    {t}Getting information from the server{/t}
                {elseif $action == 'update'}
                    {t}Updating OpenNemas to the lastest version{/t}
                {elseif $action == 'list'}
                    {t}Listing projects files{/t}
                {/if}
            </h3>
            <b>{t}Acting performed:{/t} {$checkout}</b><br />
            <pre style="padding:1em; overflow:auto;">
{if is_array($return) && (count($return) > 0)}
	{$return|implode:"\n\t"}
{/if}
            </pre>
        </div>
    {/if}
    </div>

</div>
<input type="hidden" id="action" name="action" value="" />
</form>
{/block}
