<html>
<head>
<title>..: Panel de Control :..</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<link rel="stylesheet" href="{php}echo($this->css_dir);{/php}admin.css" type="text/css" />
<link rel="stylesheet" href="{php}echo($this->css_dir);{/php}style.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}messageboard.css" media="screen" />
<!--[if IE]>
    <link rel="stylesheet" href="{php}echo($this->css_dir);{/php}ieadmin.css" type="text/css" />
<![endif]-->

<script type="text/javascript" src="{$params.JS_DIR}prototype.js" language="javascript"></script>
<script type="text/javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js" language="javascript"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}MessageBoard.js"></script>

{literal}
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
function meterLista(eleto){
    var ul = parent.document.getElementById('thelist2');
    Nodes = parent.document.getElementById('thelist2').getElementsByTagName('li');
    var valor = parent.document.getElementById(eleto).value;
    var li = parent.document.createElement('LI');
    li.setAttribute('id', eleto);
    li.setAttribute('class', 'family');
    li.setAttribute('style', 'cursor: move; list-style-type: none;');
    li.setAttribute('value', valor);
    li.setAttribute('recordid', '100');
    li.innerHTML =  '- ' + valor;
    ul.appendChild(li);
} 
    
function meterListaint(eleto){
    var ulint = parent.document.getElementById('thelist2int');
    Nodes = parent.document.getElementById('thelist2int').getElementsByTagName('li');
    var valor = parent.document.getElementById(eleto).value;
    var li = parent.document.createElement('LI');
    li.setAttribute('id', eleto);
    li.setAttribute('class', 'family');
    li.setAttribute('style', 'cursor: move; list-style-type: none;');
    li.setAttribute('value', valor);
    li.setAttribute('recordid', '100');
    li.innerHTML =  '- ' + valor;
    ulint.appendChild(li);
    
}

function escribe(list) {
    $( 'informa' ).style.display = "none";
    nombre = list.value;	
    var posic = nombre.lastIndexOf('\\');
    
    if(posic == -1) {
        posic=nombre.lastIndexOf('/');
    }
    posic = posic+1; //Para que coja la /
    var titulo = nombre.substring(posic);	
    //var filter = /^[0-9A-Za-z_]+\.[A-Za-z][A-Za-z][A-Za-z]$/;
    var filter = /^[a-z0-9\-_]+\.[a-z0-9]{2,4}$/i;	// See also .htaccess, support for *.7z files
    if($('title').value=="") {    
    	$('title').value = titulo;
    }
    if (filter.test(titulo)) {
        hideMessage();
        $('op').enable();
        
        return true;    
    } else {
        showMessage('El nombre es incorrecto. Contiene espacios en blanco o caracteres especiales.');
        $('op').disable();
    }
}

function showMessage(message) {
    $( 'informa' ).innerHTML = "<table border='0' bgcolor='red'  width='280' cellpadding='4'>" +
        "<tr><td>"+message+"</td></tr></table>" ;
    $( 'informa' ).style.display = 'inline';
}

function hideMessage() {
    $( 'informa' ).update(''); 
    $( 'informa' ).style.display = "none"; 
}

function isValidFilename(filename) {
    var regExp = new RegExp('^[a-z0-9\-_]+\.[a-z0-9]{2,4}$', 'i');
    
    // WARNING: Problem with path, for secutiry reason browser replace path to c:\fake_path\...
    // http://lists.whatwg.org/pipermail/whatwg-whatwg.org/2009-March/019003.html
    filename = filename.replace(/.*?\\([^\\]+)$/, '$1');
    
    return regExp.test(filename);
}

function checkPath(elto) {
    var filename = elto.value;
    if(!isValidFilename(filename)) {
        showMessage('El nombre de fichero no está permitido en el sistema. Contiene espacios en blanco o caracteres especiales (*+[]ñáéíóúü).');
        
        // Resetear formulario
        elto.form.reset();
        
        // Disable button
        $('op').disable(); // this method is bundle into prototype
    } else {
        hideMessage();
        $('op').enable();
    }
}

document.observe('dom:loaded', function() {
    if($('op')) {
        $('op').disable();
    }
});
/* ]]> */
</script>
{/literal}

</head>

<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

    {$jscode}

    {messageboard type="inline"}
    
    <div class="mensaje">
        {$mensaje}
    </div>
    

    <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
    <tbody>
    <tr>
        <td style="padding:10px;" align="left" valign="top">
    
            <form action="adjuntos.php" method="POST" enctype="multipart/form-data">
                
                {* <div class="panel" id="edicion-contenido" style="width:220px"> *} 
                <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="200">
                <tbody>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="30%">
                        <label for="title">T&iacute;tulo:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="70%">
                        <input type="text" id="title" name="title" title="Título" autocomplete="off"
                            value="" class="required" size="50" />
                            
                        <input type="hidden" id="category" name="category" value="{$smarty.request.category}" />
                        <input type="hidden" id="related"  name="related"  value="{$smarty.request.related}" />
                        <input type="hidden" id="desde"    name="desde"    value="{$smarty.request.desde}" />                
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="30%">
                        <label for="title">Archivo:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="70%">
                        <input id="path" name="path" type="file" autocomplete="off"
                            onChange="javascript:escribe(this);checkPath(this);" />
                        <div id="informa" style="display: none; width:100%; height:40px;"></div>
                        <br />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <input id="op" name="op" type="submit" value="Adjuntar" />				
                    </td>
                </tr>
                </tbody>
                </table>
                {* </div> *}
                
            </form>
    
        </td>
    </tr>
    </tbody>
    </table>

</body>
</html>