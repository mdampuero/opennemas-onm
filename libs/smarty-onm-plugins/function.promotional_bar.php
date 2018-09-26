<?php
/**
 * Returns the promotional bar html
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_promotional_bar($params, &$smarty)
{
    $html = '';
    $htm  = '';

    $enabled = getService('core.security')->hasExtension('PROMOTIONAL_BAR');

    if ($enabled) {
        $createNew = _('Create free newspaper');
        $goToWeb   = _('Opennemas homepage');
        $help      = _('Help Desk');
        $about     = _('About us');
        $onmText   = _('The best Internet service for your online digital newspaper');

        // Html promotional bar with sidebar like paper.li
        $html = '<link rel="stylesheet" type="text/css" href="/assets/css/promotional_bar.css">
                <script src="/assets/plugins/jquery-slider/jquery.sidr.min.js"></script>
                <link rel="stylesheet" type="text/css" href="/assets/plugins/jquery-slider/css/jquery.sidr.dark.css" >
                <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
                <div id="promotional-bar">
                    <div class="bar-left">
                        <a id="promotional-menu" href="#" class="left-content">
                            <i class="fa fa-bars"></i>
                            <img src="' . $smarty->parent->image_dir . 'logos/logo-opennemas-small.png" alt="Opennemas">
                        </a>
                    </div>
                    <div class="bar-right">
                        <a href="http://www.opennemas.com/signup" class="right-content" target="_blank">
                            ' . $createNew . '
                        </a>
                    </div>
                </div>
                <div id="sidr" style="display:none;">
                    <div class="sidr-title">
                            <i class="fa fa-close"></i>
                            Opennemas
                    </div>
                    <ul>
                        <li>
                            <a href="http://www.opennemas.com/">
                                <i class="fa fa-home"></i>
                                <div class="menu-name">
                                    ' . $goToWeb . '
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="http://www.opennemas.com/signup">
                                <i class="fa fa-newspaper-o"></i>
                                <div class="menu-name">
                                    ' . $createNew . '
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="http://help.opennemas.com/knowledgebase">
                                <i class="fa fa-question"></i>
                                <div class="menu-name">
                                    ' . $help . '
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="http://www.openhost.es/">
                                <i class="fa fa-lightbulb-o"></i>
                                <div class="menu-name">
                                    ' . $about . '
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="https://es-es.facebook.com/pages/Opennemas-el-CMS-para-tu-periódico-digital/">
                                <i class="fa fa-facebook-square"></i>
                                <div class="menu-name">
                                    Facebook
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="https://twitter.com/opennemas">
                                <i class="fa fa-twitter"></i>
                                <div class="menu-name">
                                    Twitter
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <script>
                    $(document).ready(function() {
                        $("#promotional-menu").sidr({
                            speed: 500,
                            onOpen: function(){
                                $( ".bar-left" ).animate({ "left": "+=265px" }, 500 );
                                $( ".bar-right" ).animate({ "right": "-=265px" }, 500 );
                            },
                            onClose: function(){
                                $( ".bar-left" ).animate({ "left": "0" }, 500 );
                                $( ".bar-right" ).animate({ "right": "0" }, 500 );
                            },
                        });
                        $(".sidr-title i.fa-close").on("click", function(){
                            $.sidr("close", "sidr");
                        });
                    });
                </script>';

        // Simple html promotional bar
        $htm = '<link rel="stylesheet" type="text/css" href="/assets/css/promotional_bar.css">
                <div id="onm-bar">
                    <div class="content">
                        <div class="logo">
                            open<strong>nemas</strong>
                        </div>
                        <div class="text">
                            ' . $onmText . '
                        </div>
                        <div class="sign-up">
                            <a href="http://www.opennemas.com" target="_blank">
                                ' . $createNew . '
                            </a>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        $("#onm-bar").on("click", function(){
                            window.open("http://www.opennemas.com","_blank");
                        });
                    });
                </script>';
    }

    return $htm;
}
