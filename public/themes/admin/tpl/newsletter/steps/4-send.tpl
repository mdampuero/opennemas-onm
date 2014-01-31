{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/admin.css"}
{css_tag href="/newsletter.css" media="screen"}
<style type="text/css">
    .ok, .failed {
        font-weight: bold;
    }
    .ok {
        color: green;
    }
    .failed {
        color: red;
    }
</style>
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{/block}

{block name="content"}

<form action="#" method="post" name="newsletterForm" id="newsletterForm" {$formAttrs}>
    <div class="top-action-bar clearfix" id="buttons-send">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Delivered newsletter report{/t}</h2>
            </div>

            <ul class="old-button">
                <li>
                    <a href="{url name=admin_newsletters}" class="admin_add" title="{t}Back to list{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="" /><br />
                        {t}Back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        {render_messages}

        <div id="warnings-validation"></div>

        <table class="table">
            {foreach from=$sent_result item=result}
            <tr>
                <td>
                    {$result[0]->name} &lt;{$result[0]->email}&gt;
                    {if $result[1]}
                        <span class="ok">{t}OK{/t}</span>
                    {else}
                        <span class="failed">{t}Failed{/t} - {$result[2]}</span>
                    {/if}
                </td>
            </tr>
            {/foreach}

            <tfoot>
                <tr>
                    <td colspan=2>
                        <strong>Envío finalizado.</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</form>
{/block}
