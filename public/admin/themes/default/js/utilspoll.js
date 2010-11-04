
var n = 0;

function add_item_poll(num) {
if(n<num){
	n=num;
}
	var contenedor = $('items');

	//Aumentamos n
	n++

	divItem = document.createElement('div');
  	divItem.id = 'divitem' + n;
  	divItem.setAttribute('class','marcoItem');

	//Creamos el p y el input para la imagen
	pItem = document.createElement('p');
	pItem.setAttribute('style','font-weight:bold;');
	tItem = document.createTextNode('Item #'+n+': ');

	dItem = document.createElement('input');
	dItem.setAttribute('name','item['+n+']');
	dItem.setAttribute('value','');
	dItem.setAttribute('type','text');
	dItem.setAttribute('id','item['+n+']');
	dItem.setAttribute('size','60');

/*	eItem = document.createElement('input');
	eItem.setAttribute('name','tags['+n+']');
	eItem.setAttribute('value','');
	eItem.setAttribute('type','text');
	eItem.setAttribute('id','tags['+n+']');
	eItem.setAttribute('size','40');
	*/
	tdItem = document.createTextNode(' Item: ');
	//teItem = document.createTextNode(' Tags: ');

	pItem.appendChild(tItem);

	divItem.appendChild(pItem);
	divItem.appendChild(tdItem);
	divItem.appendChild(dItem); //input respuesta
//	divItem.appendChild(teItem);
//	divItem.appendChild(eItem); //input tags


	contenedor.appendChild(divItem);
}

function del_item_poll(){
	if (n==0) return;

	//Obtenemos el formulario de la ultima foto y su padre
	var divItem = $('divitem'+n);
	var contenedor = divItem.parentNode;

	//Borramos el formulario y decrementamos n
	contenedor.removeChild(divItem);
	n--;
}


function del_this_item(div){

	//$(div).setAttribute('style','display:none;');
	var divItem = $(div);
	var contenedor = divItem.parentNode;

	//Borramos el formulario y decrementamos n
	contenedor.removeChild(divItem);

}
