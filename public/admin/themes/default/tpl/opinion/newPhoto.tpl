<html>
<head>
<title>..: Panel de Control :..</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


{css_tag href="/admin.css"}
{*[if IE]{css_tag href="/ieadmin.css.css"}[endif]*}
{script_tag src="/prototype.js" language="javascript"}
{script_tag src="/scriptaculous/scriptaculous.js" language="javascript"}
{script_tag src="/scriptaculous/photos.js" language="javascript"}
{$script|default:""}

</head>

<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="99%" >
<tbody>
<tr>
    <td align="left">
        <div>
            <form id="form_upload" action="{$smarty.server.SCRIPT_NAME}?action=addPhoto" method="POST" enctype="multipart/form-data">
                <p>
                    <table>
                        <tr>
                            <td style="border: 1px solid #ccc;"><a onclick="addFile();" style="cursor:pointer;"><img src="{$params.IMAGE_DIR}add.png" border="0" alt="A&ntilde;adir foto" width="22px" height="22px" /></a></td>
                            <td style="border: 1px solid #ccc;"><a onclick="delFile()" style="cursor:pointer;"><img src="{$params.IMAGE_DIR}del.png" border="0" alt="Suprimir foto" width="22px" height="22px" /></a></td>
                            <td style="border: 1px solid #ccc;"><input type="image" src="{$params.IMAGE_DIR}save_all.png" onClick="return getNameAuthor();" alt="Submit" name="submit" align="middle" width="22px" height="22px" style="cursor:pointer;" ></td>
                        </tr>
                    </table>
                </p>
                <input type="hidden" id="nameAuthor" name="nameAuthor" title="nameAuthor" value="{$nameAuthor|default:""}" size="40" />
                <div id="fotosContenedor">
                    <div class="marcoFoto" id="foto0"><input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                        <input type="hidden" id="nameCat" name="nameCat" title="nameCat" value="{$nameCat|default:""}" size="40" />
                        <input type="hidden" id="category" name="category" title="category" value="{$category|default:""}" size="40" />
                        <p style="font-weight: bold;">Foto #0:
                            <input type="file" name="file[0]" id="fFile0" class="required" size="50" onChange="ckeckName(this,'fileCat[0]');"/>
                            <div id="fileCat[0]" name="fileCat[0]" style="display:none;">
                                <table border='0' bgcolor='red'   cellpadding='4'>
                                    <tr>
                                        <td>El nombre es incorrecto. Contiene espacios en blanco o caracteres especiales.</td>
                                    </tr>
                                </table>
                            </div>
                        </p>

                    </div>

                </div>
                <table>
                    <tr>
                        <td>Descripcion:</td>
                        <td><input type="text" name="descript[0]" value="" id="descript[0]" size="36"/> <br/></td>
                    </tr>
                    <tr>
                        <td>Tags: </td>
                        <td><input type="text" name="tags[0]" value="" id="tags[0]" size="36"/></td>
                    </tr>
                </table>
            </form>
        </div>
    </td>
</tr>
<tr>
<td>

	<br /><hr><b>Forma de uso</b><br /><br />
	<table><tr><td><img src="{$params.IMAGE_DIR}add.png" border="0" alt="A&ntilde;adir fichero" width="22px" height="22px" /></td><td>Este signo se utiliza para <b>A&Ntilde;ADIR</b> una foto m&aacute;s al formulario de subida<br /></td></tr>
		<tr><td><img src="{$params.IMAGE_DIR}del.png" border="0" alt="A&ntilde;adir fichero" width="22px" height="22px" /></td><td> Este signo se utiliza para <b>QUITAR</b> una foto del formulario de subida<br /></td></tr>
	<tr><td><img src="{$params.IMAGE_DIR}save_all.png" border="0" alt="Guardar ficheros" width="22px" height="22px" /></td><td> Guardar las fotos del formulario de subida<br /></td></tr>

	</table>

</td>
</tr>
</tbody>
</table>


</body>
</html>
