{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    .form-wrapper {
        margin:10px auto;
        width:50%;
    }
    .categories {
        width: 33%;
        margin-left: 10px;
    }
    </style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Sync Manager{/t} :: {t}Edit Site Configuration{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}" class="admin_add" value="{t}Go back{/t}" title="{t}Go back{/t}">
                <img src="{$params.IMAGE_DIR}previous.png" title="{t}Clients list{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
   <form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario">
        <br>

        {if $message}
        <div class="error">
             <ul>
                {foreach from=$message item=msg}
                <li>{$msg}</li>
                {/foreach}
             </ul>
        </div>
        {/if}

        {if (!empty($error))}
        <div class="error">
             {render_error}
        </div>
        {/if}

        <div>

             <table class="adminheading">
                 <tr>
                     <th>{t}Synchronization settings{/t}</th>
                 </tr>
             </table>

             <table class="adminform">
                <tr>
                    <td>
                        <div class="categories">
                            {$output}
                        </div>
                    </td>
                </tr>
            </table>

            <div class="action-bar clearfix">
                <div class="right">
                    <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
                </div>
            </div>

        </div>

        <input type="hidden" id="action" name="action" value="config" />
        <input type="hidden" id="site_url" name="site_url" value="{$site_url}" />
   </form>
</div>
{/block}
