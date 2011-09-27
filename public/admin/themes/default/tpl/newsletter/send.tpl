{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/newsletter.css" media="screen"}
{/block}

{block name="header-js" append}
{script_tag src="/newsletter.js" language="javascript"}
{/block}
{block name="footer-js" append}
<script type="text/javascript">
    var postData = {$postmaster};

    // Set postmaster value
    $('postmaster').value = Object.toJSON(postData);

    // Attach click event to button
    var botonera = $('buttons').select('ul li a');
    botonera[0].setStyle({ display: '' });
    botonera[0].observe('click', function() {
    $('searchForm').action.value = 'preview';
    $('searchForm').submit();
    });
</script>
{/block}

{block name="content"}
<div class="top-action-bar" id="buttons">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Delivered newsletter report{/t} </h2>
            <img src="{$params.IMAGE_DIR}newsletter/5.gif" width="300" height="40" border="0" />
        </div>

        <ul class="old-button">
            <li>
                <a href="#" class="admin_add" title="{t}Back{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}arrow_previous.png" alt="" /><br />
                    {t}Previous step{/t}
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="wrapper-content">

    <div class="form">
        <form name="searchForm" id="searchForm" method="post" action="#">
        {* Valores asistente *}
        <input type="hidden" id="action"     name="action"     value="preview" />
        <input type="hidden" id="postmaster" name="postmaster" value="" />
        </form>
    </div>
    <table class="adminheading">
        <tr>
            <td></td>
        </tr>
    </table>

    <table class="adminlist">
        <tr>
            <td>
                {$html_final}
            </td>
        </tr>
        <tfoot>
            <tr>
                <td align="right" colspan=2>
                    <strong>Envío finalizado.</strong>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
{/block}
