{include file="header_files.tpl"}
<div>
    <!--form id="form_upload" action="{$smarty.server.SCRIPT_NAME}?action=addFile" method="POST" enctype="multipart/form-data"-->
    
    {include file="botonera_up.tpl"}
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
     {if !empty($article->autor_nombre)}<tr><td><b>Autor: </b></td><td>{$article->autor_nombre}</td></tr>{/if}
        {if !empty($article->titulo)}<tr style="color:blue;font-size:16px;font-weight:700"><td>Titulo: </td><td>{$article->titulo|truncate:50}</td></tr>{/if}
        {if !empty($article->entradilla)}<tr><td><b>Entradilla: </b></td><td>{$article->entradilla|truncate:100}</td></tr>{/if}
        {if !empty($article->textoArticulo)}<tr><td><b>Cuerpo: </b></td><td>{$article->textoArticulo|truncate:200}</td></tr>{/if}
        {if !empty($article->fecha)}<tr><td><b>Fecha: </b></td><td>{$article->fecha}</td></tr>{/if}
        {if !empty($article->seccion)}<tr><td><b>Seccion: </b></td><td>{$article->seccion}  {if !empty($article->page)} <b>Pagina: </b> {$article->page} - Artículo: {$article->num_article}{/if}</td></tr>{/if}
        {if !empty($article->agency)}<tr><td><b>Agency: </b></td><td>{$article->agency}</td></tr>{/if}
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

{include file="footer.tpl"}