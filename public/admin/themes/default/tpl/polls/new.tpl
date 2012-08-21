{extends file="base/admin.tpl"}
{block name="footer-js" append}
    <script type="text/javascript">
    jQuery(document).ready(function ($){
        $('#title').on('change', function(e, ui) {
            fill_tags($('#title').val(), '#metadata', '{url name=admin_utils_calculate_tags}');
        });
    });
    </script>
{/block}


{block name="content"}
<form action="{if $poll->id}{url name=admin_poll_update id=$poll->id}{else}{url name=admin_poll_create}{/if}" method="post" id="formulario">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Poll manager{/t} :: {if $poll->id}{t}Editing poll{/t}{else}{t}Creating a poll{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li>
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_polls category=$category}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        {render_messages}
        <table class="adminform" style="padding:10px;">
             <tbody>
                 <tr>
                     <td> </td><td > </td>
                     <td rowspan="6" valign="top" style="padding:4px;border:0px;">
                         <div align='center'>
                             <table style='background-color:#F5F5F5; padding:18px; width:69%;' cellpadding="8">
                                 <tr>
                                    <td style="padding:4px;" nowrap="nowrap">
                                         <label for="title">{t}Available{/t}</label>
                                    </td>
                                    <td>
                                         <select name="available" id="available" class="required">
                                            <option value="0" {if $poll->available eq 0} selected {/if}>{t}No{/t}</option>
                                            <option value="1" {if $poll->available eq 1} selected {/if}>{t}Yes{/t}</option>
                                         </select>
                                     </td>
                                 </tr>
                                 <tr>
                                     <td style="padding:4px;" nowrap="nowrap">
                                       <label for="title">{t}Favorite{/t}</label>
                                     </td>
                                     <td valign="top" style="padding:4px;" nowrap="nowrap">
                                         <select name="favorite" id="favorite" class="required">
                                            <option value="0" {if $poll->favorite eq 0} selected {/if}>{t}No{/t}</option>
                                            <option value="1" {if $poll->favorite eq 1} selected {/if}>{t}Yes{/t}</option>
                                         </select>
                                     </td>
                                 </tr>
                                 <tr>
                                 </tr>
                                 {is_module_activated name="COMMENT_MANAGER"}
                                 <tr>
                                    <td valign="top"  align="right" style="padding:4px;" >
                                        <label for="title">{t}Allow comments{/t}</label>
                                    </td>
                                    <td valign="top" style="padding:4px;" >
                                        <select name="with_comment" id="with_comment" class="required">
                                            <option value="0"  {if $poll->with_comment eq 0} selected {/if}>{t}No{/t}</option>
                                            <option value="1" {if $poll->with_comment eq 1} selected {/if}>{t}Yes{/t}</option>
                                        </select>
                                    </td>
                                </tr>
                                {/is_module_activated}
                             </table>
                         </div>
                     </td>
                 </tr>
                 <tr>
                     <td style="padding:4px;">
                         <label for="title">{t}Title{/t}</label>
                         <input 	type="text" id="title" name="title" title="Titulo de la noticia"
                                 value="{$poll->title|clearslash|escape:"html"}" class="required" size="80" />
                     </td>
                 </tr>
                 <tr>
                     <td style="padding:4px;">
                         <label for="title">{t}Subtitle{/t}</label>
                         <input 	type="text" id="subtitle" name="subtitle" title="subTitulo de la noticia"
                                 value="{$poll->subtitle|clearslash}" class="required" size="80" />
                     </td>
                 </tr>
                <tr>
                    <td>
                        <div style="display:inline-block; vertical-align:top;" valign="top">
                            <label for="title">{t}Section{/t}</label>
                            <select name="category" id="category"  >
                                {section name=as loop=$allcategorys}
                                    <option value="{$allcategorys[as]->pk_content_category}" {if $poll->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                    {section name=su loop=$subcat[as]}
                                        <option value="{$subcat[as][su]->pk_content_category}" {if $poll->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                    {/section}
                                {/section}
                            </select>
                        </div><!-- / -->

                        <div style="display:inline-block;vertical-align:top;" valign="top">
                            <label for="title">{t}Visualization{/t}</label>
                            <select name="visualization" id="visualization" class="required">
                              <option value="0" {if $poll->visualization eq 0} selected {/if}>Circular</option>
                              <option value="1" {if $poll->visualization eq 1} selected {/if}>Barras</option>
                            </select>
                        </div><!-- / -->
                    </td>
                </tr>
                <tr>
                    <td style="padding:4px;">
                        <label for="title">{t}Keywords{/t}</label>
                        <input 	type="text" id="metadata" name="metadata"
                                value="{$poll->metadata|clearslash}" class="required" size="80" />
                    </td>
                </tr>
                <tr>
                     <td valign="top">
                        <label for="title">{t}Answers{/t}</label>
                        <a onClick="add_item_poll({$num})" class="btn">
                            <i class="icon-plus"></i>
                            {t}Add new answer{/t}
                        </a>
                        <div id="items" name="items"></div>
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="padding:4px;" nowrap="nowrap">
                    {assign var='num' value='0'}
                    {section name=i loop=$items}
                        <div id="item{$smarty.section.i.iteration}" class="marcoItem" style='display:inline;'>
                            <a onclick="del_this_item('item{$smarty.section.i.iteration}')">
                                <img src="{$params.IMAGE_DIR}del.png" />
                            </a>
                            <input type="text" name="item[{$smarty.section.i.iteration}]" value="{$items[i].item}" id="item[{$smarty.section.i.iteration}]" size="45"/>
                            <input type="hidden" readonly name="votes[{$smarty.section.i.iteration}]" value="{$items[i].votes}" id="votes[{$smarty.section.i.iteration}]"/>
                            <small>{t}Votes{/t}:  {$items[i].votes} / {$poll->total_votes}</small>

                        </div>
                        {assign var='num' value=$smarty.section.i.iteration}
                    {/section}
                     </td>
                 </tr>
             </tbody>
             </table>

         </div>


        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />


    </form>
</div>
{/block}
