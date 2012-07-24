{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/photos.js" defer="defer" language="javascript"}
{/block}
{block name="content"}
<form action="{if $author->id}{url name=admin_opinion_author_update id=$author->id}{else}{url name=admin_opinion_author_create}{/if}" method="POST" enctype="multipart/form-data" id="formulario" >
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{if !isset($author)}{t}Opinion Manager :: New author{/t}{else}{t}Opinion Manager :: Edit author{/t}{/if}</div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="Guardar y salir"><br />{t}Save{/t}
                    </button>
                </li>
                <li>
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" alt="Guardar y continuar" ><br />{t}Save and continue{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_opinions page=$page}" title="Cancelar">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
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
                    <td colspan="2">
                        <label for="params[inrss]">{t}Show in rss:{/t}</label>
                        <input type="checkbox" id="params[inrss]" name="params[inrss]" value="1"
                            {if !isset($author->params['inrss']) || $author->params['inrss'] eq 1} checked="checked"{/if}>
                            {t}If this option is activated this author will be showed in rss{/t}
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
                                             src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photos[as]->path_img}"
                                          />
                                     </a>
                                 </li>
                            {/section}
                            </ul>
                        </div>
                        <input type="hidden" id="fk_author_img" name="fk_author_img" value="" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong>{t}Upload an image{/t}:</strong>
                        <input type="file">
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
