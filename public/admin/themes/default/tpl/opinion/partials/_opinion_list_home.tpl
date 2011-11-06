{if count($director) > 0}


<table class="listing-table">
	<thead>
		<tr>
			<th style="width:30px;"><input type="checkbox" id="toggleallcheckbox"></th>
			<th style="width:160px;">{t}Author{/t}</th>
			<th>{t}Title{/t}</th>
			 <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
			<th class="center" style="width:80px;">{t}Votes{/t}</th>
            <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}comments.png" alt="{t}Comments{/t}" title="{t}Comments{/t}"></th>
			<th class="center" style="width:100px;">Fecha</th>
			<th class="center" style="width:30px;">Home</th>
			<th class="center" style="width:70px;">{t}Actions{/t}</th>
		</tr>
	</thead>
    </table>
<table class="adminheading">
	<tr>
		<td>
			<strong>{t}Director Articles{/t}</strong>
		</td>
	</tr>
</table>

    <table class="listing-table">
	<tr>
		<td colspan='11'>
            <div id="deldirector" class="seccion">
            {assign var='cont' value=1}
            {section name=c loop=$director}
		    <table id="{$director[c]->id}" style="width:100%"  id="{$director[c]->id}" class="dir_sort">
				<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
					<td style="width:35px;">
						<input type="checkbox" class="minput"  id="selected_{$cont}" name="selected_fld[]" value="{$director[c]->id}"  style="cursor:pointer;">
					</td>
					<td onClick="javascript:document.getElementById('selected_{$cont}').click();" style="width:165px;">
						{t}Director{/t}
					</td>
					<td onClick="javascript:document.getElementById('selected_{$cont}').click();" >
						{$director[c]->title|clearslash}
					</td>
					<td class="center" style="width:80px;">
						{$director[c]->views}
					</td>
					<td class="center" style="width:80px;">
						{$director[c]->ratings}
					</td>
					<td class="center" style="width:70px;">
						{$director[c]->comments}
					</td>
					<td class="center" style="width:78px;">
						{$director[c]->created}
					</td>
					<td class="center" style="width:55px;">
						{if $director[c]->in_home == 1}
								<a href="?id={$director[c]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Sacar de portada" ></a>
						{else}
								<a href="?id={$director[0]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" class="go_home" title="Meter en portada" ></a>
						{/if}
					</td>
					<td class="center" style="width:72px;">
						<ul class="action-buttons">
                            {acl isAllowed="OPINION_AVAILABLE"}
							<li>                                
								{if $director[0]->content_status == 1}
									<a href="?id={$director[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
										<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
									</a>
								{else}
									<a href="?id={$director[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
										<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
									</a>
								{/if}                                
							</li>
                            {/acl}
                            {acl isAllowed="OPINION_UPDATE"}
							<li>
								<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$director[c]->id}');" title="Modificar">
									<img src="{$params.IMAGE_DIR}edit.png" border="0" />
								</a>
							</li>
                            {/acl}
                            {acl isAllowed="OPINION_DELETE"}
							<li>
								<a href="#" onClick="javascript:delete_opinion('{$director[c]->id}',{$paginacion->_currentPage|default:0});" title="Eliminar">
									<img src="{$params.IMAGE_DIR}trash.png" border="0" />
								</a>
							</li>
                            {/acl}
						</ul>
					</td>
				</tr>
			</table>
            {assign var='cont' value=$cont+1}
            {/section}
            </div>
		</td>
	</tr>
</table>

<br />

{/if}

{if count($editorial) > 0}

<table class="adminheading">
	<tr>
		<td>
			<strong>{t}Editorial Articles{/t}</strong>
		</td>
	</tr>
</table>
<table class="listing-table">

	<tbody>
		<tr>
			<td colspan='11'>
				<div id="editoriales" class="seccion">
				
				{section name=c loop=$editorial}
					<table width="100%" cellpadding=0 cellspacing=0  id="{$editorial[c]->id}" border=0 class="edits_sort">
						<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
							<td style="width:15px;">
								<input type="checkbox" class="minput"  id="selected_{$cont}" name="selected_fld[]" value="{$editorial[c]->id}"  style="cursor:pointer;" >
							</td>
							<td onClick="javascript:document.getElementById('selected_{$cont}').click();" style="width:160px;">
								Editorial
							</td>
							<td onClick="javascript:document.getElementById('selected_{$cont}').click();">
								{$editorial[c]->title|clearslash}
							</td>
							<td class="center" style="width:40px;">
								{$editorial[c]->views}
							</td>
							<td class="center" style="width:90px;">
								{$editorial[c]->ratings}
							</td>
							<td class="center" style="width:40px;">
								{$editorial[c]->comments}
							</td>
							<td class="center" style="width:130px;">
								{$editorial[c]->created}
							</td>
							<td class="center" style="width:30px;">
								{if $editorial[c]->in_home == 1}
									<a href="?id={$editorial[c]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Sacar de portada" ></a>
								{else}
									<a href="?id={$editorial[c]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" class="go_home" title="Meter en portada" ></a>
								{/if}
							</td>
							<td class="center" style="width:72px;">
								<ul class="action-buttons">
                                    {acl isAllowed="OPINION_AVAILABLE"}
									<li>
										{if $editorial[c]->content_status == 1}
											<a href="?id={$editorial[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
												<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
											</a>
										{else}
											<a href="?id={$editorial[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
												<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
											</a>
										{/if}
									</li>
                                    {/acl}
                                    {acl isAllowed="OPINION_UPDATE"}
									<li>
										<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$editorial[c]->id}');" title="Modificar">
											<img src="{$params.IMAGE_DIR}edit.png" border="0" />
										</a>
									</li>
                                    {/acl}
                                    {acl isAllowed="OPINION_DELETE"}
									<li>
										<a href="#" onClick="javascript:delete_opinion('{$editorial[c]->id}',{$paginacion->_currentPage|default:0});" title="Eliminar">
											<img src="{$params.IMAGE_DIR}trash.png" border="0" />
										</a>
									</li>
                                    {/acl}
								</ul>
							</td>
						</tr>
				</table>
				{assign var='cont' value=$cont+1}
				{/section}
				</div>
			</td>
		</tr>
	</tbody>
