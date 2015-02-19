<div class="content">
    {render_messages}
    <div class="grid simple">
        <div class="grid-body no-padding">
            <div class="table-wrapper ng-cloak">
                <table class="table table-hover no-margin" id="list-opinion">
                    <thead>
                        <tr>
                            <th style="width:200px;">{t}Author{/t} - {t}Title{/t}</th>
                            <th class="center hidden-xs hidden-sm" style="width:130px;">{t}Created on{/t}</th>
                            <th class="center hidden-xs" style="width:10px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}"></th>
                            <th class="center hidden-xs" style="width:10px;">{t}Home{/t}</th>
                        </tr>
                    </thead>
                    {if count($director) > 0}
                        <tbody>
                            <tr class="table-header">
                                <td colspan="4">
                                    <strong>{t}Director Articles{/t}</strong>
                                </td>
                            </tr>
                            {assign var='cont' value=1}
                            {foreach from=$director item=opinion}
                            <tr class="director-opinion" data-id="{$opinion->id}">
                                <td>
                                    {t}Director{/t} - {$opinion->title|clearslash}

                                    <div class="visible-sm visible-xs small-text">
                                      {t}Created:{/t} [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                                    </div>

                                    <div class="listing-inline-actions">
                                    {acl isAllowed="OPINION_UPDATE"}
                                        <a class="link" href="{url name=admin_opinion_show id=$opinion->id}" title="{t}Edit{/t}">
                                            <i class="fa fa-pencil"></i> {t}Edit{/t}
                                        </a>
                                    {/acl}
                                    </div>
                                </td>
                                <td class="center hidden-xs hidden-sm">
                                    {$opinion->created}
                                </td>
                                <td class="center hidden-xs">
                                    {$opinion->views}
                                </td>
                                <td class="center hidden-xs">
                                    {if $opinion->in_home == 1}
                                        <a href="{url name=admin_opinion_toggleinhome id=$opinion->id status=0 type=$type page=$page}" class="no_home" title="Sacar de portada" ></a>
                                    {else}
                                        <a href="{url name=admin_opinion_toggleinhome id=$opinion->id status=1  type=$type page=$page}" class="go_home" title="Meter en portada" ></a>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    {/if}
                    {if count($editorial) > 0}
                        <tbody>
                        <tr class="table-header">
                            <td colspan="4">
                                <strong>{t}Editorial Articles{/t}</strong>
                            </td>
                        </tr>
                        {foreach from=$editorial item=opinion}
                        <tr class="editorial-opinion" data-id="{$opinion->id}">
                            <td onClick="javascript:document.getElementById('selected_{$cont}').click();">
                                {t}Editorial{/t} - {$opinion->title|clearslash}

                                <div class="visible-sm visible-xs small-text">
                                  {t}Created:{/t} [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                                </div>

                                <div class="listing-inline-actions">
                                {acl isAllowed="OPINION_UPDATE"}
                                    <a class="link" href="{url name=admin_opinion_show id=$opinion->id}" title="{t}Edit{/t}">
                                        <i class="fa fa-pencil"></i> {t}Edit{/t}
                                    </a>
                                {/acl}
                                </div>
                            </td>
                            <td class="center hidden-xs hidden-sm">
                                {$opinion->created}
                            </td>
                            <td class="center hidden-xs">
                                {$opinion->views}
                            </td>
                            <td class="center">
                                {if $opinion->in_home == 1}
                                    <a href="{url name=admin_opinion_toggleinhome id=$opinion->id status=0  type=$type page=$page}" class="no_home" title="Sacar de portada" ></a>
                                {else}
                                    <a href="{url name=admin_opinion_toggleinhome id=$opinion->id status=1  type=$type page=$page}" class="go_home" title="Meter en portada" ></a>
                                {/if}
                            </td>
                        </tr>
                        {assign var='cont' value=$cont+1}
                        {/foreach}
                        </tbody>
                    {/if}
                    {if  count($opinions) > 0}
                        <tbody>
                            <tr class="table-header">
                                <td colspan="4">
                                    <strong>{t}Other Articles{/t}</strong>
                                </td>
                            </tr>
                            {foreach from=$opinions item=opinion}
                                {if $opinion->author->meta['is_blog'] neq 1}
                                    <tr class="normal-opinion" data-id="{$opinion->id}">
                                        <td onClick="javascript:document.getElementById('selected_{$cont}').click();">
                                            {if $opinion->type_opinion==1}
                                              {t}Editorial{/t}
                                            {elseif $opinion->type_opinion==2}
                                                {t}Director{/t}
                                            {else}
                                                {acl isAllowed="AUTHOR_UPDATE"}
                                                <a href="{url name=admin_acl_user_show id=$opinion->author->id}">
                                                    {$opinion->author->name}
                                                </a>
                                                {/acl}
                                            {/if}
                                            -
                                            {$opinion->title|clearslash}

                                            <div class="visible-sm visible-xs small-text">
                                              {t}Created:{/t} [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                                            </div>

                                            <div class="listing-inline-actions">
                                            {acl isAllowed="OPINION_UPDATE"}
                                                <a class="link" href="{url name=admin_opinion_show id=$opinion->id}" title="{t}Edit{/t}">
                                                    <i class="fa fa-pencil"></i> {t}Edit{/t}
                                                </a>
                                            {/acl}
                                            </div>
                                        </td>
                                        <td class="center hidden-xs hidden-sm">
                                            {$opinion->created}
                                        </td>

                                        <td class="center hidden-xs">
                                            {$opinion->views}
                                        </td>
                                        <td class="center">
                                            {acl isAllowed="OPINION_FRONTPAGE"}
                                                {if $opinion->in_home == 1}
                                                    <a href="{url name=admin_opinion_toggleinhome id=$opinion->id status=0 type=$type page=$page}" class="no_home" title="Sacar de portada" >
                                                        &nbsp;
                                                    </a>
                                                {else}
                                                    <a href="{url name=admin_opinion_toggleinhome id=$opinion->id status=1 type=$type page=$page}" class="go_home" title="Meter en portada" >
                                                        &nbsp;
                                                    </a>
                                                {/if}
                                            {/acl}
                                        </td>
                                    </tr>
                                {/if}
                            {/foreach}
                        </tbody>
                    {/if}
                </table>
            </div>
        </div>
    </div>
</div>
