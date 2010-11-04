// JavaScript Document

// API Menu en árbol 

//---------------------------------------------------------------------------------------------//
// Objeto Menu   ------------------------------------------------------------------------------//
//---------------------------------------------------------------------------------------------//
// Constructor
function Menu(id, pathImages, persistente, carpetas, nombreCookie) {
	this.id = id;
	this.pathImages = pathImages;
	this.persistente = persistente;	
	this.carpetas = carpetas;
	this.nombreCookie = nombreCookie;
}

// Devuelve el número de carpetas existentes
Menu.prototype.getNumCarpetas = function () {
	return (this.carpetas.length);
}

// Recupera una propiedad de una carpeta dado su identificador (id) y el nombre de la propiedad (propiedad)
Menu.prototype.getPropiedadCarpeta = function (id, propiedad) {
	for(var i=0; i<this.getNumCarpetas(); i++) {
		if(carpetas[i].id==id) {
			return(eval('carpetas['+i+'].'+propiedad));
		}
	}
	return (false);
}

// Recuperar una carpeta dada
Menu.prototype.recuperarCarpeta = function (id)  {
	for(var i=0; i<this.getNumCarpetas(); i++) {
		if(this.id+'_'+this.carpetas[i].id==id) {
			return(this.carpetas[i]);
		}
	}
}

/* Desplegar/Plegar el menú */
Menu.prototype.desplegarMenu = function (id)  {
    if (document.getElementById(id)) {
        var dom = document.getElementById(id).style.display;
	var carpetas = this.recuperarCarpeta(id);
	//alert (id + "  " + carpetas);
	
        if (dom == 'none') {
            document.getElementById(id).style.display = 'inline';
            document.getElementById(id + '_sign').src   = this.pathImages + carpetas.signoExpandido;
	    	document.getElementById(id + '_folder').src = this.pathImages + carpetas.iconoExpandido;
			this.guardarMenu();
        } else {
            if (dom == 'inline') {
                document.getElementById(id).style.display = 'none';
                document.getElementById(id + '_sign').src   = this.pathImages + carpetas.signo;
		    	document.getElementById(id + '_folder').src = this.pathImages + carpetas.icono;
				this.guardarMenu();
            }
        }
    }
}

/**/
Menu.prototype.barraEstado = function (texto) {
     if(window.status != texto) {
	window.status = texto;
     } else {
	window.status = '';
     }
     
     return true;
}

//-- Metodos para trabajar con Cookies ----------------------------------------------------//
Menu.prototype.saveCookie = function (name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000))
		var expires = "; expires="+date.toGMTString()
	}
	else expires = ""
	document.cookie = name+"="+value+expires+"; path=/"
}

Menu.prototype.readCookie = function (name) {
	var nameEQ = name + "="
	var ca = document.cookie.split(';')
	for(var i=0;i<ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length)
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length)
	}
	return null
}

Menu.prototype.deleteCookie = function (name) {
	saveCookie(name,"",-1)
}

/* Guardar estado del menu */
Menu.prototype.guardarMenu = function () {

    var padre = document.getElementById(this.id);
	//alert("Discordia guardarMenu " + this.id);
    var carpetas = padre.getElementsByTagName('span'); // Personalizar la etiqueta q contiene el menú ¿?¿?
    var numCarpetas = carpetas.length;										
	/* Poner como miembro de la clase */
    var abiertas = ''; // Cadena que contendrá las carpetas '<<abiertas>>'
					   // '0 1 4 5' implica q las carpetas 0 1 4 y 5 están abiertas
	//alert("Numero de carpetas: "+numCarpetas);
    for(var i=0;i<numCarpetas;i++) {
        if(carpetas[i].style.display=='inline') {
            abiertas += i + ' ';
			//alert(abiertas);
        }
		//alert(carpetas[i].id+" estilo:"+carpetas[i].style.display);
    }
	//alert("Guardando cookie:"+abiertas);
    this.saveCookie(this.nombreCookie,abiertas,1);
}

/* Cargar el menu */
Menu.prototype.cargarMenu = function () {
    var carpetas_cookie = this.readCookie(this.nombreCookie);
	//alert("CargarMenu con cookie: >> "+carpetas_cookie+" <<");
	//alert(this.nombreCookie+" "+opens_cookie);
    if(carpetas_cookie!=null) {
        if(carpetas_cookie!='') {
            //var carpetas = document.getElementsByName('menu');
			//alert("Discordia"+this.id);
            var raiz = document.getElementById(this.id);
            var carpetas = raiz.getElementsByTagName('span');

            var abiertas = carpetas_cookie.split(' ');
            var numCarpetas = abiertas.length;
            
			//alert("CargarMenu con "+numCarpetas+" ");
            for(var i=0;i<numCarpetas;i++) {
				var carpeta = this.recuperarCarpeta(carpetas[parseInt(abiertas[i])].id);				
				var id_carpeta = carpetas[parseInt(abiertas[i])];
				// llamar a desplegar_menu()
				//alert("CargarMenu con id:"+carpetas[parseInt(abiertas[i])].id);
				id_carpeta.style.display = 'inline';
            	document.getElementById(id_carpeta.id + '_sign').src   = this.pathImages + 'minus.gif';
	    		document.getElementById(id_carpeta.id + '_folder').src = this.pathImages + carpeta.iconoExpandido;
            }
        }
    }
}

//---------------------------------------------------------------------------------------------//

// Objeto Carpeta  ----------------------------------------------------------------------------//
function Carpeta(id, texto, icono, iconoExpandido, expandida, enlace, targetEnlace, claseCSS) {
	this.id = id;
	this.icono = icono;
	this.iconoExpandido = iconoExpandido;
	this.expandida = expandida;
	this.enlace = enlace;
	this.targetEnlace = targetEnlace;
	this.claseCSS = claseCSS; // mirar dynapi y fichero css			
}
// Incorporar más metodos para trabajar con una carpeta en concreto, en principio
// utilizamos literales en vez de objetos Carpeta. ---> TESTING v 0.1

// Funcionamiento Basico ----------------------------------------------------------------------//
//var carpeta1 = new Carpeta('identificador1', 'Carpeta 1', 'icon.gif', 'icon_exp.gif', true, '#', '_self', '');
//var carpeta2 = new Carpeta('identificador2', 'Carpeta 2', 'icon.gif', 'icon_exp.gif', true, '#', '_self', '');
//var menu_carpetas = [carpeta1, carpeta2];

/*
var menu_carpetas = [ {id:"news", nombre:"Carpeta 1", icono:"tree_close.gif", iconoExpandido:"tree_open.gif", 
				       enlace:"#1", targetEnlace:"_self", claseCSS:""},
				      {id:"mydownloads", nombre:"Carpeta 2", icono:"tree_close.gif", iconoExpandido:"tree_open.gif", 
				       enlace:"#2", targetEnlace:"_self", claseCSS:""}];
var menu = new Menu('menu','images/iconos/',true,menu_carpetas,'menu1');
this.cargarMenu();*/

//---------------------------------------------------------------------------------------------//
//-->