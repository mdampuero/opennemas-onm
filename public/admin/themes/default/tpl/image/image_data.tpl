{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/mediamanager.css"}
{/block}


{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/jquery/jquery-ui-sliderAccess.js"}
    {script_tag src="/onm/jquery.datepicker.js"}
{/block}

{block name="footer-js" append}
    {script_tag src="/photos.js"}
    {if isset($smarty.request.message) && strlen($smarty.request.message) > 0}
        <div class="message" id="console-info">{$smarty.request.message}</div>
        <script defer="defer" type="text/javascript">
            new Effect.Highlight('console-info', {ldelim}startcolor:'#ff99ff', endcolor:'#999999'{rdelim})
        </script>
    {/if}

    <script defer="defer" type="text/javascript">
    function confirmar(url) {
        if(confirm('¿Está seguro de querer eliminar este fichero?')) {
            location.href = url;
        }
    }
    </script>

    {if !empty($smarty.request.alerta)}
    <script type="text/javascript">
        alert("NO SE PUEDE ELIMINAR {$smarty.request.name} .\n Esta imagen está siendo utilizada en: {$smarty.request.alerta}.");
    </script>
    {/if}
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
<form id="form_upload" name="form_upload" action="{$smarty.server.SCRIPT_NAME}?action=updateDatasPhotos" method="POST">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2> {t 1=$datos_cat[0]->title}Image manager:: Editing "%1"{/t}</h2></div>
            <ul class="old-button">
                {acl isAllowed="IMAGE_UPDATE"}
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$photo1->id}', 'form_upload');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self', 'updateDatasPhotos', '{$photo1->id}');">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir"  alt="Guardar y salir" />
                        <br />
                        {t}Save{/t}
                    </a>
                </li>
                {/acl}
                <li class="separator"></li>
                <li>
                    {if !isset($smarty.request.stringSearch)}
                        <a href="{$smarty.server.PHP_SELF}?action={$smarty.session.desde}&category={$smarty.request.category}" class="admin_add" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
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

        {include file="image/_partials/photo_data.tpl" display='inline'}

        <input type="hidden" name="category" value="{$photo1->category}" />

    </div>
<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}
