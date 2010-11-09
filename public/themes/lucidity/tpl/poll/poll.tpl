{*
OpenNeMas project
@category   OpenNeMas
@package    OpenNeMas
@theme      Lucidity
@copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
Smarty template: article.tpl
*}
{include file="module_head.tpl"}

<div id="container" class="span-24">

    {* Cambiar color del menú segun la section *}
    {* publicidad interstitial interior *}
    {insert name="intersticial" type="150"}
    {include file="ads/widget_ad_top.tpl" type1='101' type2='102'}
    <div class="wrapper clearfix">
        <div class="container clearfix span-24 last">
            <div id="header" class="">
               {include file="frontpage/frontpage_header.tpl"}
               {include file="frontpage/frontpage_menu.tpl"}
            </div>
            <div id="main_content" class="single-article span-24">
                <div class="in-big-title span-24">

                        <h1>{$poll->title}</h1>

                    <p class="in-subtitle">{$poll->subtitle|clearslash}</p>
                    <div class="info-new">
                        <span class="author">{*Total votos: {$poll->total_votes|clearslash}*} </span> </span>
                    </div>
                </div><!-- fin lastest-news -->
                <div class="span-24">
                    <div class="layout-column first-column span-16">
                        <div class="border-dotted">
                            <div class="span-16 toolbar">
                                {*include file="utilities/widget_ratings.tpl"*}
                                <div class="vote-block span-10 ">&nbsp;{$msg}</div>
                                {include file="utilities/widget_utilities.tpl" long="true"}
                            </div><!--fin toolbar -->
                            <div class="content-article">

                                    <div>
                                    <form name="poll" method="post" action="#" >


                                        <div class="questions">
                                             {section name=i loop=$items}
                                                 <div class="items"><input type="radio" value="{$items[i].pk_item}" name="respEncuesta" alt="{$items[i].item}"/> {$items[i].item}</div>
                                             {/section}
                                        </div>

                                        <div>
                                                <input type="hidden" name="id" value='{$poll->pk_poll}'/>
                                           <input type="hidden" name="op" value='votar'/>
                                           <a class="button-votar-form" onClick="document.poll.submit()" id="enquisa" >
                                            Votar
                                        </a></div>
                                    </form>

                                    </div>

                    <script type="text/javascript"> {*EN OPTIONS is3D: true,*}
                    {if $poll->visualization eq '0'}
                        {literal}
                            google.load('visualization', '1', {'packages':['corechart']});

                            google.setOnLoadCallback(drawChart);

                           // draws it.

                          function drawChart() {
                           var options ={width: 600, height: 340,  title: '{/literal}{$poll->title}{literal}'};
                            var data_rows = {/literal}{$data_rows}{literal};

                          // Create our data table.
                            var data = new google.visualization.DataTable();
                            data.addColumn('string', 'Name');
                            data.addColumn('number', 'Value');
                            data.addRows(data_rows);

                            // Instantiate and draw our chart, passing in some options.
                            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                            chart.draw(data, options);

                          }

                           {/literal}

                        {else}
                            {literal}

                                google.load('visualization', '1', {'packages': ['imagechart']});

                            function drawVisualization() {
                                  // Create and populate the data table.
                                  var data = new google.visualization.DataTable();
                                  data.addColumn('string', 'Name');
                                  data.addColumn('number', 'Value');
                                  data.addRows(3);
                                  var data_rows = {/literal}{$data_rows}{literal};
                                  data.addRows(data_rows);

                                  var options = {};

                                  // 'bhg' is a horizontal grouped bar chart in the Google Chart API.
                                  // The grouping is irrelevant here since there is only one numeric column.
                                  options.cht = 'bhs';

                                  // Add a data range.
                                  var min = 0;
                                  var max ={/literal}{$max_value}{literal};
                                  options.chds = min + ',' + max;

                                  // Now add data point labels at the end of each bar.

                                  // Add meters suffix to the labels.
                                  var meters = 'N**';
                                  // Draw labels in pink.
                                  var color = '333333';

                                  options.chco= '990099|109618|DC3912|FF9900|3366CC';
                                  // Google Chart API needs to know which column to draw the labels on.
                                  // Here we have one labels column and one data column.
                                  // The Chart API doesn't see the label column.  From its point of view,
                                  // the data column is column 0.
                                  var index = 0;

                                  // -1 tells Google Chart API to draw a label on all bars.
                                  var allbars = -1;

                                  options.chtt ="{/literal}{$poll->title}{literal}";

                                  // 10 pixels font size for the labels.
                                  var fontSize = 14;

                                  // Priority is not so important here, but Google Chart API requires it.
                                  var priority = 0;

                                  options.chm = [meters, color, index, allbars, fontSize, priority].join(',');

                                  // Create and draw the visualization.
                                    new google.visualization.ImageChart(document.getElementById('chart_div')).
                                    draw(data, options);
                                }
                           google.setOnLoadCallback(drawVisualization);


                            {/literal}


                        {/if}
                        </script>
                        <div id="chart_div"></div>

                 </div><!-- /content-article -->

                           {* <hr class="new-separator"/> *}
                            {include file="poll/widget_polls.tpl"}

                           {include file="module_comments.tpl" content=$contentId nocache}
                        </div>
                    </div>
                    {include file="article/article_last_column.tpl"}
                </div>
            </div><!-- fin #main_content -->
        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->
    <div class="wrapper clearfix">
        <div class="container clearfix span-24 last">
            <div id="footer" class="">
                {include file="ads/widget_ad_bottom.tpl"  type1='109' type2='110'}
                {include file="frontpage/frontpage_footer.tpl"}
            </div><!-- fin .footer -->
        </div><!-- fin .container -->
    </div><!-- .wrapper -->

</div><!-- #container -->

    {literal}
        <script type='text/javascript'>
        jQuery(document).ready(function(){
            $("#tabs").tabs();
            $lock=false;
            jQuery("div.share-actions").hover(
              function () {
                if (!$lock){
                  $lock=true;
                  jQuery(this).children("ul").fadeIn("fast");
                }
                $lock=false;
              },
              function () {
                if (!$lock){
                  $lock=true;
                  jQuery(this).children("ul").fadeOut("fast");
                }
                $lock=false;
              }
            );
        });
        </script>
    {/literal}
    {include file="misc_widgets/widget_analytics.tpl"}
</body>
</html>
