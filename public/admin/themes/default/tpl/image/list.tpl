{extends file="base/admin.tpl"}

{block name="header-js" append}
<script type="text/javascript">
    var image_manager_urls = {
        batchDelete: '{url name=admin_images_batchdelete}'
    }
</script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>
                {t}Image manager{/t} ::
            {if $datos_cat[0]}
                {t 1=$datos_cat[0]->title}Category "%1"{/t}
            {elseif $category eq "2"}
                    {t}Category "Advertisement"{/t}
            {else}
                {t}Category "GLOBAL"{/t}
            {/if}</h2></div>
            <ul class="old-button">
                {acl isAllowed="IMAGE_DELETE"}
                <li>
                    <a href="#" class="batch-delete-button">
                        <img src="{$params.IMAGE_DIR}trash.png" alt="Eliminar"><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                <li>
                    <a href="#" class="check-all">
                        <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="{t}Select all{/t}" >
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a class="admin_add" href="{url name=admin_images_search category=$category}">
                        <img src="{$params.IMAGE_DIR}search.png" alt="{t}Search images{/t}"><br />{t}Search{/t}</i>
                    </a>
                </li>
                {acl isAllowed="IMAGE_CREATE"}
                <li>
                    <a class="admin_add" href="{url name=admin_image_new category=$category}" name="submit_mult" value="Subir Fotos">
                        <img src="{$params.IMAGE_DIR}upload.png" alt="{t}Upload{/t}"><br />{t}Upload{/t}
                    </a>
                </li>
                {/acl}
                <li>
                    <a href="{url name=admin_images_statistics}">
                        <img src="{$params.IMAGE_DIR}statistics.png" alt="{t}Statistics{/t}"><br />{t}Statistics{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        {include file="image/_partials/categories.tpl" home="{url name=admin_images l=a}"}

        {include file="image/_partials/_media_browser.tpl"}

    </div>

    <input type="hidden" id="page" name="page" value="{$page}" />
    <input type="hidden" id="category" name="category" value="{$category}" />
</form>
{include file="image/modals/_modalDelete.tpl"}
{include file="image/modals/_modalBatchDelete.tpl"}
{include file="image/modals/_modalAccept.tpl"}
{/block}
