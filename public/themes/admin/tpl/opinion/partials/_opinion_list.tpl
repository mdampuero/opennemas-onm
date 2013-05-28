<div class="table-info clearfix">
    <div class="pull-left"><strong>{t 1=$total}%1 opinions{/t}</strong></div>
    <div class="pull-right form-inline">
        {t}Status:{/t}
        <select name="status">
            <option value="-1" {if $status === -1} selected {/if}> {t}-- All --{/t} </option>
            <option value="1" {if  $status === 1} selected {/if}> {t}Published{/t} </option>
            <option value="0" {if $status === 0} selected {/if}> {t}No published{/t} </option>
        </select>
         &nbsp;
        {t}Select an author{/t}
        <div class="input-append">
            <select name="author" id="author">
                <option value="0" {if isset($author) && $author eq "0"} selected {/if}> {t}All authors{/t} </option>
                <option value="-1" {if isset($author) && $author eq "-1"} selected {/if}> {t}Director{/t} </option>
                <option value="-2" {if isset($author) && $author eq "-2"} selected {/if}> {t}Editorial{/t} </option>
                {section name=as loop=$autores}
                    <option value="{$autores[as]->pk_author}" {if isset($author) && $author == $autores[as]->pk_author} selected {/if}>{$autores[as]->name}</option>
                {/section}
            </select>
            <button type="submit" class="btn"><i class="icon-search"></i></button>
        </div>
    </div>
</div>
<table class="table table-hover table-condensed">
	<thead>
		<tr>
			<th style="width:15px;"><input type="checkbox" class="toggleallcheckbox"></th>
			<th>{t}Author name{/t} - {t}Title{/t}</th>
            <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
			<th class="center" style="width:110px;">{t}Created in{/t}</th>
			<th class="center" style="width:70px;">{t}In home{/t}</th>
			<th class="center" style="width:20px;">{t}Published{/t}</th>
            <th class="center" style="width:20px;">{t}Favorite{/t}</th>
			<th class="center" style="width:70px;">{t}Actions{/t}</th>
	  </tr>
	</thead>
	<tbody>
		{foreach from=$opinions item=opinion name=c}
		<tr>
			<td>
				<input type="checkbox" class="minput"  id="selected_{$smarty.foreach.c.iteration}" name="selected_fld[]" value="{$opinion->id}">
			</td>
			<td>
                <a href="{url name=admin_opinion_author_show id=$opinion->author->pk_author}">
                    {$opinion->author->name}
                </a>
                -
				<a href="{url name=admin_opinion_show id=$opinion->id}" title="Modificar">
					{$opinion->title|clearslash}
                </a>
			</td>
			<td class="center">
				{$opinion->views}
			</td>
			<td class="center">
				{$opinion->created}
			</td>
			<td class="center">
                {acl isAllowed="OPINION_FRONTPAGE"}
                {if $opinion->in_home == 1}
                <a href="{url name=admin_opinion_toggleinhome id=$opinion->id status=0 type=$type page=$page}" class="no_home" title="Sacar de portada" ></a>
                {else}
                <a href="{url name=admin_opinion_toggleinhome id=$opinion->id status=1 type=$type page=$page}" class="go_home" title="Meter en portada" ></a>
                {/if}
                {/acl}
			</td>
			<td class="center">
                {acl isAllowed="OPINION_AVAILABLE"}
				{if $opinion->content_status == 1}
					<a href="{url name=admin_opinion_toggleavailable id=$opinion->id status=0  type=$type page=$page}" title="Publicado">
						<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                    </a>
				{else}
					<a href="{url name=admin_opinion_toggleavailable id=$opinion->id status=1  type=$type page=$page}" title="Pendiente">
						<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                    </a>
				{/if}
                {/acl}
			</td>
            <td class="center">
                {acl isAllowed="OPINION_ADMIN"}
                {if $opinion->favorite == 1 && $opinion->type_opinion == 0}
                <a href="{url name=admin_opinion_togglefavorite id=$opinion->id status=0  type=$type page=$page}" class="favourite_on" title="{t}Favorite{/t}">
                    &nbsp;
                </a>
                {elseif $opinion->type_opinion == 0}
                <a href="{url name=admin_opinion_togglefavorite id=$opinion->id status=1  type=$type page=$page}" class="favourite_off" title="{t}NoFavorite{/t}">
                    &nbsp;
                </a>
                {/if}
                {/acl}
            </td>
			<td class="right">
				<div class="btn-group">
                    {acl isAllowed="OPINION_UPDATE"}
					<a class="btn" href="{url name=admin_opinion_show id=$opinion->id}" title="{t}Edit{/t}">
						<i class="icon-pencil"></i>
                    </a>
                    {/acl}
                    {acl isAllowed="OPINION_DELETE"}
					<a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                        data-url="{url name=admin_opinion_delete id=$opinion->id}"
                        data-title="{$opinion->title|capitalize}"
                        href="{url name=admin_opinion_delete id=$opinion->id}"
                        title="{t}Delete{/t}">
                        <i class="icon-trash icon-white"></i>
                    </a>
                    {/acl}
				</ul>
			</td>
		</tr>
        {foreachelse}
        <tr>
            <td class="empty" colspan="11">
                {t}There is no opinions yet.{/t}
            </td>
        </tr>
		{/foreach}
	</tbody>
	<tfoot>
		<tr >
			<td colspan="11" class="center">
                <div class="pagination">
    				{$pagination->links|default:""}&nbsp;
                </div>
			</td>
		</tr>
	</tfoot>
</table>
