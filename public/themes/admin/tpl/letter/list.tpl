{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript">
        function submitFilters(frm) {
            $('action').value='list';
            $('page').value = 1;

            frm.submit();
        }
        $('[rel=tooltip]').tooltip({ placement : 'bottom' });
    </script>
{/block}

{block name="content"}
<form action="{url name=admin_letters}" method="GET" name="formulario" id="formulario">
    <div class="top-action-bar clearfix" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2> {t}Letters to the Editor{/t}</h2>
            </div>
            <ul class="old-button">
               {acl isAllowed="LETTER_DELETE"}
               <li>
                    <button type="submit" class="batch-delete" data-controls-modal="modal-letter-batchDelete">
                        <img src="{$params.IMAGE_DIR}trash.png" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </button>
               </li>
               {/acl}
               {acl isAllowed="LETTER_AVAILABLE"}
               <li>
                    <button id="batch-unpublish" type="submit" name="status" value="2">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
               </li>
               <li>
                   <button id="batch-publish" type="submit" name="status" value="1">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
               </li>
               {/acl}
               {acl isAllowed="LETTER_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_letter_create}" class="admin_add" accesskey="N" tabindex="1">
                        <img src="{$params.IMAGE_DIR}list-add.png" title="Nueva" alt="Nueva"><br />{t}New letter{/t}
                    </a>
                </li>
                {/acl}

            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            <div class="pull-right form-inline">
                <div class="input-append">
                    <label>
                        {t}Status:{/t}
                        <select name="letterStatus" class="form-filters">
                            <option value="0" {if $letterStatus eq '0'}selected{/if}> {t}Pending{/t} </option>
                            <option value="1" {if $letterStatus eq '1'}selected{/if}> {t}Published{/t} </option>
                            <option value="2" {if $letterStatus eq '2'}selected{/if}> {t}Rejected{/t} </option>
                        </select>
                    </label>
                    <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
            </div>
        </div>


        <table class="table table-hover table-condensed">
            <thead>
                {if count($letters) > 0}
                <tr>
                    <th style='width:15px'>
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
                    <th>{t}Title{/t}</th>
                    <th>{t}Author{/t}</th>
                    <th style='width:110px;' class="left">{t}Date{/t}</th>
                    <th style='width:110px;'>{t}Image{/t}</th>
                    <th class="center" style='width:40px;'>{t}Available{/t}</th>
                    <th style='width:90px;' class="right">{t}Actions{/t}</th>
               </tr>
               {else}
               <tr>
                    <th>
                        &nbsp;
                    </th>
               </tr>
               {/if}
            </thead>

            <tbody>
                {foreach from=$letters item=letter}
                <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
                    <td >
                        <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$letter->id}"  style="cursor:pointer;" >
                    </td>
                    <td><span rel="tooltip" data-original-title="{$letter->body|strip_tags|clearslash}">{$letter->title}</span></td>
                    <td>{$letter->author}: {$letter->email}</td>
                    <td class="left"> {$letter->created} </td>
                    <td>
                    {if !empty($letter->image)}
                        <img src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}"
                                     title="{t}Media element (jpg, image, gif){/t}" />
                    {/if}
                    </td>
                    <td class="center">
                    {acl isAllowed="LETTER_AVAILABLE"}
                        {if $letter->content_status eq 0}
                            <a href="{url name=admin_letter_toggleavailable status=1 id=$letter->id letterStatus=$letterStatus page=$page}" title="Publicar">
                                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicar" /></a>
                            <a href="{url name=admin_letter_toggleavailable id=$letter->id status=2 letterStatus=$letterStatus page=$page}" title="Rechazar">
                                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Rechazar" /></a>
                        {elseif $letter->content_status eq 2}
                            <a href="{url name=admin_letter_toggleavailable id=$letter->id status=1 letterStatus=$letterStatus page=$page}" title="Publicar">
                                <img border="0" src="{$params.IMAGE_DIR}publish_r.png">
                            </a>
                        {else}
                            <a class="publishing" href="{url name=admin_letter_toggleavailable id=$letter->id status=2 letterStatus=$letterStatus page=$page}" title="Rechazar">
                                <img border="0" src="{$params.IMAGE_DIR}publish_g.png">
                            </a>
                        {/if}
                    {/acl}
                    </td>
                    <td class="right">
                        <div class="btn-group">
                            {acl isAllowed="LETTER_UPDATE"}
                                <a class="btn" href="{url name=admin_letter_show id=$letter->id}" title="Modificar">
                                    <i class="icon-pencil"></i>
                                </a>
                            {/acl}
                            {acl isAllowed="LETTER_DELETE"}
                                <a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                    data-url="{url name=admin_letter_delete id=$letter->id contentStatus=$contentStatus page=$page}"
                                    data-title="{$letter->title|capitalize}"
                                    href="{url name=admin_letter_delete id=$letter->id contentStatus=$contentStatus page=$page}" >
                                    <i class="icon-trash icon-white"></i>
                                </a>
                            {/acl}
                        </div>
                    </td>
                </tr>

                {foreachelse}
                <tr>
                    <td class="empty" colspan="11">
                        {t}There is no letters here.{/t}
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="13" class="center">
                        <div class="pagination">
                            {$pagination->links|default:""}
                        </div>
                    </td>
                </tr>
            </tfoot>

        </table>
    </div>

    <script>
        jQuery('#batch-unpublish, #batch-publish').on('click', function(){
            jQuery('#formulario').attr('action', "{url name=admin_letters_batchpublish}");
        });
    </script>
</form>

{include file="letter/modals/_modalDelete.tpl"}
{include file="letter/modals/_modalBatchDelete.tpl"}
{include file="letter/modals/_modalAccept.tpl"}

{/block}
