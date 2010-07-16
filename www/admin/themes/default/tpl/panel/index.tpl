{extends file="layout.tpl"}

{block name="head-title"}{$smarty.block.parent} - {t}Widget Manager{/t}{/block}

{block name="head-js"}
<script type="text/javascript" src="{$params.JS_DIR}jquery.feedreader.js"></script>
<script type="text/javascript" src="{$params.JS_DIR}jquery.qtip-1.0.0-rc3.min.js"></script>
{/block}


{block name="head-css"}
    <link rel="stylesheet" href="{$params.CSS_DIR}welcomepanel.css" type="text/css" />
{/block}

{block name="body-content"}

{toolbar_route toolbar="toolbar-top"
    route="page-index" text="Page Manager"}
    
{toolbar_route toolbar="toolbar-top"
    route="devel-loadcontents" text="Load Contents"}     

<table class="adminform" height="100%">
    <tbody>
        <tr>
            <td colspan="2">{flashmessenger}</td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="cpanel">
                    
                    {toolbar name="toolbar-top"}                                        
                    
                    {* acl isAllowed="FRONTPAGE_ADMIN"}
                	<div style="float: left;">
                        <div class="icon">                            
                            <a href="/admin/{url route="page-index"}">
                                <img alt="" src="{$params.IMAGE_DIR}tree.png"/>
                                <span>{t}Page Manager{/t}</span>
                            </a>
                            
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="ARTICLE_CREATE"}
                	<div style="float: left;">
                        <div class="icon">                            
                            <a href="/admin/article.php?action=new&category=221">
                                <img alt="" src="{$params.IMAGE_DIR}article_add.gif"/>
                                <span>{t}New Article{/t}</span>
                            </a>
                            
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="OPINION_CREATE"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/opinion.php?action=new">
                                <img alt="" src="{$params.IMAGE_DIR}opinion.png"/>
                                <span>{t}New Opinion{/t}</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="ADVERTISEMENT_CREATE"}
                	<div style="float: left;">
                        <div class="icon">
                            <a href="/admin/advertisement.php?action=new&category=0">
                                <img alt="" src="{$params.IMAGE_DIR}advertisement.png"/>
                                <span>{t}New Advertisement{/t}</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="MEDIA_ADMIN"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/mediamanager.php">
                                <img alt="" src="{$params.IMAGE_DIR}add_photo.png"/>
                                <span>{t}Upload Media{/t}</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="ARTICLE_FRONTPAGE"}
                	<div style="float: left;">
                        <div class="icon">
                            <a href="/admin/article.php?action=list&category=home">
                                <img alt="" src="{$params.IMAGE_DIR}iconos/frontpage_manager.png"/>
                                <span>{t}Frontpage manager{/t}</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="ARTICLE_LIST_PEND"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/article.php?action=list_pendientes&category=todos">
                                <img alt="" src="{$params.IMAGE_DIR}iconos/draft_manager.png"/>
                                <span>{t}Pending{/t}</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="OPINION_ADMIN"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/opinion.php?action=list">
                                <img alt="" src="{$params.IMAGE_DIR}iconos/draft_manager.png"/>
                                <span>{t}Opinion manager{/t}</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="COMMENTS_ADMIN"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/comment.php?action=list&category=todos">
                                <img alt="" src="{$params.IMAGE_DIR}comments_manager.png"/>
                                <span>{t}Comment manager{/t}</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="NEWSLETTER_ADMIN"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/newsletter.php">
                                <img alt="" src="{$params.IMAGE_DIR}newsletter/mail_message_new.png"/>
                                <span>{t}Newsletter{/t}</span>
                            </a>
                        </div>
                    </div>
                    {/acl *}  
                    
                </div>
            </td>
        </tr>
        
        <tr>
            <td width="50%" valign="top">
                {if count($messages.entries)>0}
                <div class="rssBox">
                    <h2 style="cursor: pointer;">
                        <img src="{$params.IMAGE_DIR}gmail_ico.png" border="0" align="absmiddle" />
                        {$messages.total} {t}unread emails{/t}
                    </h2>
                    <div id="gmailBox">
                        <ul>
                        {assign var="entries" value=$messages.entries}
                        {section name="im" loop=$entries}
                        <li>
                            <img src="{$params.IMAGE_DIR}iconos/sin_leer.gif" border="0" align="absmiddle" />
                            <a href="mailto:{$entries[im].email}"><span class="author">{$entries[im].name}</span></a> &middot;
                            <a href="{$entries[im].link}"
                               onmouseover="Tip('{$entries[im].summary|escape:"quotes"|regex_replace:"/[\r\t\n]+/":""}', SHADOW, true, ABOVE, true, WIDTH, 300)"
                                onmouseout="UnTip();" target="_blank" class="title">{$entries[im].title|escape:"html"}</a>
                        </li>
                        {/section}
                        </ul>
                    </div>
                </div>
                {/if}
                
                <div class="clearer"></div>
                
                <div class="rssBox">
                    <h2>
                        <img src="{$params.IMAGE_DIR}rss/rss.png" border="0" align="absmiddle" />
                        El Correo Gallego
                    </h2>
                    <div id="rssCorreo"></div>
                </div>
                {literal}
                <script type="text/javascript">
                /* <![CDATA[ */
                    //jQuery('#rssCorreo').feedreader('http://www.elcorreogallego.es/rss/rss.php?idWeb=1&idIdioma=1&idMenuTipo=1&sinEdicion=false&idMenu=1&txtTitulo=%DAltima+Hora', {theme: 'correo', timeout: 8000});
                /* ]]> */                            
                </script>
                {/literal}
                
                <div class="clearer"></div>
                {* 
                <div class="rssBox">
                    <h2>
                        <img src="{$params.IMAGE_DIR}rss/rss.png" border="0" align="absmiddle" />
                        La Opinión A Coruña
                    </h2>
                    <div id="rssOpinion"></div>
                </div>
                {literal}
                <script type="text/javascript">
                /* <![CDATA[ */
                    jQuery('#rssOpinion').feedreader('http://www.laopinioncoruna.es/elementosInt/rss/1', {timeout: 8000});
                /* ]]> */                            
                </script>
                {/literal}
                
                <div class="clearer"></div>
                
                <div class="rssBox">
                    <h2>
                        <img src="{$params.IMAGE_DIR}rss/rss.png" border="0" align="absmiddle" />
                        El Pais
                    </h2>
                    <div id="rssPais"></div>
                </div>
                {literal}
                <script type="text/javascript">
                /* <![CDATA[ */
                    jQuery('#rssPais').feedreader('http://www.elpais.com/rss.html', {timeout: 10000});
                /* ]]> */                            
                </script>
                {/literal *}
                
                
            </td>
            
			<td width="50%" valign="top">                
                <div>
                    
                        <div class="rssBox">
                            <h2>
                                <img src="{$params.IMAGE_DIR}rss/rss.png" border="0" align="absmiddle" />
                                Xornal.com                                                                
                            </h2>
                            <div id="rssXornal"></div>
                        </div>                            
                        {literal}
                        <script type="text/javascript">
                        /* <![CDATA[ */
                            //jQuery('#rssXornal').feedreader('http://www.xornal.com/rss/', {timeout: 20000});
                        /* ]]> */                            
                        </script>
                        {/literal}
                        {*
                        <div class="rssBox">
                            <h2>
                                <img src="{$params.IMAGE_DIR}rss/rss.png" border="0" align="absmiddle" />
                                La Voz de Galicia
                            </h2>
                            <div id="rssVoz"></div>
                        </div>
                        {literal}
                        <script type="text/javascript">
                        /* <![CDATA[ */
                            jQuery('#rssVoz').feedreader('http://www.lavozdegalicia.es/portada/index.xml', {theme: 'voz', timeout: 6000});
                        /* ]]> */                            
                        </script>
                        {/literal}
                        
                        <div class="rssBox">
                            <h2>
                                <img src="{$params.IMAGE_DIR}rss/rss.png" border="0" align="absmiddle" />
                                Faro de Vigo
                            </h2>
                            <div id="rssFaro"></div>
                        </div>
                        {literal}
                        <script type="text/javascript">
                        /* <![CDATA[ */
                            jQuery('#rssFaro').feedreader('http://www.farodevigo.es/elementosInt/rss/1', {timeout: 8000});
                        /* ]]> */                            
                        </script>
                        {/literal*}
                        
                    </div>                                        
            </td>
        </tr>
    </tbody>
</table>
    
{literal}
<script type="text/javascript">
jQuery(document).ready( function() {
    jQuery('div.rssBox h2').click(function(evt) {
        $(this).parent().find('div[id]').slideToggle("normal"); 
    });
});
</script>
{/literal}

{*<div id="feedControl"></div>
<script  type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("feeds", "1");

  function initialize() {
    var feedControl = new google.feeds.FeedControl();
    feedControl.addFeed("http://www.xornal.com/rss/", "Xornal");
    feedControl.addFeed("http://www.elpais.com/rss.html", "El Pais");
    feedControl.draw(document.getElementById("feedControl"));
  }
  google.setOnLoadCallback(initialize);
</script>
*}

{/block}
