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
            <div class="title">
                <h2>{t}Images{/t}</h2>
            </div>
            <ul class="old-button">
                {acl isAllowed="IMAGE_DELETE"}
                <li>
                    <a href="#" class="batch-delete-button">
                        <img src="{$params.IMAGE_DIR}trash.png" alt="Eliminar"><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="IMAGE_CREATE"}
                <li>
                    <a class="admin_add" href="{url name=admin_image_new category=$category}">
                        <img src="{$params.IMAGE_DIR}upload.png" alt="{t}Upload{/t}"><br />{t}Upload{/t}
                    </a>
                </li>
                {/acl}
                <li class="separator"></li>
                <li>
                    <a class="admin_add" href="{url name=admin_images_search category=$category}">
                        <img src="{$params.IMAGE_DIR}search.png" alt="{t}Search images{/t}"><br />{t}Search{/t}
                    </a>
                </li>
                {*<li class="separator"></li>
                <li>
                    <a href="{url name=admin_images_config}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt=""><br>
                        {t}Settings{/t}
                    </a>
                </li> *}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        {include file="image/_partials/_media_browser.tpl"}

    </div>

    <input type="hidden" id="page" name="page" value="{$page}" />
    <input type="hidden" id="category" name="category" value="{$category}" />
</form>
{include file="image/modals/_modalDelete.tpl"}
{include file="image/modals/_modalBatchDelete.tpl"}
{include file="image/modals/_modalAccept.tpl"}

<script>
jQuery(".simple_overlay").modal({
    backdrop: true, //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});
jQuery('.table').on('click', '.image-preview', function(e){
    var image = $(this);
    var rel_target = image.attr('rel');

    $(rel_target).modal('show');
    e.preventDefault();
});
</script>
{/block}
