{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/admin.css"}
{css_tag href="/newsletter.css" media="screen"}
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{script_tag src="/onm/jquery.content-provider.js"}
{script_tag src="/jquery-onm/jquery.newsletter.js"}

{/block}

{block name="content"}

<form action="#" method="post" name="newsletterForm" id="newsletterForm" {$formAttrs}>

    <div id="buttons-contents" class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Newsletter management{/t}</h2>
            </div>

            <ul class="old-button">

                <li>
                    <a href="#" class="admin_add" title="{t}Next{/t}" id="next-button">
                        <img src="{$params.IMAGE_DIR}arrow_next.png" alt="{t}Next{/t}" /><br />
                        {t}Next step{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" title="{t}Clean containers{/t}" id="clean-button">
                        <img src="{$params.IMAGE_DIR}editclear.png" alt="{t}Clean containers{/t}" /><br />
                        {t}Clean{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config newsletter module{/t}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Configurations{/t}
                    </a>
                </li>

                <li >
                    <a href="subscriptors.php?action=list" class="admin_add" id="submit_mult" title="{t}Subscriptors{/t}">
                        <img src="{$params.IMAGE_DIR}authors.png" title="{t}Subscriptors{/t}" alt="{t}Subscriptors{/t}"><br />{t}Subscriptors{/t}
                    </a>
                </li>
            </ul>

        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="newsletter-contents">

            <table style="margin-bottom:0; width:100%;">
                <tbody>
                    <tr>
                        <td style="width:50%; vertical-align:top;background-color:#EEE;padding:10px;" >
                            <div id="savedNewsletter" style="width:100%">
                                <label>Seleccione un boletín guardado y pulse restaurar su contenido.</label>
                                <select name="saved_newsletters" id="saved_newsletters">
                                    <option value="0">  </option>
                                    {section loop=$savedNewsletters name=a}
                                        <option value="{$savedNewsletters[a]->id}"> {$savedNewsletters[a]->created|date_format:"%d/%m/%Y - %H:%M:%S"} </option>
                                    {/section}
                                </select>
                                <a id="load-saved" href="#"  title="{t}Load saved newsletter{/t}" >
                                    <img src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" alt="Cargar" style="width:24px;"/>
                                </a>
                            </div>
                        </td>
                        <td style="width:50%; vertical-align:top;;background-color:#EEE;padding:10px;" >
                            <ul>
                                <li style="float:left; text-align:center;padding-left:10px;">
                                    <a id="button-add-container" style="cursor:pointer;">
                                        <img src="{$params.IMAGE_DIR}list-add.png" style="width:24px" border="0" /><br>{t}Add Container{/t}
                                    </a>
                                </li>
                                <li  style="float:left; text-align:center;padding-left:10px;">
                                    <a id="button-check-all" href="#"  title="{t}Check All{/t}">
                                        <input id="toggleallcheckbox" type="checkbox" ><br> {t}Check All{/t}
                                    </a>
                                </li>
                                <li  style="float:left; text-align:center;padding-left:10px;">
                                    <a id="add-selected" href="#"  title="{t}Add Selected items{/t}" >
                                        <img src="{$params.IMAGE_DIR}list-add.png" alt="{t}Add Selected items{/t}" style="width:24px;"/>
                                        <br> {t}Add Selected{/t}
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50%; vertical-align:top; padding:4px 0;" >

                            <div id="newsletter-container" class="column-receiver">
                                <h5>{t}Newsletter contents{/t}</h5>
                                <hr>
                                {if empty($newsletterContent)}
                                    <ul class="content-receiver" data-id="1">
                                        <li class="container-label" data-type="label"  data-id="1" data-title="Destacado">Destacado</li>
                                    </ul>
                                {else}

                                    {section name=c loop=$newsletterContent}
                                        {assign var='contents' value=$newsletterContent[c]->items}
                                        {if !empty($contents)}
                                        <ul class="content-receiver" data-id="{$newsletterContent[c]->id}">
                                            {section name=d loop=$contents}
                                                {if !empty($contents[d]->title)}
                                                <li  data-id="{$contents[d]->id}"
                                                    {if $contents[d]->content_type eq 'label'} class="container-label" {/if}
                                                    data-title="{$contents[d]->title}" data-type="{$contents[d]->content_type}" >
                                                     {$contents[d]->type} {$contents[d]->title}
                                                </li>
                                                {/if}
                                            {/section}
                                        </ul>
                                        {/if}
                                    {/section}
                                {/if}

                            </div>
                        </td>
                        <td style="width:50%; vertical-align:top; padding:4px 0;" >

                            {include file="newsletter/_partials/container_contents.tpl"}
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" id="action" name="action" value="preview" />
        </div>
    </div>

</form>
 {include file="newsletter/modals/_add_container_label.tpl"}
{/block}

