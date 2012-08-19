{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/admin.css"}
{css_tag href="/newsletter.css" media="screen"}
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{script_tag src="/jquery-onm/jquery.newsletter.js"}
{/block}

{block name="content"}

<form action="#" method="post" name="newsletterForm" id="newsletterForm" {$formAttrs}>
    <div class="top-action-bar" id="buttons-send">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Delivered newsletter report{/t} </h2>
            </div>

            <ul class="old-button">
                <li>
                    <a href="#" id="prev-button" class="admin_add" title="{t}Back{/t}">
                        <img src="{$params.IMAGE_DIR}arrow_previous.png" alt="{t}Back{/t}" /><br />
                        {t}Previous step{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        <div class="form">
            <input type="hidden" id="action"  name="action" value="preview" />
        </div>

        <table class="adminlist">
            <tr>
                <td>
                    {$html_final}
                </td>
            </tr>
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
