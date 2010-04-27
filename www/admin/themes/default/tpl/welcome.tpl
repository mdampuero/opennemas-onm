{include file="header.tpl"}

<table class="adminform">
    <tbody>
        <tr>
            <td colspan="2">
                <div id="cpanel" >
                    
                    {acl isAllowed="ARTICLE_CREATE"}
                	<div style="float: left;">
                        <div class="icon">                            
                            <a href="/admin/article.php?action=new&category=221">
                                <img alt="" src="{$params.IMAGE_DIR}article_add.gif"/>
                                <span>Nuevo Articulo</span>
                            </a>
                            
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="OPINION_CREATE"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/opinion.php?action=new">
                                <img alt="" src="{$params.IMAGE_DIR}opinion.png"/>
                                <span>Nueva Opini&oacute;n</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="ADVERTISEMENT_CREATE"}
                	<div style="float: left;">
                        <div class="icon">
                            <a href="/admin/advertisement.php?action=new&category=0">
                                <img alt="" src="{$params.IMAGE_DIR}advertisement.png"/>
                                <span>Nueva Publicidad</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="MEDIA_ADMIN"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/mediamanager.php">
                                <img alt="" src="{$params.IMAGE_DIR}add_photo.png"/>
                                <span>Subir Foto</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="ARTICLE_FRONTPAGE"}
                	<div style="float: left;">
                        <div class="icon">
                            <a href="/admin/article.php?action=list&category=home">
                                <img alt="" src="{$params.IMAGE_DIR}iconos/frontpage_manager.png"/>
                                <span>Gestor Portada</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="ARTICLE_LIST_PEND"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/article.php?action=list_pendientes&category=todos">
                                <img alt="" src="{$params.IMAGE_DIR}iconos/draft_manager.png"/>
                                <span>Gestor Pendientes</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="OPINION_ADMIN"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/opinion.php?action=list">
                                <img alt="" src="{$params.IMAGE_DIR}iconos/draft_manager.png"/>
                                <span>Gestor Opini&oacute;n</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="COMMENTS_ADMIN"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/comment.php?action=list&category=todos">
                                <img alt="" src="{$params.IMAGE_DIR}comments_manager.png"/>
                                <span>Gestor Comentarios</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                    {acl isAllowed="NEWSLETTER_ADMIN"}
                    <div style="float: left;">
                        <div class="icon">
                            <a href="/admin/newsletter.php">
                                <img alt="" src="{$params.IMAGE_DIR}newsletter/mail_message_new.png"/>
                                <span>Envío Boletín</span>
                            </a>
                        </div>
                    </div>
                    {/acl}  
                    
                    {acl isAllowed="XML_IMPORT"}
                    <div style="float: left;">
                        <div class="icon">
                             <a href="importXML.php">
                                <img border="0" src="{$params.IMAGE_DIR}xml.png">
                                <span>Importar XML</span>
                            </a>
                        </div>
                    </div>
                    {/acl}
                    
                </div>
            </td>
        </tr>
        
        <tr>
            <td width="50%" valign="top">
                {if count($messages.entries)>0}
                <div class="rssBox">
                    <h2 style="cursor: pointer;">
                        <img src="{$params.IMAGE_DIR}gmail_ico.png" border="0" align="absmiddle" />
                        {$messages.total} emails sin leer
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
                    var correoRss = new FeedReader('rssCorreo', 'http://www.elcorreogallego.es/rss/rss.php?idWeb=1&idIdioma=1&idMenuTipo=1&sinEdicion=false&idMenu=1&txtTitulo=%DAltima+Hora', {theme: 'correo', timeout: 8000});
                /* ]]> */                            
                </script>
                {/literal}
                
                <div class="clearer"></div>
                
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
                    new FeedReader('rssOpinion', 'http://www.laopinioncoruna.es/elementosInt/rss/1', {timeout: 8000});
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
                    new FeedReader('rssPais', 'http://www.elpais.com/rss.html', {timeout: 10000});
                /* ]]> */                            
                </script>
                {/literal}
                
                
            </td>
            
			<td width="50%" valign="top">
                {*<div class="pane-sliders" id="content-pane">
                    <div class="cpanelright">
                        <h3 id="cpanel-panel-custom">
                            <span>Bienvenido al Panel de control del Backend de OpenNeMaS!</span>
                        </h3>
                        <div style="border-top: medium none; border-bottom: medium none; overflow: hidden; padding-top: 0px; padding-bottom: 0px; height: 366px;">
                            <div style="padding: 5px;">
                                <ul>
                                {section name="it" loop=$news}
                                <li>
                                    <a href="{$news[it].link}" title="{$news[it].description|escape:"html"}" target="_blank">
                                        {$news[it].title|escape:"html"}</a>
                                </li>
                                {/section}
                                </ul>
                            </div>                                                        
                        </div>
                    </div>                    
                </div>*}
                
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
                            var xornalRss = new FeedReader('rssXornal', 'http://www.xornal.com/rss/', {timeout: 20000});
                        /* ]]> */                            
                        </script>
                        {/literal}
                    
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
                            var vozRss = new FeedReader('rssVoz', 'http://www.lavozdegalicia.es/portada/index.xml', {theme: 'voz', timeout: 6000});
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
                            var faroRss = new FeedReader('rssFaro', 'http://www.farodevigo.es/elementosInt/rss/1', {timeout: 8000});
                        /* ]]> */                            
                        </script>
                        {/literal}
                        
                    </div>
            </td>
        </tr>
    </tbody>
</table>
    
{literal}
<script type="text/javascript">
document.observe('dom:loaded', function() {
    $$('div.rssBox').each(function(item) {
        item.select('h2')[0].observe('click', function(evt) {
            Effect.toggle(this.up().select('div')[0], 'slide', {
                afterFinish: function(effect) {
                    $(effect.element).setStyle({height: '280px', overflow: 'auto'});
                }
            });
        }, item);
    });
});
</script>
{/literal}

{include file="footer.tpl"}
