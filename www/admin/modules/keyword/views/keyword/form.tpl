{extends file="layout.tpl"}

{block name="head-title"}{$smarty.block.parent} - {t}Keyword Manager{/t}{/block}

{block name="head-js"}
    <script type="text/javascript" src="{$params.JS_DIR}edit_area/edit_area_full.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.localisation-min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.scrollTo-min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}ui.multiselect.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}anytime.js"></script>
{/block}

{block name="head-css"}
    <link rel="stylesheet" href="{$params.CSS_DIR}ui.multiselect.css" type="text/css" />
    <link rel="stylesheet" href="{$params.CSS_DIR}anytime.css" type="text/css" />
{/block}

{block name="body-content"}
    <form action="{baseurl}/{url route="keyword-keyword-"|cat:$request->getActionName()}" method="post">

    {flashmessenger}

    {toolbar_button toolbar="toolbar-top"
        icon="save" type="submit" text="Save"}

    {toolbar_route toolbar="toolbar-top"
        icon="close" route="keyword-keyword-index" text="Cancel"}

    <div id="menu-acciones-admin">
        <div style="float: left; margin-left: 10px; margin-top: 10px;">
            <h2>{t}Widget Manager{/t}</h2>
        </div>
        {toolbar name="toolbar-top"}
    </div>

    <div style="float: left;">
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
    <tbody>
    <tr>
        <td valign="top" align="right" style="padding:4px;" width="150">
            <label for="title">{t}Name of keyword{/t}:</label>
        </td>
        <td valign="top">
            <input type="text" id="word" name="word" title="Nombre del keyword" value="{$keyword->word}"
                   class="required" size="30" maxlength="60" />
        </td>
    </tr>
     <tr>
        <td valign="top" align="right" style="padding:4px;" width="150">
            <label for="title">{t}Value of keyword{/t}:</label>
        </td>
        <td valign="top">
            <input type="text" id="value" name="value" title="Value del keyword" value="{$keyword->value}"
                   class="required" size="30" maxlength="60" />
        </td>
    </tr>

    <tr>
        <td valign="top" align="right" style="padding:4px;">
            <label for="renderlet">{t}Tipo de keyword{/t}:</label>
        </td>
        <td valign="top">
            <select name="type" id="type">
                <option value="html" {if $keyword->type == 'EMAIL'}selected="selected"{/if}>email</option>
                <option value="php" {if $keyword->type == 'SEARCH'}selected="selected"{/if}>search</option>
                <option value="smarty" {if $keyword->type == 'URL'}selected="selected"{/if}>url</option>
            </select>
        </td>
    </tr>



    </tbody>
    </table>
    </div>
 
    {if ($request->getActionName() eq "update")}
    <input type="hidden" name="pk_content" value="{$keyword->pk_keyword}" />
    
    {/if}


    <script language="Javascript" type="text/javascript">
    /* <![CDATA[ */
    
     

    
    $(document).ready(function() {
        var headerOnClick = function(e) {
            var jQ = ($(this).get(0).nodeName.toLowerCase() == 'a')? $(this).parent() : jQ = $(this);

            jQ.parent().find('div.ui-widget-content').toggleClass('ui-helper-hidden');

            if(jQ.hasClass('ui-corner-top')) {
                jQ.removeClass('ui-corner-top').addClass('ui-corner-all');
            } else {
                jQ.removeClass('ui-corner-all').addClass('ui-corner-top');
            }

            e.preventDefault();
            e.stopPropagation();
        };

        jQuery('div.ui-widget-header a').click(headerOnClick);
        jQuery('div.ui-widget-header').click(headerOnClick).css('cursor', 'pointer');

        editAreaLoader.execCommand('content', "change_syntax", '{$widget->renderlet|default:"html"}');
    });
    /* ]]> */
    </script>

    </form>
{/block}