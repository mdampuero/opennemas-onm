{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EFE importer{/t} :: {t}Article information{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=import&id={$element->xmlFile}" class="admin_add" value="{t}Import{/t}" title="{t}Import{/t}">
                <img border="0" src="{$params.IMAGE_DIR}archive_no.png" title="{t}Import{/t}" alt="{t}Import{/t}" ><br />{t}Import{/t}
                </a>
            </li>
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=list" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
   <form action="{$smarty.server.PHP_SELF}" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>

   <div id="{$category|default:""}">

        <table class="adminheading">
	    <tr>
            <th align="left">&nbsp;</th>
	    </tr>
	</table>

	<table class="adminform" border=0>
        <tbody>
    	    <tr>
                <td>
                    <div id="{$element->id}" style="width:70%; margin:0 auto; margin-top:5px; font-size:13px;">
                        <fieldset>
                            <legend>{t}Basic information{/t}</legend>
                            {if $element->texts[0]->pretitle}
                            <p>
                                <label>{t}Pretitle:{/t}</label>
                                {$element->texts[0]->pretitle}
                            </p>
                            {/if}
                            <p>
                                <label>{t}Title:{/t}</label>
                                {$element->title}
                            </p>
                            
                            <p>
                                <label>{t}Priority:{/t}</label>
                                {$element->priority}
                            </p>

                            <p>
                                <strong>{t}Date:{/t}</strong> {$element->created_time->format("H:i:s d-m-Y")}
                            </p>
                            {if $element->texts[0]->summary}
                            <p>
                                <strong>{t}Summary:{/t}</strong> <br/>
                                {$element->texts[0]->summary}
                            </p>
                            {/if}
                        </fieldset>

                        <fieldset>
                            <legend>{t}Main information{/t}</legend>
                            <p>
                                {$element->texts[0]->body}
                            </p>
                        </fieldset>

                        <fieldset>
                            <legend>{t}Attached contents{/t}</legend>

                            {if count($element->photos) > 0}
                            <p>
                                <strong>{t}Photos:{/t}</strong> <br/>
                                <ul>
                                {foreach from=$element->photos item=photo}
                                    <li>{$photo->title}</li>
                                {/foreach}
                                </ul>
                            </p>
                            {/if}
                            {if count($element->videos) > 0}
                            <p>
                                <strong>{t}Videos:{/t}</strong> <br/>
                                <ul>
                                {foreach from=$element->Videos item=video}
                                    <li>{$video}</li>
                                {/foreach}
                                </ul>
                            </p>
                            {/if}
                            {if count($element->files) > 0}
                            <p>
                                <strong>{t}Files:{/t}</strong> <br/>
                                <ul>
                                {foreach from=$element->files item=doc}
                                    <li>{$file}</li>
                                {/foreach}
                                </ul>
                            </p>
                            {/if}
                            {if count($element->moddocs) > 0}
                            <p>
                                <strong>{t}Documentary modules:{/t}</strong> <br/>
                                <ul>
                                {foreach from=$element->moddocs item=doc}
                                    <li>{$doc}</li>
                                {/foreach}
                                </ul>
                            </p>
                            {/if}
                        </fieldset>
                    </div>
                </td>
            </tr>
            </tbody>

            <tfoot>
             <tr class="pagination" >
                 <td colspan="13" align="center">&nbsp;</td>
             </tr>
            </tfoot>

	   </table>
   </div>

   <input type="hidden" id="action" name="action" value="list" />
   </form>
</div>
{/block}
