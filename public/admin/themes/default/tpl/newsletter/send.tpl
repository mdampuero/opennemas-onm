{extends file="base/admin.tpl"}

{block name="header-css" append}
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}newsletter.css" media="screen" />
{/block}

{block name="header-js" append}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}newsletter.js?cacheburst=1259855452"></script>
{/block}
{block name="footer-js" append}
<script type="text/javascript">
    var postData = {$postmaster};

    // Set postmaster value
    $('postmaster').value = Object.toJSON(postData);

    // Attach click event to button
    var botonera = $('menu-acciones-admin').select('ul li a');
    botonera[0].setStyle({ display: '' });
    botonera[0].observe('click', function() {
    $('searchForm').action.value = 'preview';
    $('searchForm').submit();
    });
</script>
{/block}

{block name="content"}
<div class="wrapper-content">
    <div id="menu-acciones-admin">
        <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Delivered newsletter report{/t}</h2></div>
        <div class="steps">
            <img src="{$params.IMAGE_DIR}newsletter/5.gif" width="300" height="40" border="0" />
        </div>

        <ul>
            <li>
                <a href="#" class="admin_add" title="{t}Back{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}arrow_previous.png" alt="" /><br />
                    {t}Previous step{/t}
                </a>
            </li>
        </ul>
    </div>

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
