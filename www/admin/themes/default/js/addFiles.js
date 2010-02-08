 //Por ahora igual que el mmanager.
var n = 0;

function addFile() {

	if (n==9) {
		alert('Solo puedes subir un maximo de 10 imagenes de cada vez.');
		return;
	}

	var divFile, pNombre, iNombre, tNombre, pFile, iFile, sFile, tFile, pBox, iBox, tBox, dFile, eFile, tdFile, teFile;
	var o0File, o1File, o2File, o3File, o4File, o5File, o6File, o7File, o8File, o9File, o10File, o11File;
	var t0File, t1File, t2File, t3File, t4File, t5File, t6File, t7File, t8File, t9File, t10File, t11File;
	var contenedor = $('FileContainer');

	//Aumentamos n
	n++;

	//Creamos el div del formulario especifico de la foto
	divFile = document.createElement('div');
  	divFile.id = 'File' + n;
  	divFile.setAttribute('class','marcoFoto');

	//Creamos el p y el input para la imagen
	pFile = document.createElement('p');
	pFile.setAttribute('style','font-weight:bold;');
	tFile = document.createTextNode('File #'+n+': ');

	iFile = document.createElement('input');
	iFile.setAttribute('name','file['+n+']');
	iFile.setAttribute('value','');
	iFile.setAttribute('type','file');
	iFile.setAttribute('id','fFile'+n);
	iFile.setAttribute('class','boton');
	iFile.setAttribute('size','50');
	iFile.setAttribute('onChange','ckeckName(this,\'fileCat['+n+']\')');

	dFile = document.createElement('input');
	dFile.setAttribute('name','descript['+n+']');
	dFile.setAttribute('value','');
	dFile.setAttribute('type','text');
	dFile.setAttribute('id','descript['+n+']');
	dFile.setAttribute('size','36');

	eFile = document.createElement('input');
	eFile.setAttribute('name','tags['+n+']');
	eFile.setAttribute('value','');
	eFile.setAttribute('type','text');
	eFile.setAttribute('id','tags['+n+']');
	eFile.setAttribute('size','38');

        cFile = document.createElement('span');
	cFile.setAttribute('name','fileCat['+n+']');
	cFile.setAttribute('id','fileCat['+n+']');
	cFile.innerHTML= ' <table border="0" bgcolor="red"  cellpadding="4"><tr><td>El nombre es incorrecto. Contiene espacios en blanco o caracteres especiales.</td></tr></table>' ;
 	cFile.setAttribute('style','display:none');

// </b></span>
        sFile = document.createElement('span');
	sFile.setAttribute('style','text-align:right;width:240px;');

	sFile.innerHTML= ' Importar a pendientes: <input type="checkbox"  checked="checked" id="check_pendientes['+n+']"  name="check_pendientes['+n+']" value="1"  style="cursor:pointer;"> ' ;


	tdFile = document.createTextNode(' Descripcion:');
	teFile = document.createTextNode(' Tags: ');

	pFile.appendChild(tFile);
	pFile.appendChild(iFile); //input file
	pFile.appendChild(sFile);  //div con nombre
        pFile.appendChild(cFile);  //span con check pendientes

	/*/Creamos el p y el checbox de nombre
	pBox = document.createElement('p');
	tBox = document.createTextNode(' Asignar como nombre el mismo que el de la File.');
	iBox = document.createElement('input');
	iBox.setAttribute('name','mismoNombre['+n+']');
	iBox.setAttribute('type','checkbox');
	iBox.setAttribute('id','fBox'+n);
	iBox.setAttribute('onClick','mismoNom('+n+')');
	iBox.setAttribute('class','input');
	pBox.appendChild(iBox);
	pBox.appendChild(tBox);*/

	/*/Agregamos los p como hijos del div
	divFile.appendChild(pNombre);*/
	divFile.appendChild(pFile);

	//Agregamos el formulario especifico
	contenedor.appendChild(divFile);
}

function delFile(){
	if (n==0) return;

	//Obtenemos el formulario de last File y su padre
	var divFile = $('File'+n);
	var contenedor = divFile.parentNode;

	//Borramos el formulario y decrementamos n
	contenedor.removeChild(divFile);
	n--;
}

function mismoNom(i){
	//Obtenemos el formulario
	var cBox = $('fBox'+i);
	var nameFile = $('fNombre'+i);

	if (cBox.checked == 1) ponerNombre(nameFile,cBox,i);
	else sacarNombre(nameFile,cBox);
}

function mismosNom() {
	var gBox = $('gBox');
	for(i=0;i<=n;i++) {
		//Obtenemos el formulario
		var cBox = $('fBox'+i);
		var nameFile = $('fNombre'+i);

		if ((gBox.checked == 1) && (cBox.checked != 1)) ponerNombre(nameFile,cBox,i);
 		else if (gBox.checked != 1) sacarNombre(nameFile,cBox);
	}
}

function ponerNombre(inNombre,cMismo,num) {
	var fileFile = $('fFile'+num);

	if (fileFile.value.length==0 ) {
		//Creamos el p y el input para el nombre
		var padre = fileFile.parentNode;
		if (padre.childNodes.length != 3) {
			pNode = document.createElement('p');
			pNode.setAttribute('style','color: red;');
			mNode = document.createTextNode('Debe seleccionar primero un fichero.');
			pNode.appendChild(mNode);
			padre.appendChild(pNode);
		}
		//Hacemos que se deseleccione el Checbox
		cMismo.checked=0;
	} else {
		var ls = fileFile.value.split('\\');
		inNombre.readOnly = true;
		inNombre.setAttribute('style','color:#CCC');
		inNombre.value = ls[ls.length-1];
		//en caso de que exista un msg de error pues lo borra
		var padre = fileFile.parentNode;
		if (padre.childNodes.length == 3)
			padre.removeChild(fileFile.nextSibling);
		//Hacemos que se seleccione el Checbox
		cMismo.checked=1;
	}
}

function sacarNombre(inNombre,cMismo) {
		//Hacemos que se deseleccione el Checbox
		cMismo.checked=0;
		//Borramos el nombre del fichero y habilitamos el input
		inNombre.value = '';
		inNombre.readOnly = false;
		inNombre.setAttribute('style','color:black');
}

function ckeckName(list, cap){
	/* var capa = document.getElementById(cap);
  	 capa.setAttribute('style','display:none');
	 nombre=list.value;
	 var posic=nombre.lastIndexOf('\\');
	 if(posic==-1){posic=nombre.lastIndexOf('/');}
	 posic=posic+1; //Para que coja la /
	 var titulo= nombre.substring(posic);
	 var filter=/^[0-9A-Za-z_]+\.[A-Za-z][A-Za-z][A-Za-z]$/;
 	 $('title').value = titulo;
	 if (filter.test(titulo))
		   return true;
		else
		  capa.setAttribute('style','display:inline');
		  */
 }

 function getNameAuthor(){
 	var author = parent.document.getElementById('name').value;
 	//alert(author);
 	if(author){
 		$('nameAuthor').value=author;
 		return true;
 	}else{
 		alert('Escriba un nombre de autor');
 		return false;
 	}

 }