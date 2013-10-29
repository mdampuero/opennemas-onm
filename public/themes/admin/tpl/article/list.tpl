{extends file="base/admin.tpl"}
{block name="header-js" append}
    <script>
        var article_manager_urls = {
            batch_delete: '{url name=admin_articles_batchdelete category=$category page=$page}',
            batch_publish: '{url name=admin_articles_batchpublish new_status=1}',
            batch_unpublish: '{url name=admin_articles_batchpublish new_status=0}',
        }
    </script>
    {script_tag src="/onm/jquery-functions.js" language="javascript"}

{/block}

{block name="content"}
<form action="{url name=admin_articles}" method="GET" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Articles{/t} :: </h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if !isset($datos_cat[0]->title) || ($category == 0)}{t}All categories{/t}{else}{$datos_cat[0]->title}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        {include file="common/drop_down_categories.tpl" home="{url name=admin_articles l=1 status=$status}"}
                    </div>
                </div>
            </div>
            <ul class="old-button">
                {acl isAllowed="ARTICLE_DELETE"}
                <li>
                    <a class="delChecked" data-controls-modal="modal-article-batchDelete" href="#" title="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ARTICLE_AVAILABLE"}
                <li class="batch-actions">

                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" id="batch-publish">
                                {t}Batch publish{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish">
                                {t}Batch unpublish{/t}
                            </a>
                        </li>
                        <!-- <li>
                            <a href="#" id="batch-delete">
                                {t}Batch delete{/t}
                            </a>
                        </li> -->
                    </ul>
                </li>

                {/acl}
                <li class="separator"></li>
                {acl isAllowed="ARTICLE_CREATE"}
                <li>
                    <a href="{url name=admin_article_create category=$category}">
                        <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New article{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            {acl hasCategoryAccess=$category}<div class="pull-left"><strong>{t 1=$totalArticles}%1 articles{/t}</strong></div> {/acl}
            <div class="pull-right">
                <div class="form-inline">
                    <input type="text" placeholder="{t}Search by title:{/t}" name="title" value="{$title}"/>
                    {t}Status:{/t}
                    <div class="input-append">
                        <select name="status">
                            <option value="-1" {if $status === -1} selected {/if}> {t}-- All --{/t} </option>
                            <option value="1" {if  $status === 1} selected {/if}> {t}Published{/t} </option>
                            <option value="0" {if $status === 0} selected {/if}> {t}No published{/t} </option>
                        </select>
                        <button type="submit" class="btn"><i class="icon-search"></i> </button>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-hover table-condensed">
            <thead>
                <th style="width:15px;"><input type="checkbox" class="toggleallcheckbox"></th>
                <th class="left" >{t}Title{/t}</th>
                {if $category eq 'all' || $category == 0}
                    <th class="left">{t}Section{/t}</th>
                {/if}
                <th  class="left" style="width:80px;">{t}Author{/t}</th>
                <th class="center" style="width:130px;">{t}Created{/t}</th>
                <th class="center" style="width:80px;">{t}Last Editor{/t}</th>
                <th class="center" style="width:10px;">{t}Available{/t}</th>
                <th class="center" style="width:70px;">{t}Actions{/t}</th>
            </thead>
            {*acl hasCategoryAccess=$category*}
            <tbody>
            {foreach name=c from=$articles item=article}
             {acl hasCategoryAccess=$article->category}
                <tr>
                    <td>
                        <input type="checkbox" class="minput"  id="selected_{$smarty.foreach.c.iteration}" name="selected_fld[]" value="{$article->id}"  style="cursor:pointer;" />
                    </td>
                    <td class="left" >
                        <span  rel="tooltip" data-original-title="{t 1=$article->editor}Last author: %1{/t}">{$article->title|clearslash}</span>
                    </td>
                    {if $category eq 'all' || $category == 0}
                    <td class="left">
                        {if $article->category_name == 'unknown'}
                            {t}Unasigned{/t}
                        {else}
                            {$article->category_name|upper|clearslash}
                        {/if}
                    </td>
                    {/if}
                    <td class="left">
                        {if $article->fk_author neq 0}
                            {$article->author}
                        {elseif $article->agency neq ''}
                            {$article->agency}
                        {else}
                            {$article->editor}
                        {/if}
                    </td>
                    <td class="center">{$article->created}</td>
                    <td class="center">{$article->editor}</td>
                    <td class="center">
                        {if !empty($article->category) && $article->category != 20}
                        {acl isAllowed="ARTICLE_AVAILABLE"}
                        {if $article->available == 1}
                            <a href="{url name=admin_article_toggleavailable id=$article->id status=0 redirectstatus=$status category=$category page=$page}" title="Publicado">
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                            </a>
                        {else}
                            <a href="{url name=admin_article_toggleavailable id=$article->id status=1 redirectstatus=$status category=$category page=$page}" title="Pendiente">
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                            </a>
                        {/if}
                        {/acl}
                        {/if}
                    </td>
                    <td class="right">
                        <div class="btn-group">
                            <a class="btn" href="{url name=admin_article_show id=$article->id}" title="{t}Edit{/t}">
                                <i class="icon-pencil"></i>
                            </a>

                            <a class="del btn btn-danger"
                                data-title="{$article->title|escape}"
                                data-url="{url name=admin_article_delete id=$article->id category=$category page=$page title=$title status=$status}"
                                href="{url name=admin_article_delete id=$article->id category=$category page=$page title=$title status=$status}">
                                <i class="icon-trash icon-white"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                {/acl}
            {foreachelse}
                <tr>
                    <td class="empty" colspan="10">{t}No available articles.{/t}</td>
                </tr>
            {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <div class="pagination">{$pagination->links}</div>
                    </td>
                </tr>
            </tfoot>
            {*/acl*}
        </table>

    </div>
    <input type="hidden" name="category" value="{$category}">
</form>
{include file="article/modals/_modalDelete.tpl"}
{include file="article/modals/_modalBatchDelete.tpl"}
{/block}


{block name="footer-js" append}
    <script>
    jQuery(document).ready(function ($){
        $('[rel="tooltip"]').tooltip();

        $('.minput').on('click', function() {
            checkbox = $(this).find('input[type="checkbox"]');
            var checked_elements = $('.table tbody input[type="checkbox"]:checked').length;
            if (checked_elements > 0) {
                $('.old-button .batch-actions').fadeIn('fast');
            } else {
                $('.old-button .batch-actions').fadeOut('fast');
            }
        });

        var form = $('#formulario');

        $('#batch-publish').on('click', function (e, ui) {
            e.preventDefault();
            $.get(
                article_manager_urls.batch_publish,
                form.serializeArray()
            ).done(function(data) {
                window.location.href = '{url name=admin_articles}';
            }).fail(function(data) {
            });
        });

        $('#batch-unpublish').on('click', function (e, ui) {
            e.preventDefault();
            $.get(
                article_manager_urls.batch_unpublish,
                form.serializeArray()
            ).done(function(data) {
                window.location.href = '{url name=admin_articles}';
            }).fail(function(data) {
            });
        });

        $('#batch-delete').on('click', function (e, ui) {
            e.preventDefault();
            $.get(
                article_manager_urls.batch_delete,
                form.serializeArray()
            ).done(function(data) {
                window.location.href = '{url name=admin_articles}';
            }).fail(function(data) {
            });
        });
    });
    </script>
{/block}
