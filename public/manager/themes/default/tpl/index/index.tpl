{extends file="base/base.tpl"}

{block name="content" append}

<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Welcome to OpenNeMas instance manager{/t}</h2>
        </div>
    </div>
</div>
<form action="{$smarty.server.PHP_SELF}" method="post" name="formulario" id="formulario">
    <div class="wrapper-content">
        <table class="adminform">
            <tbody>
                <tr valign="top">
                    <td class="center">
                        Here you will see some statistics about <strong>instances</strong> and other <br>
                        awesome things that will blow out your imagination.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form> 
{/block}