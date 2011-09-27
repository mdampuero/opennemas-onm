{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
function toggleAdvanced() {
    var results = $$('tr.advanced');
    results.each(function(elem){
        elem.toggleClassName('nodisplay');
    });
}
</script>
{/block}

{block name="header-css" append}
<style type="text/css">
.nodisplay {
    display:none;
}

table th, table label {
    color: #888;
    text-shadow: white 0 1px 0;
    font-size: 13px;
}
th {
    vertical-align: top;
    text-align: left;
    padding: 10px;
    width: 200px;
    font-size: 13px;
}
label{
    font-weight:normal;
}
.panel {
    background:White;
}
fieldset {
    border:none;
    border-top:1px solid #ccc;
}
legend {
    color:#666;
    text-transform:uppercase;
    font-size:13px;
    padding:0 10px;
}

.awesome {
    border:0;
}
.panel {
    margin:0;
}
.default-value {
    display:inline;
    color:#666;
    margin-left:10px;
    vertical-align:middle
}
input[type="text"],
textarea{
    max-height:80%
}
</style>
{/block}


{block name="footer-js" append}
    {script_tag src="/photos.js" defer="defer" language="javascript"}
    {if isset($smarty.request.message) && strlen($smarty.request.message) > 0}
        <div class="message" id="console-info">{$smarty.request.message}</div>
        <script defer="defer" type="text/javascript">
            new Effect.Highlight('console-info', {ldelim}startcolor:'#ff99ff', endcolor:'#999999'{rdelim})
        </script>
    {/if}

    <script defer="defer" type="text/javascript">
    function confirmar(url) {
        if(confirm('¿Está seguro de querer eliminar este fichero?')) {
            location.href = url;
        }
    }
    </script>

    {if !empty($smarty.request.alerta)}
    <script type="text/javascript">
        alert("NO SE PUEDE ELIMINAR {$smarty.request.name} .\n Esta imagen está siendo utilizada en: {$smarty.request.alerta}.");
    </script>
    {/if}

    <script type="text/javascript" language="javascript">
        if($('starttime')) {
            new Control.DatePicker($('starttime'), {
                icon: '{$params.IMAGE_DIR}/template_manager/update16x16.png',
                locale: 'es_ES',
                timePicker: true,
                timePickerAdjacent: true,
                dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
            });

        }
        else {
            alert('Not existent');
        }

        if($('endtime')) {
            new Control.DatePicker($('endtime'), {
                icon: '{$params.IMAGE_DIR}/template_manager/update16x16.png',
                locale: 'es_ES',
                timePicker: true,
                timePickerAdjacent: true,
                dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
            });

        }
    </script>

{/block}

{block name="content"}
<form action="{$smarty.server.PHP_SELF}" method="get">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Images Manager{/t}:: {if $action eq 'search'} {t}Search{/t} {elseif $action eq 'searchResult'} {t}Search result{/t} {else} {t}Information{/t} {/if} </h2></div>
            <ul class="old-button">
                <li>
                    <a class="admin_add" href="mediamanager.php?category={$category}" onmouseover="return escape('Listado de Categorias');" name="submit_mult" value="Listado de Categorias">
                        <img border="0" style="width:50px;"  src="{$params.IMAGE_DIR}previous.png" alt="Información"><br />{t}Go back{/t}
                    </a>
                </li>
                {if $action eq 'searchResult'}
                <li>
                    <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar"><br />{t}Delete{/t}
                    </a>
                </li>
                <li>
                    <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                        <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                    </button>
                </li>
                {/if}
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        <div id="contenedor-gral">

            <ul class="pills">
                <li>
                    <a href="mediamanager.php?listmode={$listmode|default:""}&category=GLOBAL" {if $category==0}class="active"{/if}>
                        {t}Global{/t}</a>
                </li>
                {if $smarty.server.PHP_SELF eq '/admin/controllers/mediamanager/mediamanager.php'}
                {acl isAllowed="ADVERTISEMENT_ADMIN"}
                    <li>
                        <a href="mediamanager.php?listmode={$listmode|default:""}&category=2" {if $category==2}class="active"{/if}>
                            {t}Advertisement{/t}</a>
                    </li>
                {/acl}
                {/if}
                {include file="menu_categories.tpl" home="mediamanager.php?listmode="}
            </ul>

            <table class="adminheading">
                <tr>
                    <td></td>
                </tr>
            </table>
            <table class="adminform" >
                <tbody >
                    <tr>
                        <th align='right'> <strong>{t}Image name:{/t}</strong></td>
                        <td align='left'>
                                <input type="text" id="stringSearch" name="stringSearch" size="60" value="{$smarty.request.stringSearch}" />
                            <br />
                        </td>
                    </tr>
                    <tr>
                        <th align='right'> <strong>{t}Section{/t}</strong></td>
                        <td align='left'>
                            <select name="categ" id="categ" />
                                <option value="todas" {if $photo1->color eq "todas"} selected {/if}>{t}All{/t}</option>
                                <option value="2" {if $category eq "2"} selected {/if}>{t}Advertisement{/t}</option>
                                {section name=as loop=$allcategorys}
                                    <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}">{$allcategorys[as]->title}</option>
                                    {section name=su loop=$subcat[as]}
                                        <option value="{$subcat[as][su]->pk_content_category}" {if $category  eq $subcat[as][su]->pk_content_category} selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                   {/section}
                                {/section}
                            </select>
                         </td>
                    </tr>

                    <tr>
                        <th></th>
                        <td><a href="javascript:toggleAdvanced();" id="show-advanced">{t escape=off}Show advanced search &darr;{/t}</a></td>
                    </tr>
                    <tr class="advanced nodisplay">
                        <th align='right'> <strong>{t}Size:{/t}</strong> </td>
                        <td align='left'>

                            <label for="anchoMax">{t}Max width:{/t} </label>
                            <input type="text" id="anchoMax" name="anchoMax" size="10" />
                            <br>
                            <label for="anchoMin">{t}Min width:{/t}</label>
                            <input type="text" id="anchoMin" name="anchoMin" size="10" />
                            <br><br>
                            <label for="altoMax">{t}Max height:{/t}</label>
                            <input type="text" id="altoMax" name="altoMax" size="10" />
                            <br>
                            <label for="altoMin">{t}Min height:{/t}</label>
                            <input type="text" id="altoMin" name="altoMin" size="10" />

                        </td>
                    </tr>
                    <tr class="advanced nodisplay">
                        <th align='right'> <strong>{t}File size:{/t}</strong> </td>
                        <td align='left'>

                            <label for="pesoMax">{t}Max:{/t}</label>
                            <input type="text" id="pesoMax" name="pesoMax" size="18" />  Kb
                        </td>
                    </tr>
                    <tr class="advanced nodisplay">
                        <td align='right'></td>
                        <td align='left'>
                            <label for="pesoMin">{t}Min:{/t}</label>
                            <input type="text" id="pesoMin" name="pesoMin" size="18" />  Kb
                        </td>
                    </tr>
                    <tr class="advanced nodisplay">
                        <th align='right'> <strong>{t}Type:{/t}</strong></td>
                        <td align='left'>
                            <select name="tipo" id="tipo" />
                                <option value="" selected >{t} - All types - {/t}</option>
                                <option value="jpg" >jpg</option>
                                <option value="gif" >gif</option>
                                <option value="png" >png</option>
                                <option value="svg" >svg</option>
                                <option value="swf" >swf</option>
                                <option value="otros" >{t}Others{/t}</option>
                            </select>
                         </td>
                    </tr>
                    <tr class="advanced nodisplay">
                        <th align='right'> <strong>{t}Color:{/t}</strong></td>
                        <td align='left'>
                            <select name="color" id="color" />
                                 <option value="" selected  >{t} - All types - {/t}</option>
                                <option value="BN" >{t}Black and white{/t}</option>
                                <option value="color" >{t}Color{/t}</option>
                            </select>
                         </td>
                    </tr>
                    <tr class="advanced nodisplay">
                        <th align='right'> <strong>{t}Author:{/t}</strong></td>
                        <td align='left'>
                            <input type="text" id="author" name="author"
                            value='{$photo1->author_name|clearslash|escape:'html'}' size="15"  title="Autor" />
                        </td>
                    </tr>
                    <tr class="advanced nodisplay">
                        <th align='right' style="vertical-align:top;"><strong>Periodo:</strong></td>
                        <td align='left'>
                            {t}From:{/t}<input type="text" size="18" id="starttime" name="starttime"
                            value=""  title="Fecha" />

                            {t}To:{/t}<input type="text" size="18" id="endtime" name="endtime"
                            value=""  title="Fecha" />
                         </td>
                    </tr>
                    <tr class="advanced nodisplay">
                        <td colspan='2' align='center' style="padding:20px 0;">
                            <input type="hidden" name="acti"  id="acti" value="searchResult" />
                        </td>
                    </tr>
                    </div>
                </tbody>
            </table>
            <div class="action-bar clearfix">
                <div class="right">
                    <button type="submit" class="onm-button red">{t}Search{/t}</button>
                </div>
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="searchResults" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>

{/block}
