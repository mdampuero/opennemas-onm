<?php
/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');
?>
<html>
<style type="text/css">
div.cuadroFoto {
	border: 1px #ccc solid;
	height: 100px;
	width: 200px;
	margin: 2px;
	padding: 4px;
	float: left;
	overflow: hidden;
	text-align: left;
}
</style>
<script>
 	        function asignaFoto(name) {
 	                inputF = '<div id="fotoInput"><input type="text" id="input_img" name="img" title="Imagen" value="'+name+'" /></div>';
 	                selec = '<div id="fotoSelec"><img src="../media/images/advertisements/'+name+'" id="img" border="0" /></div>';

 	                window.opener.document.getElementById('fotoInput').innerHTML = inputF;
 	                window.opener.document.getElementById('fotoSelec').innerHTML = selec;
 	                close();
 	        }
</script>

<body background='themes/default/images/fondo.gif'>

<?
if(!$_POST['op']) $op='view';

if ($op=='view') {
?><div>
<p><b>Seleccine una de las imagenes publicitarias que aparecen a continuacion o
 pulse el boton Examinar para seleccionarla en su directorio local.
</b></p>
<p align='center''>
	<form action="uppubli.php" method="post" enctype="multipart/form-data">
	    <b>Imagen Publicitaria:</b> <input name="file" type="file" /><br />

	    <input name="op" type="submit" value="subir">
	</form></p>
</div>
<div>
</ br>
<?
	//definimos el path de acceso
	$path = "../media/images/advertisements/";
	//abrimos el directorio
	$dir = opendir($path);
	//Mostramos las informaciones
	while ($file = readdir($dir))
	{
		if (strpos(strtolower($file), '.gif',1)
			||strpos(strtolower($file), '.jpg',1)
			||strpos(strtolower($file), '.png',1)) {
            echo "<div class=\"cuadroFoto\">";
            echo "<a href=\"javascript:;\" onclick=\"asignaFoto('".$file."')\">";
			echo "<img src='../media/images/advertisements/".$file."' border=0 />";
			echo "</a></div>";
        }
	}
	//Cerramos el directorio
	closedir($dir);
} else {
	$path = "../media/images/advertisements/";
	//datos del arhivo
	$nombre_archivo = $HTTP_POST_FILES['file']['name'];
	$tipo_archivo = $HTTP_POST_FILES['file']['type'];
	$tamano_archivo = $HTTP_POST_FILES['file']['size'];
	//compruebo si las características del archivo son las que deseo
	if (!(strpos($tipo_archivo, "gif") || strpos($tipo_archivo, "jpeg") || strpos($tipo_archivo, "jpg") || strpos($tipo_archivo, "png"))) {
	    echo "La extensión o el tamaño de los archivos no es correcta. <br><br><table><tr><td><li>Se permiten archivos .gif o .jpg</li></td></tr></table>";
	}else{
	    if (move_uploaded_file($HTTP_POST_FILES['file']['tmp_name'], $path.$nombre_archivo)){
	       echo "El archivo ha sido cargado correctamente.";
	       echo "<a href=\"javascript:;\" onclick=\"asignaFoto('".$nombre_archivo."')\">";
			echo "<img src='../media/images/advertisements/".$nombre_archivo."' border=0 />";
			echo "</a>";
	    }else{
	       echo "Ocurrió algún error al subir el fichero. No pudo guardarse.";
	    }
	}
	echo '<br /><br /><a href="uppubli.php">Ver imagenes publicitarias</a>';
}
?>
</div>



<p align='center'><b>Seleccine una de las imagenes publicitarias que aparecen a continuacion o
 pulse el boton Examinar para seleccionarla en su directorio local.
</b></p>
<p align='center'>
	<form action="uppubli.php" method="post" enctype="multipart/form-data">
	    <b>Nueva imagen publicitaria:</b><input name="file" type="file" /><br />
	    <input name="op" type="submit" value="subir">
	</form>
</p>

<br />
</body>
</html>