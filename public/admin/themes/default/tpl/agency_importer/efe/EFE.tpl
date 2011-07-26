{extends file="base/admin.tpl"}

{block name="content"}
 <script type="text/javascript" language="javascript" src="{$params.JS_DIR}addFiles.js"></script>

<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}{$titulo_barra}{/t}</h2></div>
        <ul class="old-button">
            <li>
				<a href="#" class="admin_add" onClick="cancel('list_agency', 'todos', '');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
				</a>
			</li>
            <li>
                <a href="#" class="admin_add"  onclick="enviar(this, '_self', 'check', 0);" onmouseover="return escape('<u>C</u>heck');" name="check" value="check">
                    <img border="0" src="{$params.IMAGE_DIR}checkout.png" alt="Importar"><br />{t}Check{/t}
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onclick="enviar(this, '_self', 'import', 0);" onmouseover="return escape('<u>I</u>mportar XML');" name="import" value="import">
                    <img border="0" src="{$params.IMAGE_DIR}checkout.png" alt="Importar"><br />{t}Import{/t}
                </a>
            </li>
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
        </ul>
    </div>
</div>

<div class="wrapper-content">
	<div>

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
        <div style="background:#F7F7F7 none repeat scroll 0 0;border:1px solid #D7D7D7;padding:0;margin:0.5em 1em;overflow:auto;">
            <div style="float:right;padding: 10px; width:200px">
                <div><b>Ficheros:</b> {$total_num}</div>
                {if !empty($numCategories)}
                     <br>
                     <div><b>{t}To categories{/t}:</b></div>
                     <ul style="list-style:none;">
                        {foreach from=$numCategories key=k item=i}
                            {if ($i != '0')}<li><b>{$k}: </b> {$i}</li>{/if}
                        {/foreach}
                     </ul>
                {/if}
               
            </div>

            {foreach from=$dataXML item=article name=articl}
                <table style="width: 90%;">
                    <tr><td colspan="2"><h3>{$XMLFile[$smarty.foreach.articl.index]}</h3></td></tr>
                 {if !empty($article.agency)}<tr><td><b>Agencia: </b></td><td>{$article.agency}</td></tr>{/if}
                  {if !empty($article.created)}<tr><td><b>Fecha: </b></td><td>{$article.created}</td></tr>{/if}
                    {if !empty($article.title)}<tr style="color:blue;font-size:16px;font-weight:700"><td>Titulo: </td><td>{$article.title}</td></tr>{/if}
                    {if !empty($article.summary)}<tr><td style="vertical-align:top;"><b>Entradilla: </b></td><td>{$article.summary}</td></tr>{/if}
                    {if !empty($article.text)}<tr><td style="vertical-align:top;" ><b>Cuerpo: </b></td><td>{$article.text}</td></tr>{/if}
                    {if !empty($article.date)}<tr><td><b>Fecha: </b></td><td>{$article.date}</td></tr>{/if}
                    {if !empty($article.category)}<tr><td><b>Sección: </b></td><td>{$article.category}   </td></tr>{/if}
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
	<input type="hidden" name="id" id="id" value="{$id}" />
</div>
</form>
{/block}


 