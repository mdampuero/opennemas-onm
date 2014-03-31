{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
{/block}

{block name="footer-js" append}
    <script>
    var book_manager_urls = {
        batchDelete: '{url name=admin_books_batchdelete category=$category page=$page}',
        savePositions: '{url name=admin_books_save_positions category=$category page=$page}',
        batch_publish: '{url name=admin_books_batchpublish new_status=1}',
        batch_unpublish: '{url name=admin_books_batchpublish new_status=0}'
    }

    jQuery('#save-positions').on('click', function(e, ui){
        e.preventDefault();

        var items_id = [];
        jQuery( "tbody.sortable tr" ).each(function(){
            items_id.push(jQuery(this).data("id"))
        });

        jQuery.ajax(book_manager_urls.savePositions, {
           type: "POST",
           data: { positions : items_id }
        }).done(function( msg ){

               jQuery('#warnings-validation').html("<div class=\"success\">"+msg+"</div>")
                                             .effect("highlight", { }, 3000);

       });
        return false;
    });

    </script>
{/block}

{block name="content"}
<form action="{url name="admin_books"}" method="get" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Books{/t}</h2>
            </div>

            <ul class="old-button">
                {acl isAllowed="BOOK_DELETE"}
                <li>
                    <button type="submit" href="#" data-controls-modal="modal-book-batchDelete" title="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </button>
                </li>
                {/acl}
                {acl isAllowed="BOOK_AVAILABLE"}
                <li>
                    <button id="batch-unpublish" type="submit" name="status" value="0">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
               </li>
               <li>
                   <button id="batch-publish" type="submit" name="status" value="1">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
               </li>
                {/acl}
                {acl isAllowed="BOOK_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_books_create category=$category}"  title="{t}New book{/t}">
                        <img src="{$params.IMAGE_DIR}/book.gif" alt="{t}New book{/t}"><br />{t}New book{/t}
                    </a>
                </li>
                {/acl}
                {if $page <= 1}
                <li class="separator"></li>
                <li>
                    <a href="#" id="save-positions" title="{t}Save positions{/t}">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save positions{/t}"><br />{t}Save positions{/t}
                    </a>
                </li>
                {/if}
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <ul class="pills clearfix">
            <li>
                <a href="{url name=admin_books_widget}" {if $category == 'widget'}class="active"{elseif $ca eq $datos_cat[0]->fk_content_category}{*class="active"*}{/if}>WIDGET HOME</a>
            </li>
            <li>
                <a href="{url name=admin_books}" {if $category == 'all'}class="active"{elseif $ca eq $datos_cat[0]->fk_content_category}{*class="active"*}{/if}>All categories</a>
            </li>
        </ul>

        <div id="warnings-validation"></div>

        <div class="table-info clearfix">
            <div>
                <div class="right form-inline">
                    <label>{t}Status:{/t}
                    <select name="status" class="form-filters" {if $category == 'widget'} disabled="disabled"{/if}>
                        <option value="" {if  !isset($status)}selected{/if}> {t}All{/t} </option>
                        <option value="0" {if $status eq '0'}selected{/if}> {t}Unpublished{/t} </option>
                        <option value="1" {if $status eq '1'}selected{/if}> {t}Published{/t} </option>
                    </select>
                    <input type="hidden" id="new_status" name="new_status" value="" />
                    </label>

                    <div class="input-append">
                        <label for="category">
                            {t}Category:{/t}
                            <select name="category" class="form-filters">
                                <option value="all" {if $category eq '0'}selected{/if}> {t}All{/t} </option>
                                {section name=as loop=$allcategorys}
                                     <option value="{$allcategorys[as]->pk_content_category}" {if isset($category) && ($category eq $allcategorys[as]->pk_content_category)}selected{/if}>{$allcategorys[as]->title}</option>
                                     {section name=su loop=$subcat[as]}
                                            {if $subcat[as][su]->internal_category eq 1}
                                                <option value="{$subcat[as][su]->pk_content_category}"
                                                {if $category eq $subcat[as][su]->pk_content_category || $article->category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                            {/if}
                                        {/section}
                                {/section}
                            </select>
                        </label>
                        <button type="submit" id="search" class="btn"><i class="icon-search"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th style="width:15px;">
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
                    <th class="title">{t}Title{/t}</th>
                    <th style="width:65px;" class="center">{t}Section{/t}</th>
                    <th class="center" style="width:100px;">Created</th>
                    <th class="center" style="width:10px;">{t}Published{/t}</th>
                    <th class="center" style="width:10px;">{t}Favorite{/t}</th>
                    <th class="right" style="width:110px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody class="sortable">
            {foreach $books as $book}
            <tr data-id="{$book->pk_book}" style="cursor:pointer;">
                <td class="center">
                    <input type="checkbox" class="minput"  id="selected_{$book->id}" name="selected_fld[]" value="{$book->id}"  style="cursor:pointer;" >
                </td>
                <td>
                    <a href="{url name=admin_book_show id=$book->pk_book}" title="{$book->title|clearslash}">
                        {$book->title|clearslash}
                    </a>
                </td>
                <td class="center">
                    {$book->category_title}
                </td>
                <td class="center">
                    {$book->created}
                </td>
                <td class="center">
                    {acl isAllowed="BOOK_AVAILABLE"}
                        {if $book->available == 1}
                            <a href="{url name=admin_books_toggle_available id=$book->id status=0 category=$category page=$page|default:1}" title="{t}Published{/t}">
                                <img src="{$params.IMAGE_DIR}publish_g.png" alt="{t}Published{/t}" />
                            </a>
                        {else}
                            <a href="{url name=admin_books_toggle_available id=$book->id status=1 category=$category page=$page|default:1}" title="{t}Pendiente{/t}">
                                <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Pendiente{/t}" />
                            </a>
                        {/if}
                    {/acl}
                </td>

                <td class="center">
                    {acl isAllowed="BOOK_AVAILABLE"}
                        {if $book->in_home == 1}
                           <a href="{url name=admin_books_toggle_inhome id=$book->id status=0 category=$category page=$page|default:1}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="{url name=admin_books_toggle_inhome id=$book->id status=1 category=$category page=$page|default:1}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    {/acl}
                </td>
                <td class="right">
                    <div class="btn-group">
                       {acl isAllowed="BOOK_UPDATE"}
                            <a class="btn"  href="{url name=admin_book_show id=$book->pk_book}" title="{t}Edit book{/t}" >
                               <i class="icon-pencil"></i> {t}Edit{/t}
                            </a>
                       {/acl}

                       {acl isAllowed="BOOK_DELETE"}
                            <a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                data-id="{$book->pk_book}"
                                data-title="{$book->title|capitalize}"
                                data-url="{url name=admin_books_delete id=$book->id}"
                                href="{url name=admin_books_delete id=$book->id}" >
                                <i class="icon-trash icon-white"></i>
                            </a>
                       {/acl}
                    </div>
                </td>

            </tr>
            {foreachelse}
            <tr>
                <td class="empty" colspan="9">{t}There is no books yet{/t}</td>
            </tr>
            {/foreach}
        </tbody>
        <tfoot>
          <td colspan="9">
            <div class="pagination" class="center">
                {$pagination->links|default:""}
            </div>
          </td>
        </tfoot>
    </table>

    </div>
</form>
 <script>
    // <![CDATA[
        jQuery('#batch-publish').on('click', function(e){
            e.preventDefault();
            jQuery('#formulario').attr('action', book_manager_urls.batch_publish);
            $('#new_status').val($(this).val());
            jQuery('#formulario').submit();
        });
        jQuery('#batch-unpublish').on('click', function(e){
            e.preventDefault();
            jQuery('#formulario').attr('action', book_manager_urls.batch_unpublish);
            $('#new_status').val($(this).val());
            jQuery('#formulario').submit();
        });


        jQuery(document).ready(function() {
            makeSortable();
        });

    // ]]>

</script>

{include file="book/modals/_modalDelete.tpl"}
{include file="book/modals/_modalBatchDelete.tpl"}
{include file="book/modals/_modalAccept.tpl"}

{/block}
