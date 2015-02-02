{extends file="base/admin.tpl"}

{block name="header-css" append}
{/block}

{block name="header-js" prepend}
{/block}

{block name="content"}

<form action="{url name=admin_importer_xmlfile_import}" method="POST" enctype="multipart/form-data">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-download"></i>
                            {t}XML importer{/t}
                        </h4>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h5>{t}Select files{/t}</h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="#" onclick="delFile()" onmouseover="return escape('<u>R</u>emove File');" name="remove" value="remove">
                                <i class="fa fa-times"></i>
                                {t}Remove File{/t}
                            </a>
                        </li>
                        <li class="quicklinks">
                            <a class="btn btn-link" href="#" onclick="addFile();" onmouseover="return escape('<u>A</u>dd File');" name="add" value="add">
                                <i class="fa fa-plus"></i>
                                {t}Add File{/t}
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_importer_xmlfile_config}" title="{t}Config XML Schema{/t}">
                                <i class="fa fa-gear"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            {acl isAllowed="ARTICLE_CREATE"}
                               <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-download"></i>
                                    {t}Import{/t}
                                </button>
                            {/acl}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="content">

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