</table>

<br>
{/if}
 
{if  count($opinions) > 0}

<table class="adminheading">
	<tr>
		<td>
			<strong>{t}Other Articles{/t}</strong>
		</td>
	</tr>
</table>
<table class="listing-table">
 
	<tr>
	<td colspan='11'>
	<div id="cates" class="seccion">
	
		{section name=c loop=$opinions}
		 <table id="{$opinions[c]->id}" border=0 {if $opinions[c]->type_opinion==0}class="sortable"{/if} style="width:100%">
			<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
				<td style="width:15px;">
				   <input type="checkbox" class="minput"  id="selected_{$cont}" name="selected_fld[]" value="{$opinions[c]->id}"  style="cursor:pointer;" >
				</td>
				{if $type_opinion=='-1' ||  $type_opinion=='0'}
				<td onClick="javascript:document.getElementById('selected_{$cont}').click();" style="width:160px;">
					{if $opinions[c]->type_opinion==1} Editorial{elseif $opinions[c]->type_opinion==2}
						{t}Director{/t}
					{else}

                        {acl isAllowed="AUTHOR_UPDATE"}
						<a href="author.php?action=read&id={$opinions[c]->fk_author}">
							{$names[c]}
						</a>
                        {/acl}
					{/if}
				</td>
				{/if}
				<td onClick="javascript:document.getElementById('selected_{$cont}').click();">
					{$opinions[c]->title|clearslash}
				</td>

				<td class="center" style="width:40px;">
					{$opinions[c]->views}
				</td>
				<td class="center" style="width:90px;">
					{$op_rating[c]|default:""}
				 </td>
				<td style="width:70px;" class="center">
					{$op_comment[c]}
				</td>
				<td class="center" style="width:100px;">
					{$opinions[c]->created}
				</td>
				<td class="center" style="width:40px;">
                    {acl isAllowed="OPINION__FRONTPAGE"}
					{if $opinions[c]->in_home == 1}
						<a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category|default:""}" class="no_home" title="Sacar de portada" >
                            &nbsp;
                        </a>
					{else}
						<a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category|default:""}" class="go_home" title="Meter en portada" >
                            &nbsp;
                        </a>
					{/if}
                    {/acl}
				</td>
				<td class="center" style="width:70px;">
					<ul class="action-buttons">
                        {acl isAllowed="OPINION_UPDATE"}
						<li>
							{if $opinions[c]->content_status == 1}
							<a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage|default:""}" title="Publicado">
								<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
							</a>
							{else}
							<a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage|default:""}" title="Pendiente">
								<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
							</a>
							{/if}
						</li>
                        {/acl}
                        {acl isAllowed="OPINION_UPDATE"}
						<li>
							<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$opinions[c]->id}');" title="Modificar">
								<img src="{$params.IMAGE_DIR}edit.png" border="0" />
							</a>
						</li>
                        {/acl}
                        {acl isAllowed="OPINION_DELETE"}
						<li>
							<a href="#" onClick="javascript:delete_opinion('{$opinions[c]->id}',{$paginacion->_currentPage|default:0});" title="Eliminar">
								<img src="{$params.IMAGE_DIR}trash.png" border="0" />
							</a>
						</li>
                        {/acl}
					</ul>
				</td>
			</tr>
        </table>
        {assign var='cont' value=$cont+1}
	{/section}
</div>
  </td>
</tr>
<tfoot>
	<tr class="pagination">
		<td colspan="11" class="center">
			{$paginacion}&nbsp;
		</td>
	</tr>
</tfoot>
</table>
{/if}

{if $type_opinion=='-1'}
<script type="text/javascript">
 // <![CDATA[
	Sortable.create('cates',{
		tag:'table',
		only:'sortable',
		dropOnEmpty: true,
		containment:["cates"],
		constraint:false
	});
	Sortable.create('editoriales',{
		tag:'table',
		only:'edits_sort',
		dropOnEmpty: true,
		containment:["editoriales"],
		constraint:false
	});
    Sortable.create('deldirector',{
		tag:'table',
		only:'dir_sort',
		dropOnEmpty: true,
		containment:["deldirector"],
		constraint:false
	});
 // ]]>
</script>
{/if}
