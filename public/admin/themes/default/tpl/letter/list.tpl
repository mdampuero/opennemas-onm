{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript">
        function submitFilters(frm) {
            $('action').value='list';
            $('page').value = 1;

            frm.submit();
        }
    </script>
{/block}

{block name="content"}
<form action="{$smarty.server.PHP_SELF}" method="GET" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2> {t}Letter to the Editor manager{/t} :: {t}Listing letters{/t}</h2>
            </div>
            <ul class="old-button">
               {acl isAllowed="LETTER_DELETE"}
               <li>
                    <a class="delChecked" data-controls-modal="modal-letter-batchDelete" href="#" title="{t}Delete{/t}" alt="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </a>
               </li>
               {/acl}
               {acl isAllowed="LETTER_AVAILABLE"}
               <li>
                    <button value="batchReject" name="buton-batchReject" id="buton-batchReject" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Reject{/t}
                   </button>
               </li>
               <li>
                   <button value="batchFrontpage" name="buton-batchFrontpage" id="buton-batchFrontpage" type="submit">
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

        <div class="clearfix">

            <ul class="pills">
                <li>
                    <a id="pending-tab" href="{url name=admin_letters letterStatus=0}" {if $letterStatus==0}class="active"{/if} >{t}Pending{/t}</a>
                </li>
                <li>
                    <a id="published-tab" href="{url name=admin_letters letterStatus=1}" {if $letterStatus==1}class="active"{/if}>{t}Published{/t}</a>
                </li>
                <li>
                    <a id="rejected-tab" href="{url name=admin_letters letterStatus=2}" {if $letterStatus==2}class="active"{/if}>{t}Rejected{/t}</a>
                </li>
            </ul>

        </div>

        <div id="letter">

            <table class="listing-table">
                <thead>
                    {if count($letters) > 0}
                    <tr>
                        <th style='width:15px'>
                            <input type="checkbox" id="toggleallcheckbox">
                        </th>
                        <th>{t}Title{/t}</th>
                        <th style='width:200px;'>{t}Author{/t}</th>
                        <th  style='width:110px;' class="center">{t}Date{/t}</th>
                        <th class="center">{t}Available{/t}</th>
                        <th style='width:90px;' class="center">{t}Actions{/t}</th>
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
                    {section name=c loop=$letters|default:array()}
                    <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
                        <td >
                            <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$letters[c]->id}"  style="cursor:pointer;" >
                        </td>
                        <td>{$letters[c]->title}</td>
                        <td>{$letters[c]->author}: {$letters[c]->email}</td>
                        <td class="center">
                            {$letters[c]->created}
                        </td>
                        <td class="center">
                        {acl isAllowed="LETTER_AVAILABLE"}
                            {if $letters[c]->content_status eq 0}
                                <a href="{url name=admin_letter_toggleavailable status=1 id=$letters[c]->id letterStatus=$letterStatus page=$page}" title="Publicar">
                                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicar" /></a>
                                <a href="{url name=admin_letter_toggleavailable id=$letters[c]->id status=2 letterStatus=$letterStatus page=$page}" title="Rechazar">
                                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Rechazar" /></a>
                            {elseif $letters[c]->content_status eq 2}
                                <a href="{url name=admin_letter_toggleavailable id=$letters[c]->id status=1 letterStatus=$letterStatus page=$page}" title="Publicar">
                                    <img border="0" src="{$params.IMAGE_DIR}publish_g.png">
                                </a>
                            {else}
                                <a class="publishing" href="{url name=admin_letter_toggleavailable id=$letters[c]->id status=2 letterStatus=$letterStatus page=$page}" title="Rechazar">
                                    <img border="0" src="{$params.IMAGE_DIR}publish_r.png">
                                </a>
                            {/if}
                        {/acl}
                        </td>
                        <td class="right">
                            <div class="btn-group">
                                {acl isAllowed="LETTER_UPDATE"}
                                    <a class="btn" href="{url name=admin_letter_show id=$letters[c]->id}" title="Modificar">
                                        <i class="icon-pencil"></i>
                                    </a>
                                {/acl}
                                {acl isAllowed="LETTER_DELETE"}
                                    <a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                        data-url="{url name=admin_letter_delete id=$letters[c]->id contentStatus=$contentStatus page=$page}"
                                        data-title="{$letters[c]->title|capitalize}"
                                        href="{url name=admin_letter_delete id=$letters[c]->id contentStatus=$contentStatus page=$page}" >
                                        <i class="icon-trash icon-white"></i>
                                    </a>
                                {/acl}
                            </div>
                        </td>
                    </tr>

                    {sectionelse}
                    <tr>
                        <td class="empty" colspan=10>
                            {t}There is no letters here.{/t}
                        </td>
                    </tr>
                    {/section}
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="13">
                            {$pagination->links|default:""} &nbsp;
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    <input type="hidden" name="status" id="status" value="" />
    <script>
        jQuery('#buton-batchReject').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "2");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
        jQuery('#buton-batchFrontpage').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "1");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
    </script>
</form>

{include file="letter/modals/_modalDelete.tpl"}
{include file="letter/modals/_modalBatchDelete.tpl"}
{include file="letter/modals/_modalAccept.tpl"}

{/block}
