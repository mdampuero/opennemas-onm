{extends file="base/admin.tpl"}


{block name="footer-js" append}
    {script_tag src="/photos.js" language="javascript"}
    <script type="text/javascript">
        try {
                // Activar la validación
                new Validation('form_upload', { immediate : true });
                Validation.addAllThese([
                        ['validate-password',
                                '{t}Your password must contain 5 characters and dont contain the word <password> or your user name.{/t}', {
                                minLength : 6,
                                notOneOf : ['password','PASSWORD','Password'],
                                notEqualToField : 'login'
                        }],
                        ['validate-password-confirm',
                                '{t}Please check your first password and check again.{/t}', {
                                equalToField : 'password'
                        }]
                ]);

                // Para activar los separadores/tabs
                $fabtabs = new Fabtabs('tabs');
        } catch(e) {
                // Escondemos los errores
                //console.log( e );
                    }
    </script>
{/block}

{block name="content"}
<form id="form_upload" name="form_upload" action="{$smarty.server.SCRIPT_NAME}" method="POST">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2> {t 1=$datos_cat[0]->title}Image manager:: Editing "%1"{/t}</h2></div>
            <ul class="old-button">
                {acl isAllowed="IMAGE_UPDATE"}
                <li>
                    <button type="submit" name="action" value="validate">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" >
                        <br />
                        {t}Save and continue{/t}
                    </button>
                </li>
                <li>
                    <button type="submit" name="action" value="update">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir"  alt="Guardar y salir" />
                        <br />
                        {t}Save{/t}
                    </button>
                </li>
                {/acl}
                <li class="separator"></li>
                <li>
                    {if !isset($smarty.request.stringSearch)}
                        <a href="{$smarty.server.PHP_SELF}?action={$smarty.session.desde}&amp;category={$smarty.session.category}" class="admin_add" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
                    {else}
                        <a href="{$smarty.const.SITE_URL_ADMIN}/controllers/search_advanced/search_advanced.php?stringSearch={$smarty.get.stringSearch}&photo=on&action=search&id=0"
                           class="admin_add" value="Cancelar" title="Cancelar">
                    {/if}
                         <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        {foreach from=$photos item=photo name=photo_show}
            {include file="image/_partials/photo_data.tpl" display='inline'}
        {/foreach}
    </div>
</form>
{/block}
