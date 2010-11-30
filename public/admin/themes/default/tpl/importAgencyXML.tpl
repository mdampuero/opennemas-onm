{extends file="base/admin.tpl"}

{block name="admin_menu"}
	<div id="menu-acciones-admin" class="clearfix">
        <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}:: &nbsp;{$datos_cat[0]->title}</h2></div>
		<ul>
            <li>
				<a href="#" class="admin_add" onClick="cancel('list_agency', 'todos', '');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
            <li>
                <a href="#" class="admin_add"  onclick="enviar(this, '_self', 'check', 0);" onmouseover="return escape('<u>C</u>heck');" name="check" value="check">
                    <img border="0" src="{$params.IMAGE_DIR}checkout.png" alt="Importar"><br />Check
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onclick="enviar(this, '_self', 'import', 0);" onmouseover="return escape('<u>I</u>mportar XML');" name="import" value="import">
                    <img border="0" src="{$params.IMAGE_DIR}checkout.png" alt="Importar"><br />Import
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onclick="delFile()" onmouseover="return escape('<u>R</u>emove File');" name="remove" value="remove">
                    <img border="0" src="{$params.IMAGE_DIR}list-remove.png" alt="Remove"><br />Remove File
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onclick="addFile();" onmouseover="return escape('<u>A</u>dd File');" name="add" value="add">
                    <img border="0" src="{$params.IMAGE_DIR}list-add.png" alt="Add"><br />Add File
                </a>
            </li>
		</ul>
	</div>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}
	 style="max-width:70% !important; margin: 0 auto; display:block;">
<div>
    <!--form id="form_upload" action="{$smarty.server.SCRIPT_NAME}?action=addFile" method="POST" enctype="multipart/form-data"-->

    {block name="admin_menu"}{/block}

    <table class="adminheading">
        <tr>
            <th nowrap>&nbsp;</th>
        </tr>
    </table>
    <table class="adminlist">
        <tr><td colspan="2"><br />
                  <div id="FileContainer">

                        <div class="marcoFoto" id="File0">
                            <p style="font-weight: bold;">File #0:
                             <input type="file" name="file[0]" id="fFile0" class="required" size="50" onChange="ckeckName(this,'fileCat[0]');"/> <span style="text-align:right;width:240px;"> Importar a pendientes: <input type="checkbox"  id="check_pendientes[0]" checked="checked" name="check_pendientes[0]" value="1"  style="cursor:pointer;"> </span>
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
<pre style="background:#F7F7F7 none repeat scroll 0 0;border:1px solid #D7D7D7;padding:0;margin:0.5em 1em;overflow:auto;">
    <div style="float:right;width:200px">
        <span><b>Ficheros: {$total_num}</b></span>
        <table style="width: 100px;" align="center">
        {foreach from=$numCategories key=k item=i}
            {if ($i != '0')}<tr><td><b>{$k}: </b></td><td>{$i}</td></tr>{/if}
        {/foreach}
        </table>
    </div>

    {foreach from=$dataXML item=article name=articl}
    <table>
        <tr><td colspan="2"><h3>{$XMLFile[$smarty.foreach.articl.index]}</h3></td></tr>
     {if !empty($article.agency)}<tr><td><b>Agencia: </b></td><td>{$article.agency}</td></tr>{/if}
      {if !empty($article.created)}<tr><td><b>Fecha: </b></td><td>{$article.created}</td></tr>{/if}
        {if !empty($article.title)}<tr style="color:blue;font-size:16px;font-weight:700"><td>Titulo: </td><td>{$article.title}</td></tr>{/if}
        {if !empty($article.summary)}<tr><td><b>Entradilla: </b></td><td>{$article.summary}</td></tr>{/if}
        {if !empty($article.text)}<tr><td><b>Cuerpo: </b></td><td>{$article.text}</td></tr>{/if}
        {if !empty($article.date)}<tr><td><b>Fecha: </b></td><td>{$article.date}</td></tr>{/if}
        {if !empty($article.category)}<tr><td><b>Sección: </b></td><td>{$article.category}   </td></tr>{/if}

    </table>
    {/foreach}
</pre>
{else}
    <br />
     <pre style="background:#F7F7F7 none repeat scroll 0 0;border:1px solid #D7D7D7;padding:0;margin:0.5em 1em;overflow:auto;">
        <div style="text-align:center"><h3>Select a XML or a set of XML Files to import</h3></div>
    </pre>
{/if}
</div>


<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
