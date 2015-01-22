{extends file="base/admin.tpl"}

{block name="content"}
<form id="formulario" name="formulario" action="{url name=admin_keywords}" method="GET">

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Keywords{/t}
                    </h4>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <h4>{t}Statistics{/t}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    {acl isAllowed="PCLAVE_CREATE"}
                    <li>
                        <a href="{url name=admin_keyword_create}" class="btn btn-primary">
                            {t}Create{/t}
                        </a>
                    </li>
                {/acl}
            </div>
        </div>
    </div>
</div>
{if isset($smarty.request.filter)
    && isset({$smarty.request.filter.pclave})}
    {assign var=filterPClave value=$smarty.request.filter.pclave}
{/if}
<div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="m-r-10 input-prepend inside search-input no-boarder">
                    <span class="add-on">
                        <span class="fa fa-search fa-lg"></span>
                    </span>
                    <input class="no-boarder" type="text" name="name" value="{$name|default:""}" placeholder="{t}Filter by name{/t}" />
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                </li>
                <li class="pull-right">
                    <strong>Result:</strong> {$pagination->_totalItems} {t}users{/t}
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="content">

	{render_messages}

    <div class="grid simple">
        <div class="grid-body no-padding">
            {if count($keywords) > 0}
    		<table class="table table-hover table-condensed">
    			<thead>
    				<tr>
    					<th style="width:20px;" class="nowrap">{t}Type{/t}</th>
    					<th scope=col>{t}Keyword{/t}</th>
    					<th scope=col>{t}Replacement value{/t}</th>
    					<th scope=col style="width:100px;" class="right nowrap">{t}Actions{/t}</th>
    				</tr>
    			</thead>

    			<tbody>
    				{foreach name=k from=$keywords|default:array() item=keyword}
    				<tr>
    					<td class="center nowrap">
    						{$types[$keyword->tipo]}
    					</td>
    					<td>
    						{$keyword->pclave}
    					</td>
    					<td>
    						{$keyword->value|default:"-"}
    					</td>

    					<td class="right">
    						<div class="btn-group">
    							<a class="btn" href="{url name=admin_keyword_show id=$keyword->id}" title="{t}Edit this content{/t}">
    	                            <i class="fa fa-pencil"></i>{t}Edit{/t}
    	                        </a>
    	                        <a href="{url name=admin_keyword_delete id=$keyword->id}" class="btn btn-danger" title="{t}Delete{/t}">
    	                            <i class="fa fa-trash-o"></i>
    	                        </a>
    						</div>

    					</td>
    				</tr>
    				{/foreach}
    			</tbody>

    			<tfoot>
    				<tr>
    					<td colspan="5" class="center">
    						<div class="pagination">
    							{$pagination->links}
    						</div>
    					</td>
    				</tr>
    			</tfoot>
    		</table>
            {else}
            <div class="center">
                <p>
                    <h5>{t}No available keywords yet.{/t}</h5>
                </p>
            </div>
            {/if}
        </div>
    </div>

</div>
</form>
{/block}
