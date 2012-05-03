<table style="margin-bottom:0; width:100%;">
    <tbody>
        <tr>
            <td style="width:50%; vertical-align:top; padding:4px 0;" >
                {include file="article/related/_related_provider.tpl"}
            </td>
            <td style="width:50%; vertical-align:top; padding:4px 0;" >
                <div id="frontpage_related" class="column-receiver">
                    <h5>{t}Related in frontpage{/t}</h5>
                    <hr>
                    <ul class="content-receiver" >
                        {section name=d loop=$losrel}
                            <li class="" data-type="{$losrel[d]->content_type}" data-id="{$losrel[d]->pk_content}">
                                {$losrel[d]->created|date_format:"%d-%m-%Y"}:{$losrel[d]->title|clearslash}
                            </li>
                        {/section}
                    </ul>
                </div>

                <div id="inner_related" class="column-receiver">
                        <h5>{t}Related in inner{/t}</h5>
                        <hr>
                        <ul class="content-receiver" >
                        {section name=d loop=$intrel}
                            <li class="" data-type="{$intrel[d]->content_type}" data-id="{$intrel[d]->pk_content}">
                                {$intrel[d]->created|date_format:"%d-%m-%Y"}:{$intrel[d]->title|clearslash}
                            </li>
                        {/section}
                        </ul>
                </div>


                {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                    <div id="home_related" class="column-receiver">
                        <h5>{t}Related in home{/t}</h5>
                        <hr>
                        <ul class="content-receiver" >
                            {section name=d loop=$contentsHome}
                                <li class="" data-type="{$contentsHome[d]->content_type}" data-id="{$contentsHome[d]->pk_content}">
                                    {$contentsHome[d]->created|date_format:"%d-%m-%Y"}:{$contentsHome[d]->title|clearslash}
                                </li>
                            {/section}
                        </ul>

                    </div>
                    <div id="gallery-Frontpage" class="column-receiver">
                        <h5>{t}Gallery for frontpage{/t}</h5>
                        <hr>
                        <ul class="content-receiver" >
                                <li class="" data-type="{$contentsHome[d]->content_type}" data-id="{$contentsHome[d]->pk_content}">
                                    {$article->params['gallery']}
                                </li>
                        </ul>

                    </div>
                    <div id="gallery-Inner" class="column-receiver">
                         <h5>{t}Gallery for inner{/t}</h5>
                        <hr>
                        <ul class="content-receiver" >
                                <li class="" data-type="{$contentsHome[d]->content_type}" data-id="{$contentsHome[d]->pk_content}">
                                    {$article->params['gallery']}
                                </li>
                        </ul>

                    </div>
                {/is_module_activated}

            </td>
        </tr>
    </tbody>
</table>