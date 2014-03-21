<div class="table-info clearfix">
    <div class="pull-left"><strong>[% total %] {t}items{/t}</strong></div>
    <div class="pull-right form-inline">
        {t}Type:{/t}
        <select class="input-small select2" name="type">
            <option value="opinion">Opinion</option>
            <option value="blog">Blog</option>
        </select>
        {t}Status:{/t}
        <select class="input-small select2" name="status">
            <option value="-1" {if $status === -1} selected {/if}> {t}-- All --{/t} </option>
            <option value="1" {if  $status === 1} selected {/if}> {t}Published{/t} </option>
            <option value="0" {if $status === 0} selected {/if}> {t}No published{/t} </option>
        </select>
         &nbsp;
        {t}Select an author{/t}
        <select class="select2 input-large" name="author" id="author">
            <option value="0" {if isset($author) && $author eq "0"} selected {/if}> {t}All authors{/t} </option>
            <option value="-1" {if isset($author) && $author eq "-1"} selected {/if}> {t}Director{/t} </option>
            <option value="-2" {if isset($author) && $author eq "-2"} selected {/if}> {t}Editorial{/t} </option>
            {section name=as loop=$autores}
                <option value="{$autores[as]->id}" {if isset($author) && $author == $autores[as]->id} selected {/if}>{$autores[as]->name} {if $autores[as]->meta['is_blog'] eq 1} (Blogger) {/if}</option>
            {/section}
        </select>
    </div>
</div>
<table class="table table-hover table-condensed">
	<thead>
		<tr>
			<th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
			<th>{t}Author name{/t} - {t}Title{/t}</th>
            <th class="center" style="width:40px"><i class="icon-eye-open" style="font-size: 130%;"></i></th>
			<th class="center" style="width:110px;">{t}Created in{/t}</th>
			<th class="center" style="width:70px;">{t}In home{/t}</th>
			<th class="center" style="width:20px;">{t}Published{/t}</th>
            <th class="center" style="width:20px;">{t}Favorite{/t}</th>
			<th class="center" style="width:70px;">{t}Actions{/t}</th>
	  </tr>
	</thead>
	<tbody>
		<tr ng-if="contents.length > 0" ng-include="'opinion'" ng-repeat="content in contents">

		</tr>
        <tr ng-if="contents.length == 0">
            <td class="empty" colspan="11">
                {t}There is no opinions yet.{/t}
            </td>
        </tr>
	</tbody>
	<tfoot>
		<tr >
			<td colspan="11" class="center">
                <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_opinions_list')" page="page" total-items="total" num-pages="pages"></pagination>
			</td>
		</tr>
	</tfoot>
</table>
