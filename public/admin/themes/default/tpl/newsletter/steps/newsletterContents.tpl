{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/admin.css"}
{css_tag href="/newsletter.css" media="screen"}
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{script_tag src="/onm/jquery.content-provider.js"}
{script_tag src="/jquery-onm/newsletter/jquery.stepContents.js"}

{/block}

{block name="content"}

<form action="#" method="post" name="newsletterForm" id="newsletterForm" {$formAttrs}>

    <div id="buttons" class="top-action-bar clearfix">
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
                        <td style="width:50%; vertical-align:top; padding:4px 0;" >
                            <div id="savedNewsletter" style="width:100%;background-color:#EEE;padding:10px;">
                                <label>Seleccione un boletín guardado y pulse restaurar su contenido.</label>
                                <select name="saved_newsletters" id="saved_newsletters">
                                    {section loop=$savedNewsletters name=a}
                                        <option value="{$savedNewsletters[a]->id}"> {$savedNewsletters[a]->created|date_format:"%d/%m/%Y - %H:%M:%S"} </option>
                                    {/section}
                                </select>
                                <a id="load-saved" href="#"  title="{t}Load saved newsletter{/t}" >
                                    <img src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" alt="Cargar" style="width:24px;"/>
                                </a>
                            </div>
                            <div id="newsletter-container" class="column-receiver">
                                <h5>{t}Newsletter contents{/t}</h5>
                                <hr>
                                <ul class="content-receiver" >
                                    {if !empty($newsletterContent)}
                                    {section name=d loop=$newsletterContent}
                                    {if !empty($newsletterContent[d]->title)}
                                    <li  data-id="{$newsletterContent[d]->id}" data-type="{$newsletterContent[d]->type}">
                                         {$newsletterContent[d]->type} {$newsletterContent[d]->title}
                                    </li>
                                    {/if}
                                    {/section}
                                    {/if}
                                </ul>
                            </div>
                        </td>
                        <td style="width:50%; vertical-align:top; padding:4px 0;" >
                            <div  style="width:100%;background-color:#EEE;padding:10px;">
                                <a id="button-add-text" style="cursor:pointer;">
                                    <img src="{$params.IMAGE_DIR}list-add.png" style="width:24px" border="0" /><br>{t}Add Text{/t}
                                </a>
                            </div>
                            {include file="newsletter/_partials/container_contents.tpl"}
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" id="action" name="action" value="preview" />
        </div>
    </div>

</form>
{/block}

