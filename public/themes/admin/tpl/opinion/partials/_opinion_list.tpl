<div class="table-info clearfix">
    <div class="pull-left"><strong>[% total %] {t}items{/t}</strong></div>
    <div class="pull-right form-inline">
        {t}Type:{/t}
        <select class="input-small select2" ng-model="filters.search.blog">
            <option value="-1">-- All --</option>
            <option value="0">Opinion</option>
            <option value="1">Blog</option>
        </select>
        {t}Status:{/t}
        <select class="input-small select2" ng-model="filters.search.available">
            <option value="-1">{t}-- All --{/t}</option>
            <option value="1">{t}Published{/t}</option>
            <option value="0">{t}No published{/t}</option>
        </select>
         &nbsp;
        {t}Select an author{/t}
        <select class="select2 input-large" ng-model="filters.search.author">
            <option value="-1"> {t}All authors{/t} </option>
            <option value="-2"> {t}Director{/t} </option>
            <option value="-3"> {t}Editorial{/t} </option>
            {section name=as loop=$autores}
                <option value="{$autores[as]->id}" {if isset($author) && $author == $autores[as]->id} selected {/if}>{$autores[as]->name} {if $autores[as]->meta['is_blog'] eq 1} (Blogger) {/if}</option>
            {/section}
        </select>
    </div>
</div>
<div ng-if="loading" style="text-align: center; padding: 40px 0px;">
    <img src="/assets/images/facebox/loading.gif" style="margin: 0 auto;">
</div>
<table class="table table-hover table-condensed" ng-if="!loading">
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
