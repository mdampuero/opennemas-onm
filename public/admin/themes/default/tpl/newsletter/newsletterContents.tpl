{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/admin.css"}
{css_tag href="/newsletter.css" media="screen"}
{/block}

{block name="footer-js" append}
{script_tag src="/newsletter.js"}
{script_tag src="newsletter/addContents.js"}
{script_tag src="/onm/jquery.content-provider.js"}
{script_tag src="/jquery-onm/jquery.newsletter.js"}
{/block}

{block name="content"}

<div id="buttons" class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Newsletter management{/t}</h2>
        </div>

        <ul class="old-button">

            <li>
                <a href="#" class="admin_add" title="{t}Next{/t}">
                    <img src="{$params.IMAGE_DIR}arrow_next.png" alt="{t}Next{/t}" /><br />
                    {t}Next step{/t}
                </a>
            </li>
             <li>
                <a href="#" class="admin_add" title="{t}Previous{/t}">
                    <img src="{$params.IMAGE_DIR}arrow_previous.png" alt="{t}Previous{/t}" /><br />
                    {t}Prev step{/t}
                </a>
            </li>

            <li>
                <a href="#" class="admin_add" title="{t}Clean containers{/t}">
                    <img src="{$params.IMAGE_DIR}editclear.png" alt="{t}Clean containers{/t}" /><br />
                    {t}Clean{/t}
                </a>
            </li>
            <li class="separator"></li>
            <li>
                <a href="#" class="admin_add" title="{t}Load saved Newsletter{/t}">
                    <img src="{$params.IMAGE_DIR}newsletter/load.png" alt="{t}Load saved Newsletter{/t}" /><br />
                    {t}Load newsletter{/t}
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


<div class="newsletter-contents">

    {render_messages}

    <table style="margin-bottom:0; width:100%;">
        <tbody>
            <tr>
                <td style="width:50%; vertical-align:top; padding:4px 0;" >
                    <div id="newsletter-container" class="column-receiver">
                        <h5>{t}Newsletter contents{/t}</h5>
                        <hr>
                        <ul class="content-receiver" >

                        </ul>
                    </div>
                </td>
                <td style="width:50%; vertical-align:top; padding:4px 0;" >
                    {include file="newsletter/_partials/container_contents.tpl"}
                </td>
            </tr>
        </tbody>
    </table>

</div>
{/block}

