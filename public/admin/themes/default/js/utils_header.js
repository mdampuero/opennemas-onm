

var objForm = null;
var dialogo = null;
var editores = null;

function enviar(elto, trg, acc, id) {
    var parentEl = elto.parentNode;

    while(parentEl.nodeName != "FORM") {
        parentEl = parentEl.parentNode;
    }

    parentEl.target = trg;
    parentEl.action.value = acc;
    parentEl.id.value = id;

    if(objForm != null) {
        objForm.submit();
    } else {
        parentEl.submit();
    }
}

function validateForm(formID)
{
    var checkForm = new Validation(formID, {immediate:true, onSubmit:true});
    if(!checkForm.validate()) {
        if($$('.validation-advice')) {
            if($('warnings-validation')) {
                $('warnings-validation').update('Existen campos sin cumplimentar o errores en el formulario. Por favor, revise todas las pestañas.');
                new Effect.Highlight('warnings-validation');
            }
        }
        return false;
    } else {
        if($$('.validation-advice') && $('warnings-validation')) {
            $('warnings-validation').setStyle({display: 'none'});
        }
    }
    return true;
}

function sendFormValidate(elto, trg, acc, id, formID)
{
    if(!validateForm(formID))
        return;

    enviar(elto, trg, acc, id);
}

function onSearchKeyEnter(e, elto, trg, acc, id)
{
    ekey = (document.all) ? e.keyCode : e.which;
    if (ekey==13)
    {
        return enviar(elto, trg, acc, id);
    }
}

function onSearchAdvKeyEnter(e, id, checked)
{
    ekey = (document.all) ? e.keyCode : e.which;
    if (ekey==13)
    {
        Effect.Appear('search-div2');
        return search_adv(id, $('stringSearch').value,1,checked);
    }
}

function preview(elto, trg, acc, id)
{
    this.blur();
    try { UserVoice.PopIn.show(id); return false; }
    catch(e){}

}

function confirmar(elto, id) {
    if(confirm('¿Está seguro de querer eliminar este elemento?')) {
        enviar(elto, '_self', 'delete', id);
    }
}

function confirmarDelComment(elto, id) {
    if(confirm('¿Está seguro de querer eliminar este elemento?')) {
        enviar(elto, '_self', 'delete_comment', id);
    }
}


function confirmar_hemeroteca(eleto,category, id) {
    if(confirm('¿Está seguro de enviarlo a hemeroteca?')){
        if(id==0){
            enviar2(eleto, '_self', 'mstatus', 0);
        }else{
            var ruta='article.php?id='+id+'&action=change_status&status=0&category='+category+' ';
            location.href= ruta;
        }
    }
}


function vaciar(elto, id) {
    if(confirm('¿Está seguro de quitar este elemento de la papelera?')) {
        enviar(elto, '_self', 'remove', id);
    }
}

function seleccionar_fichero(nombre_campo, tipo) {
    if(dialogo)
    {
        if(!dialogo.closed) dialogo.close();
    }

    dialogo = window.open('include/dialogo.archivo.php?campo_retorno='+nombre_campo+'&tipo_archivo='+tipo, 'dialogo', 'toolbar=no, location=no, directories=no, status=no, menub ar=no, scrollbar=no, resizable=no, copyhistory=yes, width=410, height=360, left=100, top=100, screenX=100, screenY=100');
    dialogo.focus();
}

//Operaciones multiples.
function enviar2(elto, trg, acc, id) {
    var Lista=document.getElementsByClassName('minput');
    var arreglo = $A(Lista);
    var alguno=0;
    arreglo.each(function(el, indice) {
        if(document.getElementById(el.id).checked!=false){
          alguno=1;
        }
    });

    if ((alguno != 1) && (id != 6)){
        alert("No hay ninguna noticia seleccionada");
    }else{
        if((acc=='mdelete')){
            if(id == 6){
                var res = confirm('¿Está seguro de eliminar TODOS los elementos?');
            }else{
                var res = confirm('¿Está seguro de eliminar esos elementos?');
            }
            if(res) {

                var parentEl = elto.parentNode;
                
                while(parentEl.nodeName != "FORM") {
                    parentEl = parentEl.parentNode;
                }

                parentEl.target = trg;
                parentEl.action.value = acc;
                parentEl.id.value = id;

                if(objForm != null) {
                    objForm.submit();
                } else {
                    parentEl.submit();
                }
            }
        } else {

            var parentEl = elto.parentNode;
            while(parentEl.nodeName != "FORM") {
                  parentEl = parentEl.parentNode;
            }

            parentEl.target = trg;
            parentEl.action.value = acc;
            parentEl.id.value = id;

            if(objForm != null) {
                objForm.submit();
            } else {
                parentEl.submit();
            }

        }
    }
}

//Desde papelera litter
function enviar3(elto, trg, acc, id) {
    var Lista=document.getElementsByClassName('minput');
    var arreglo = $A(Lista);
    var alguno=0;
    arreglo.each(function(el, indice) {
        if(document.getElementById(el.id).checked!=false){
          alguno=1;
        }
    });
    if (alguno != 1){
        alert("No hay ninguna elemento seleccionada");
    }else{
      if(acc=='mremove'){
        if(confirm('¿Está seguro de eliminar definitivamente esos elementos?'))
        {
            var parentEl = elto.parentNode;
            while(parentEl.nodeName != "FORM") {
                parentEl = parentEl.parentNode;
            }

            parentEl.target = trg;
            parentEl.action.value = acc;
            parentEl.id.value = id;

            if(objForm != null) {
                objForm.submit();
            } else {
                parentEl.submit();
            }
        }
      }else{
           var parentEl = elto.parentNode;
            while(parentEl.nodeName != "FORM") {
                parentEl = parentEl.parentNode;
            }

            parentEl.target = trg;
            parentEl.action.value = acc;
            parentEl.id.value = id;

            if(objForm != null) {
                objForm.submit();
            } else {
                parentEl.submit();
            }
      }
    }
}

function cancel(action,category,page) {
    if(/index_portada/.test(action)) {
        location.href ='/admin/index.php';
    }else if(/opinion/.test(action)) {
        location.href ='controllers/opinion/opinion.php';
    }else if(/advertisement/.test(action)) {
        location.href ='controllers/advertisement/advertisement.php';
    }else{
        location.href= 'article.php?action='+action+'&category='+category+'&page='+page;
    }
}

function change_att_pos(id, position, id2) {
    location.href= 'article.php?action=set_att_position&id='+id+'&position='+position+'&id2='+id2;
}

function change_pos(id, posic, category) {
    location.href= 'article.php?action=set_position&id='+id+'&posicion='+posic+'&category='+category;
}

function alert_frontpage() {
    window.alert("No puede publicar mas de 21 articulos en la Portada!");
}
