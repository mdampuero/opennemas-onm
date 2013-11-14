<!doctype html>
<html>
<head>
    {block name="title"}<title>{t 1=$server->get('SERVER_NAME')}%1 not found - Opennemas{/t}</title>{/block}

    <meta charset="utf-8">
    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="robots"    content="noindex, nofollow" />
    <meta name="description" content="OpenNeMaS - An specialized CMS focused in journalism." />
    <meta name="keywords" content="CMS, Opennemas, OpenHost, journalism" />

    <!-- Apple devices fullscreen -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <!-- Apple devices fullscreen -->
    <meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />

    <!-- Favicon -->
    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">

    {block name="header-css"}
        {css_tag href="/bootstrap/bootstrap.css" media="screen" common=1}
        {css_tag href="/style.css" media="screen" common=1}
        {css_tag href="/fontawesome/font-awesome.min.css" common=1}
    {/block}

    <style>
    .global-nav, .navbar, .navbar .navbar-inner {
        background:none;
        box-shadow:none;
        border:none;
        position:relative;
    }
    #content {
        margin:80px auto 120px auto;
    }
    footer {
        /*border-top:1px solid #eee;*/
        padding-top:10px;
        text-align:center;
        margin:0 auto;
        font-size:1em;
    }
    @media (max-width: 767px) {
        .navbar-innner {
            text-align:center;
        }
        .logoonm {
            display:block;
            float:none;
        }
        .instance-error .desc {
            font-size:2.6em;
        }
        .instance-error .explanation {
            line-height:1.3em;
        }
        footer {
            font-size:1.3em;
        }
        footer nav {
            width:100%;
        }
        footer nav ul li {
            display:inline-block;
            text-align:center;
            margin-bottom:10px;
        }
        footer .copyright {
            display: none;
        }
    }
    </style>
</head>

<body class='error instance-error'>

    <header class="clearfix">
        <div class="navbar navbar-inverse global-nav">
            <div class="wrapper-content navbar-inner">
                <a  href="" class="brand ir logoonm">OpenNemas</a>
                <div class="nav-collapse collapse navbar-inverse-collapse">
                    <ul class="nav pull-left">
                        <li>
                            <a href="http://www.opennemas.com">{t}The CMS for journalism{/t}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <div id="content" role="main">
        <div class="wrapper-content">
        {block name="content"}
        <div class="desc">{t 1=$server->get('SERVER_NAME')}%1 doesn’t exist.{/t}</div>
        <div class="explanation">
            <a href="http://www.opennemas.com/#singup">{t 1=$server->get('SERVER_NAME')}Do you want to register %1?{/t}</a>
        </div>
        {/block}
        </div>
    </div>

    {block name="copyright"}
    <footer class="wrapper-content">
        <div class="clearfix">
            <nav class="left">
                <ul>
                    <li>&copy; {strftime("%Y")} OpenHost S.L.</li>
                    <li><a href="http://www.opennemas.com" target="_blank" title="Go to opennemas website">{t}About{/t}</a></li>
                    <li><a href="http://help.opennemas.com" target="_blank" title="{t}Help{/t}">{t}Help{/t}</a></li>
                    <li><a href="http://help.opennemas.com/knowledgebase/articles/235300-opennemas-pol%C3%ADtica-de-privacidad"
                           target="_blank" title="{t}Privacy Policy{/t}">{t}Privacy Policy{/t}</a></li>
                    <li><a href="http://help.opennemas.com/knowledgebase/articles/235418-terminos-de-uso-de-opennemas"
                           target="_blank" title="{t}Legal{/t}">{t}Legal{/t}</a></li>


                </ul><!-- / -->
            </nav>
            <nav class="right copyright">
                <ul>
                    <li>
                        <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FOpenNemas%2F282535299100&amp;width=100&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;send=false&amp;appId=229591810467176" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
                    </li>
                    <li>
                        <div class="g-follow" data-annotation="bubble" data-height="20" data-href="//plus.google.com/103592875488169354089" data-rel="publisher"></div>
                        <script type="text/javascript">
                          (function() {
                            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                            po.src = 'https://apis.google.com/js/plusone.js';
                            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                          })();
                        </script>
                    </li>
                    <li>
                        {literal}
                        <a href="https://twitter.com/opennemas" class="twitter-follow-button" data-show-count="true" data-show-screen-name="false">Seguir</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                        {/literal}
                    </li>
                </ul>
            </nav>
        </div><!-- / -->
    </footer>
    {/block}
    {block name="footer-js"}
        {script_tag src="/jquery/jquery.min.js" common=1}
        {script_tag src="/libs/bootstrap.js" common=1}
        {script_tag src="/libs/modernizr.min.js" common=1}
    {/block}
</body>
</html>
