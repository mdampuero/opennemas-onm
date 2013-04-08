{extends file="base/admin.tpl"}

{block name="header-js" append}
<script type="text/javascript">
    var image_manager_urls = {
        batchDelete: '{url name=admin_images_batchdelete}'
    }
    jQuery(document).ready(function($) {
        $("img[rel]").overlay();
    });
</script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Images{/t} :: </h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if !isset($datos_cat[0]->title)}{t}All categories{/t}{elseif ($category == 2)}{t}Advertisement{/t}{else}{$datos_cat[0]->title}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        {include file="common/drop_down_categories.tpl" home="{url name=admin_images l=a}" ads=1 opinion=1}
                    </div>
                </div>
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
                        <img src="{$params.IMAGE_DIR}search.png" alt="{t}Search images{/t}"><br />{t}Search{/t}</i>
                    </a>
                </li>
                <li>
                    <a href="{url name=admin_images_statistics}">
                        <img src="{$params.IMAGE_DIR}statistics.png" alt="{t}Statistics{/t}"><br />{t}Statistics{/t}
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
{/block}
