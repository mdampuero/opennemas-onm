{extends file="base/base.tpl"}

{block name="content" append}

<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Cache manager{/t}</h2>
        </div>
    </div>
</div>
<form action="{$smarty.server.PHP_SELF}" method="post" name="formulario" id="formulario">
    <div class="wrapper-content">
        <table class="adminform">
            <tbody>
                <tr valign="top">
                    <td class="center">
                        {t}Here you can mass-delete cache, compile, sessions, and other stuff<br>
                        from one specific instance.{/t}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>
{/block}
