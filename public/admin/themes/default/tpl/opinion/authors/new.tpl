{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/photos.js" defer="defer" language="javascript"}    
{/block}
{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{if $smarty.request.action eq "new"}{t}Opinion Manager :: New author{/t}{else}{t}Opinion Manager :: Edit author{/t}{/if}</div>
            <ul class="old-button">
                <li>
                    {if isset($author->pk_author)}
                    <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$author->pk_author|default:""}, 'formulario');">
                    {else}
                    <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0, 'formulario');">
                    {/if}
                    <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$author->pk_author|default:""}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    {if $smarty.session._from eq 'opinion.php'}
                    <a href="opinion.php" class="admin_add">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                    </a>
                    {else}
                    <a href="{$smarty.server.PHP_SELF}?action=list&page=0" class="admin_add" title="Cancelar">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                    </a>
                    {/if}
                </li>

            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <div id="warnings-validation"></div>
        <table class="adminheading">
            <tr align="right">
                <td>&nbsp;</td>
            </tr>
        </table>
        <table  class="adminform">
            <tbody>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="200px">
                        <label for="name">{t}Name{/t}</label>
                    </td>
                    <td >
                        <input type="text" id="name" name="name" title="{t}Author name{/t}"
                            value="{$author->name|default:""}" class="required"  size="50"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <label for="phone">{t}Condition{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="condition" name="condition" title="{t}Condition{/t}" value="{$author->condition|default:""}"  size="50"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <label for="phone">{t}Blog name:{/t}</label>
                    </td>
                    <td>
                        <input type="text" id="politics" name="politics" title="{t}Blog name{/t}" value="{$author->politics|default:""}"  size="50"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="phone">{t}Blog url:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="blog" name="blog" title="{t}Blog url{/t}" value="{$author->blog}"  size="50"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <b>{t}Author photos{/t}:</b>
                        {if $photos}
                        </br>
                        <b style="font-size:0.8em;">({t}Double click on image to delete{/t})</b>
                        {/if}
                    </td>
                    <td style="padding:4px;">
                        <div id="contenedor" name="contenedor" style="display:none; "> </div>
                        <div id="photos" class="photos" >
                            <ul id='thelist'  class="gallery_list">
                            {section name=as loop=$photos|default:array()}
                                <li id='{$photos[as]->pk_img}'>
                                    <a class="album" title="{t}Show image{/t}">
                                         <img ondblclick="del_photo('{$photos[as]->pk_img}');"
                                             style="{cssimagescale resolution=67 photo=$photos[as]}"
                                             src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photos[as]->path_img}"
                                          />
                                     </a>
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
                    <td valign="top" align="right">
                        <b>{t}Upload an image{/t}:</b>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap"    >
                        <div id="iframe" style="display: inline;">
                            <iframe src="newPhoto.php?nameCat=authors&category=7" style=" background:#fff; height:300px; width:100%" align="center" frameborder="0" framespacing="0" scrolling="none" border="0"></iframe>
                        </div>
                    </td>
                </tr>
            </tbody>

    <tfoot>
        <tr class="pagination">
            <td colspan=2></td>
        </tr>
    </tfoot>
</table>
</form>
{/block}
