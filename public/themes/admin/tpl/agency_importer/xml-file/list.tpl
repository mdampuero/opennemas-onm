{extends file="base/admin.tpl"}

{block name="header-css" append}
{/block}

{block name="header-js" prepend}
{/block}

{block name="content"}

<form action="{url name=admin_importer_xmlfile_import}" method="POST" enctype="multipart/form-data">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
             <div class="title"><h2>{t}XML importer{/t} :: {t}Select files{/t}</h2></div>
            <ul class="old-button">
                {*<li>
                    <button type="submit" name="dryrun" value="1">
                        <img src="{$params.IMAGE_DIR}checkout.png" alt="Importar"><br />{t}Check{/t}
                    </button>
                </li>*}
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}checkout.png" alt="Importar"><br />{t}Import{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="#" class="admin_add" onclick="delFile()" onmouseover="return escape('<u>R</u>emove File');" name="remove" value="remove">
                        <img border="0" src="{$params.IMAGE_DIR}list-remove.png" alt="Remove"><br />{t}Remove File{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onclick="addFile();" onmouseover="return escape('<u>A</u>dd File');" name="add" value="add">
                        <img border="0" src="{$params.IMAGE_DIR}list-add.png" alt="Add"><br />{t}Add File{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_importer_xmlfile_config}" title="{t}Config XML Schema{/t}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="{t}Config XML Schema{/t}" ><br />
                        {t}Config{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <table class="table table-hover table-condensed">
            <tr><td colspan="2"><br />
                      <div id="FileContainer">
                            <div class="marcoFoto" id="File0">
                                <p style="font-weight: bold;">File #0:
                                 <input type="file" name="file[0]" id="fFile0" class="required" size="50" onChange="ckeckName(this,'fileCat[0]');"/> <span style="text-align:right;width:240px;"> </span>
                                 <div id="fileCat[0]" name="fileCat[0]" style="display:none;"><table border='0' bgcolor='red'   cellpadding='4'><tr><td>El nombre es incorrecto. Contiene espacios en blanco o caracteres especiales.</td></tr></table></div>
                                </p>
                            </div>
                      </div>
                  <p>&nbsp;</p>
            </td></tr>
        </table><br />


            {if isset($dataXML) && !empty($dataXML)}
                {if isset($action) && $action eq 'check'}<h2>Checking XML files</h2>
                {else}<h2>IMPORTING XML files</h2>
                {/if}
                <br />
                <div style="background:#F7F7F7 none repeat scroll 0 0;border:1px solid #D7D7D7;padding:0;margin:0.5em 1em;overflow:auto;">
                    <div style="float:right;padding: 10px; width:200px">
                        <div><b>Ficheros:</b> {$total_num}</div>


                    </div>

                    {foreach from=$dataXML item=article name=articl}
                        <table style="width: 90%;">
                            <tr><td colspan="2"><h3>{$XMLFile[$smarty.foreach.articl.index]}</h3></td></tr>
                            {if !empty($article.title)}<tr style="color:blue;font-size:16px;font-weight:700"><td>Titulo: </td><td>{$article.title}</td></tr>
                            {else}<tr style="color:orangered;font-size:16px;font-weight:700"><td>Titulo: </td><td> No tiene</td></tr>
                            {/if}
                            {if !empty($article.title_int)}<tr style="font-size:16px;font-weight:700"><td>Titulo Interior: </td><td>{$article.title_int}</td></tr>{/if}
                            {if !empty($article.img1)}
                            <tr><td>
                                <strong>{t}Photos:{/t}</strong> </td><td>{$article.img} <br/>
                                <img style="max-height:120px; max-width:200px;" src="{$article.photo}">
                                 {$article.img1_footer}
                            </td></tr>
                            {/if}
                            {if !empty($article.agency)}<tr><td><b>Agencia: </b></td><td>{$article.agency|strip_tags}</td></tr>{/if}
                            {if !empty($article.created)}<tr><td><b>Fecha: </b></td><td>{$article.created}</td></tr>{/if}
                            {if !empty($article.category_name)}<tr><td><b>Sección: </b></td><td>{$article.category_name}   </td></tr>{/if}
                            {if !empty($article.subtitle)}<tr style="font-size:16px;font-weight:700"><td>Antetitulo: </td><td>{$article.subtitle}</td></tr>{/if}
                            {if !empty($article.summary)}<tr><td style="vertical-align:top;"><b>Entradilla: </b></td><td>{$article.summary}</td></tr>{/if}
                            {if !empty($article.body)}<tr><td style="vertical-align:top;" ><b>Cuerpo: </b></td><td>{$article.body}</td></tr>{/if}
                            {if !empty($article.description)}<tr><td><b>Description: </b></td><td>{$article.description}</td></tr>{/if}
                            {if !empty($article.metadata)}<tr><td><b>Metadata: </b></td><td>{$article.metadata}</td></tr>{/if}
                        </table>
                        <br>
                    {/foreach}
                </div>
            {else}
                <br />
                 <pre style="background:#F7F7F7 none repeat scroll 0 0;border:1px solid #D7D7D7;padding:0;margin:0.5em 1em;overflow:auto;">
                    <div style="text-align:center"><h3>Select a XML or a zip of XML Files to import</h3></div>
                </pre>
            {/if}
            </div>


            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" name="id" id="id" value="{$id|default:""}" />
        </div>
</form>
{/block}


