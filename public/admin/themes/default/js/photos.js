//Por ahora igual que el mmanager.
var n = 0;

function addFile() {
    
	if (n==9) {
		alert('Solo puedes subir un maximo de 10 imagenes de cada vez.');
		return;
	}
	
	var divFoto, pNombre, iNombre, tNombre, pFoto, iFoto, sFoto, tFoto, pBox, iBox, tBox, dFoto, eFoto, tdFoto, teFoto;
	var o0Foto, o1Foto, o2Foto, o3Foto, o4Foto, o5Foto, o6Foto, o7Foto, o8Foto, o9Foto, o10Foto, o11Foto;
	var t0Foto, t1Foto, t2Foto, t3Foto, t4Foto, t5Foto, t6Foto, t7Foto, t8Foto, t9Foto, t10Foto, t11Foto;
	var contenedor = $('fotosContenedor');
	
	//Aumentamos n
	n++;
	
	//Creamos el div del formulario espec�fico de la foto
	divFoto = document.createElement('div');
  	divFoto.id = 'foto' + n;
  	divFoto.setAttribute('class','marcoFoto');

	//Creamos el p y el input para la imagen	
	pFoto = document.createElement('p');
	pFoto.setAttribute('style','font-weight:bold;');
	tFoto = document.createTextNode('Foto #'+n+': ');

	iFoto = document.createElement('input');
	iFoto.setAttribute('name','file['+n+']');
	iFoto.setAttribute('value','');
	iFoto.setAttribute('type','file');
	iFoto.setAttribute('id','fFile'+n);
	iFoto.setAttribute('class','boton');
	iFoto.setAttribute('size','50');
	iFoto.setAttribute('onChange','ckeckName(this,\'fileCat['+n+']\')');
/*
	dFoto = document.createElement('input');
	dFoto.setAttribute('name','descript['+n+']');
	dFoto.setAttribute('value','');
	dFoto.setAttribute('type','text');
	dFoto.setAttribute('id','descript['+n+']');	
	dFoto.setAttribute('size','36');
		
	eFoto = document.createElement('input');
	eFoto.setAttribute('name','tags['+n+']');
	eFoto.setAttribute('value','');
	eFoto.setAttribute('type','text');
	eFoto.setAttribute('id','tags['+n+']');	
	eFoto.setAttribute('size','38');
*/
	sFoto = document.createElement('div');
	sFoto.setAttribute('name','fileCat['+n+']');
	sFoto.setAttribute('id','fileCat['+n+']');
	sFoto.innerHTML= ' <table border="0" bgcolor="red"  cellpadding="4"><tr><td>El nombre es incorrecto. Contiene espacios en blanco o caracteres especiales.</td></tr></table>' ;
 	sFoto.setAttribute('style','display:none');

	tdFoto = document.createTextNode(' Descripcion:');
	teFoto = document.createTextNode(' Tags: ');

	pFoto.appendChild(tFoto);
	pFoto.appendChild(iFoto); //input file
	pFoto.appendChild(sFoto);  //div con nombre

	/*/Creamos el p y el checbox de nombre
	pBox = document.createElement('p');
	tBox = document.createTextNode(' Asignar como nombre el mismo que el de la foto.');
	iBox = document.createElement('input');
	iBox.setAttribute('name','mismoNombre['+n+']');
	iBox.setAttribute('type','checkbox');
	iBox.setAttribute('id','fBox'+n);
	iBox.setAttribute('onClick','mismoNom('+n+')');
	iBox.setAttribute('class','input');
	pBox.appendChild(iBox);
	pBox.appendChild(tBox);*/
		
	/*/Agregamos los p como hijos del div 
	divFoto.appendChild(pNombre);*/
	divFoto.appendChild(pFoto);
	//divFoto.appendChild(tdFoto);
	//divFoto.appendChild(dFoto); //input description
	//divFoto.appendChild(teFoto);
	//divFoto.appendChild(eFoto); //input tags
	//divFoto.appendChild(pBox);

	//Agregamos el formulario espec�fico
	contenedor.appendChild(divFoto);
}

function delFile(){
	if (n==0) return;
	
	//Obtenemos el formulario de la ultima foto y su padre
	var divFoto = $('foto'+n);
	var contenedor = divFoto.parentNode;
	
	//Borramos el formulario y decrementamos n
	contenedor.removeChild(divFoto);
	n--;
}

function mismoNom(i){
	//Obtenemos el formulario
	var cBox = $('fBox'+i);
	var nameFoto = $('fNombre'+i);

	if (cBox.checked == 1) ponerNombre(nameFoto,cBox,i);
	else sacarNombre(nameFoto,cBox);
}

function mismosNom() {
	var gBox = $('gBox');
	for(i=0;i<=n;i++) {
		//Obtenemos el formulario
		var cBox = $('fBox'+i);
		var nameFoto = $('fNombre'+i);
		
		if ((gBox.checked == 1) && (cBox.checked != 1)) ponerNombre(nameFoto,cBox,i);
 		else if (gBox.checked != 1) sacarNombre(nameFoto,cBox);
	}
}

function ponerNombre(inNombre,cMismo,num) {
	var fileFoto = $('fFile'+num);

	if (fileFoto.value.length==0 ) {
		//Creamos el p y el input para el nombre
		var padre = fileFoto.parentNode;
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
		var ls = fileFoto.value.split('\\');
		inNombre.readOnly = true;
		inNombre.setAttribute('style','color:#CCC');
		inNombre.value = ls[ls.length-1];
		//en caso de que exista un msg de error pues lo borra
		var padre = fileFoto.parentNode;
		if (padre.childNodes.length == 3)
			padre.removeChild(fileFoto.nextSibling);
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