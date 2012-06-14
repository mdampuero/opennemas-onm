<table style="margin-bottom:0; width:100%;">
    <tbody>
        <tr>
            <td style="width:50%; vertical-align:top; padding:4px 0;" >
                {include file="article/related/_related_provider.tpl"}
            </td>
            <td style="width:50%; vertical-align:top; padding:4px 0;" >
                <div id="frontpage_related" class="column-receiver">
                    <h5>{t}Related in frontpage{/t}</h5>
                    <ul class="content-receiver" >
                        {section name=d loop=$orderFront}
                            <li class="" data-type="{$orderFront[d]->content_type}" data-id="{$orderFront[d]->pk_content}">
                                {$orderFront[d]->created|date_format:"%d-%m-%Y"}:{$orderFront[d]->title|clearslash}
                            </li>
                        {/section}
                    </ul>
                </div>

                <div id="inner_related" class="column-receiver">
                        <h5>{t}Related in inner{/t}</h5>
                        <ul class="content-receiver" >
                        {section name=d loop=$orderInner}
                            <li class="" data-type="{$orderInner[d]->content_type}" data-id="{$orderInner[d]->pk_content}">
                                {$orderInner[d]->created|date_format:"%d-%m-%Y"}:{$orderInner[d]->title|clearslash}
                            </li>
                        {/section}
                        </ul>
                </div>


                {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                    <div id="home_related" class="column-receiver">
                        <h5>{t}Related in home{/t}</h5>
                        <ul class="content-receiver" >
                            {section name=d loop=$orderHome}
                                <li class="" data-type="{$orderHome[d]->content_type}" data-id="{$orderHome[d]->pk_content}">
                                    {$orderHome[d]->created|date_format:"%d-%m-%Y"}:{$orderHome[d]->title|clearslash}
                                </li>
                            {/section}
                        </ul>

                    </div>
                    <div id="gallery-Frontpage" class="column-receiver gallery">
                        <h5>{t}Gallery for frontpage{/t}(*{t}Only one album{/t})</h5>
                        <ul class="content-receiver" >
                            {if !empty($article->params['withGallery']) && !empty($galleries['front']->pk_album)}
                                <li class="" data-type="Album" data-id="{$article->params['withGallery']}">
                                    {$galleries['front']->created|date_format:"%d-%m-%Y"}:{$galleries['front']->title|clearslash}
                                </li>
                            {/if}
                        </ul>
                    </div>
                    <div id="gallery-Inner" class="column-receiver gallery">
                        <h5>{t}Gallery for inner{/t}(*{t}Only one album{/t})</h5>
                        <ul class="content-receiver" >
                            {if !empty($article->params['withGallery']) && !empty($galleries['inner']->pk_album)}
                                <li class="" data-type="Album" data-id="{$article->params['withGallery']}">
                                    {$galleries['inner']->created|date_format:"%d-%m-%Y"}:{$galleries['inner']->title|clearslash}
                                </li>
                            {/if}
                        </ul>
                    </div>
                    <div id="gallery-Home" class="column-receiver gallery">
                        <h5>{t}Gallery for Home{/t} (*{t}Only one album{/t})</h5>
                        <ul class="content-receiver" >
                            {if !empty($article->params['withGallery']) && !empty($galleries['home']->pk_album)}
                                <li class="" data-type="Album" data-id="{$article->params['withGallery']}">
                                    {$galleries['home']->created|date_format:"%d-%m-%Y"}:{$galleries['home']->title|clearslash}
                                </li>
                            {/if}
                        </ul>

                    </div>
                {/is_module_activated}

            </td>
        </tr>
    </tbody>
</table>