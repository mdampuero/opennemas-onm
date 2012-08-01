var objForm = null;

function enviar(elto, trg, acc, id) {
    var parentEl = elto.parentNode;

    while (parentEl.nodeName != "FORM") {
        parentEl = parentEl.parentNode;
    }

    parentEl.target = trg;
    parentEl.action.value = acc;
    parentEl.id.value = id;

    if (objForm !== null) {
        objForm.submit();
    } else {
        parentEl.submit();
    }
}

function validateForm(formID)
{
    var checkForm = new Validation(formID, {immediate:true, onSubmit:true});
    if(!checkForm.validate()) {
        if(jQuery('.validation-advice')) {
            if (jQuery('#warnings-validation')) {
                jQuery('#warnings-validation').html('Existen campos sin cumplimentar o errores en el formulario. Por favor, revise todas las pestañas.');
            }
        }
        return false;
    } else {
        if (jQuery('.validation-advice') && jQuery('#warnings-validation')) {
            jQuery('#warnings-validation').html('');
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
    if (confirm('¿Está seguro de enviarlo a hemeroteca?')){
        if(id === 0){
            enviar2(eleto, '_self', 'mstatus', 0);
        }else{
            var ruta='article.php?id='+id+'&action=change_status&status=0&category='+category+' ';
            location.href= ruta;
        }
    }
}


//Operaciones multiples.
function enviar2(elto, trg, acc, id) {
    var Lista=document.getElementsByClassName('minput');
    var arreglo = $A(Lista);
    var alguno=0;
    arreglo.each(function(el, indice) {
        if (document.getElementById(el.id).checked !== false){
          alguno=1;
        }
    });

    if ((alguno != 1) && (id != 6)){
        alert("No hay ninguna noticia seleccionada");
    } else {
        var parentEl;

        if ((acc=='mdelete') || (acc=='mremove')){
            var res;
            if(id == 6){
                res = confirm('¿Está seguro de eliminar TODOS los elementos?');
            }else{
                res = confirm('¿Está seguro de eliminar esos elementos?');
            }

            if (res) {

                parentEl = elto.parentNode;

                while(parentEl.nodeName != "FORM") {
                    parentEl = parentEl.parentNode;
                }

                parentEl.target = trg;
                parentEl.action.value = acc;
                parentEl.id.value = id;

                if (objForm !== null) {
                    objForm.submit();
                } else {
                    parentEl.submit();
                }
            }
        } else {
            parentEl = elto.parentNode;
            while (parentEl.nodeName != "FORM") {
                  parentEl = parentEl.parentNode;
            }

            parentEl.target       = trg;
            parentEl.action.value = acc;
            parentEl.id.value     = id;

            if (objForm !== null) {
                objForm.submit();
            } else {
                parentEl.submit();
            }
        }
    }
}
