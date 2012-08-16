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
