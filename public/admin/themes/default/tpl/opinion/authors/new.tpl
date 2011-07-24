{extends file="base/admin.tpl"}


{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{if $smarty.request.action eq "new"}{t}Opinion Manager :: New author{/t}{else}{t}Opinion Manager :: Edit author{/t}{/if}</div>
            <ul class="old-button">
                <li>
                    {if isset($author->pk_author)}
                    <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$author->pk_author}, 'formulario');">
                    {else}
                    <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0, 'formulario');">
                    {/if}
                    <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$author->pk_author}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    {if $smarty.session._from eq 'opinion.php'}
                            <a href="opinion.php" class="admin_add">
                            <img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                        </a>
                    {else}
                        <a href="{$_SERVER['PHP_SELF']}?action=list&page=0" class="admin_add" title="Cancelar">
                            <img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                        </a>
                    {/if}
                </li>

            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <table class="adminheading">
            <tr align="right">
                <td>&nbsp;</td>
            </tr>
        </table>
        <table  class="adminlist">
            <tbody>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="200px">
                        <label for="name">{t}Name{/t}</label>
                    </td>
                    <td >
                        <input type="text" id="name" name="name" title="{t}Author name{/t}"
                            value="{$author->name}" class="required"  size="50"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <label for="phone">{t}Condition{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="condition" name="condition" title="{t}Condition{/t}" value="{$author->condition}"  size="50"/>
                    </td>
                </tr>

     {* 			<tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">{t}Sex:{/t}</label>
                    </td>
                        <td style="padding:4px;" nowrap="nowrap">
                             <select name="gender" id="gender" class="required">
                                <option value="Mujer" {if $author->gender eq 'Mujer'} selected {/if}>{t}Women{/t}</option>
                                <option value="Hombre" {if $author->gender eq 'Hombre'} selected {/if}>{t}Men{/t}</option>
                            </select>
                        </td>
                </tr> *}

                <tr>
                    <td valign="top" align="right">
                        <label for="phone">{t}Blog name:{/t}</label>
                    </td>
                    <td>
                        <input type="text" id="politics" name="politics" title="{t}Blog name{/t}" value="{$author->politcs}"  size="50"/>
                    </td>
    {*				<td style="padding:4px;" nowrap="nowrap">
                        <select name="politics" id="politics" class="required" title="Tendencia politica">
                            <option value="Progresista" {if $author->politics eq 'Progresista'} selected {/if}>{t}Progresist{/t}</option>
                            <option value="Conservador" {if $author->politics eq 'Conservador'} selected {/if}>{t}Conservative{/t}</option>
                            <option value="Izquierdas" {if $author->politics eq 'Izquierdas'} selected {/if}>{t}Left-wind{/t}</option>
                            <option value="Derechas" {if $author->politics eq 'Derechas'} selected {/if}>{t}Right-wind{/t}</option>
                            <option value="Centro" {if $author->politics eq 'Centro'} selected {/if}>{t}Center-wind{/t}</option>
                            <option value="Comunista" {if $author->politics eq 'Comunista'} selected {/if}>{t}Comunist{/t}</option>
                         </select>
                    </td>*}
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="phone">{t}Blog url:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="gender" name="gender" title="{t}Blog url{/t}" value="{$author->gender}"  size="50"/>
                    </td>
                </tr>

     {*			<tr>
                    <td valign="top" align="right" style="padding:4px;" width="40%">
                        <label for="phone">{t}Birthday:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="date_nac" name="date_nac" size="18" title="{t}Birthday{/t}" value="{$author->date_nac}" /><button id="triggerend">...</button>
                    </td>
                </tr> *}

                <tr>
                    <td valign="top" align="right">
                        <b>{t}Author photos{/t}</b>
                    </td>
                    <td style="padding:4px;">
                        <div id="contenedor" name="contenedor" style="display:none; "> </div>
                        <div class="photos" >
                                 <ul id='thelist'  class="gallery_list">
                                        {section name=as loop=$photos}
                                        <li id='{$photos[as]->pk_img}'>
                                                <div style="float: left;width:90px;">
                                                        <a><img src="{$MEDIA_IMG_PATH_URL}{$photos[as]->path_img}" id="{$photos[as]->pk_img}" width="67"  border="1" /></a>
                                                        <br>
                                                        {$photos[as]->description}
                                                </div>
                                                <a href="#" onclick="javascript:del_photo('{$photos[as]->pk_img}');" title="{t}Delete photo{/t}">
                                                        <img src="{$params.IMAGE_DIR}iconos/eliminar.gif" border="0" align="absmiddle" style="width:20px; height:20px;" />
                                                </a>&nbsp;
                                        </li>
                                        {/section}
                                 </ul>
                        </div>
                        <input type="hidden" id="action" name="action" value="">
                        <input type="hidden" id="del_img" name="del_img" value="">
                        <input type="hidden" id="fk_author_img" name="fk_author_img" value="" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">{t}Upload more files{/t}</td>
                    <td>
                        <div id="iframe" style="display: inline;">
                            <iframe src="newPhoto.php?nameCat=authors&category=7" style=" background:#fff; height:300px; width:100%" align="center" frameborder="0" framespacing="0" scrolling="none" border="0"></iframe>
                        </div>
                    </td>
                </tr>
                <div id="photograph" style="width:80%; margin:0 auto;">
                </div>
            </tbody>

            <tfoot>
                <tr class="pagination">
                    <td colspan=2></td>
                </tr>
            </tfoot>
        </table>


        {*dhtml_calendar inputField="date_nac" button="triggerend" singleClick=true ifFormat="%Y-%m-%d" firstDay=1 align="CR"*}

        <style type="text/css">
            .gallery_list {
                width:auto !important;
            }
        </style>

    </div><!--fin wrapper-content-->
</form>
{/block}
