{if $terms_accepted < 1}
<style>
    #modal-terms-accept .modal-footer {
        text-align:left;
    }
    #modal-terms-accept .accept {
        margin-top:4px;
    }
    label {
        display:inline-block;
    }
</style>
<div class="modal hide fade" id="modal-terms-accept">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Terms of Service Agreement{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}In order to use the Opennemas platform you must read and accept our terms of use. You can find these terms in the next link <a href="http://help.opennemas.com/knowledgebase/articles/235418-terminos-de-uso-de-opennemas" title="Terms of use in Opennemas">Terms of use in Opennemas</a>{/t}</p>
    </div>
    <div class="modal-footer">
            <input type="checkbox" id="checkbox-accept" name="accept" value="1">
        <label for="checkbox-accept">
            {t escape=off}I have read and accept the Opennemas <a href="http://help.opennemas.com/knowledgebase/articles/235418-terminos-de-uso-de-opennemas" title="Terms of use in Opennemas">Terms of use</a> and <a href="http://help.opennemas.com/knowledgebase/articles/235300-opennemas-pol%C3%ADtica-de-privacidad">Privacy policy</a>.{/t}
        </label>

        <a class="btn btn-primary accept left" id="accept" href="#">{t}Yes, accept{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-terms-accept").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: false, //Can close on escape
    show: true
});
jQuery('#modal-terms-accept a.btn.accept').on('click', function(e){
    e.preventDefault();

    if ($('#checkbox-accept').attr('checked') === 'checked') {
        $.ajax('{url name="admin_welcome_accept_terms"}', { 'accept' : true });
        jQuery("#modal-terms-accept").modal('hide');
    } else {
        $('#modal-terms-accept .modal-footer label').css({ 'color' : 'Red' })
    };
});
</script>
{/if}
