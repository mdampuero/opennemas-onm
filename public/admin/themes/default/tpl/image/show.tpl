{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/jquery/jquery-ui-sliderAccess.js"}
    {script_tag src="/onm/jquery.datepicker.js"}
    <script>
    try {
        new Validation('form_upload', { immediate : true });
        Validation.addAllThese([
            [
                'validate-password',
                '{t}Your password must contain 5 characters and dont contain the word <password> or your user name.{/t}',
                {
                    minLength : 6,
                    notOneOf : ['password','PASSWORD','Password'],
                    notEqualToField : 'login'
                }
            ],
            [
                'validate-password-confirm',
                '{t}Please check your first password and check again.{/t}',
                { equalToField : 'password' }
            ]
        ]);
    } catch(e) { }
    </script>
    <script src="http://maps.google.com/maps?file=api&amp;sensor=true&amp;key={setting name=google_maps_api_key}"></script>
{/block}
{block name="content"}
<form id="form_upload" name="form_upload" action="{url name=admin_image_update}" method="POST">
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
                        <a href="{url name=admin_images category=$photos[0]->category}" class="admin_add" title="{t}Go back{/t}">
                    {else}
                        <a href="{url name=admin_search stringSearch=$smarty.get.stringSearch} photo=on id=0"
                           title="Cancelar">
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
