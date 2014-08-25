<div class="content">
    <div class="title">
        <h2>{t}Zend Opcache status{/t}</h2>
    </div>
    <div class="top-buttons">
        <li>
            <!-- <a href="{url name=manager_framework_opcache_status action='reset'}" class="btn btn-large">
                <img src="{$params.IMAGE_DIR}template_manager/delete48x48.png" />
                <br />
                Reset cache
            </a> -->
        </li>
    </div>

<div class="opcache-stats">
    <div class="alert alert-block alert-error fade in" ng-if="serverData.not_supported_message">
        <button type="button" class="close" data-dismiss="alert"></button>
        <h4 class="alert-heading"><i class="icon-warning-sign"></i> Error!</h4>
        <p>[% serverData.not_supported_message %]</p>
    </div>
    <tabset class="tab-form">
        <tab heading="{t}Status{/t}">
            <table>
                <tr ng-repeat="(key, value) in serverData.status_key_values">
                    <th>[% key %]</th>
                    <td>[% value %]</td>
                </tr>
            </table>

            <div id="graph" class="clearfix">
                <div id="graphcontainer" class="clearfix">
                    <form>
                        <label><input type="radio" name="dataset" value="memory" checked> Memory</label>
                        <label><input type="radio" name="dataset" value="keys"> Keys</label>
                        <label><input type="radio" name="dataset" value="hits"> Hits</label>
                    </form>

                    <div id="stats-opcode"></div>
                </div>
            </div>
        </tab>
        <tab heading="{t}Configuration{/t}">
            <table>
                {foreach $directive_key_values as $key => $value}
                <tr><th>{$key}</th><td>{$value}</td></tr>
                {/foreach}
            </table>
        </tab>
        <tab heading="Scripts ([% status.scripts.length %])">
            <table style="font-size:0.8em;">
                <tr>
                    <th width="70%">Path</th>
                    <th width="20%">Memory</th>
                    <th width="10%">Hits</th>
                </tr>
                {foreach $files_key_values as $dir}
                    <tr onclick="toggleVisible('#head-{$dir@iteration}', '#row-{$dir@iteration}')" id="head-{$dir@iteration}">
                        <th>{$dir['name']} ({$dir['count']})</th>
                        <th>{$dir['total_memory_consumption']}</th>
                        <th></th>
                    </tr>
                    {foreach $dir['files'] as $fileName => $fileInfo}
                        <tr id="row-{$dir@iteration}">
                            <td>
                                {if $dir['count'] > 1}
                                    {$fileInfo['full_path']}
                                {else}
                                    {$fileInfo['full_path']}
                                {/if}
                            </td>
                            <td>{$fileInfo['memory_consumption_human_readable']}</td>
                            <td>{$fileInfo["hits"]}</td>
                        </tr>
                    {/foreach}


                {/foreach}
            </table>
        </tab>
    </tabset>
</div>

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/d3/3.0.1/d3.v3.min.js"></script>
<script type="text/javascript">
    // var hidden = {};
    // function toggleVisible(head, row) {
    //     if (!hidden[row]) {
    //         d3.selectAll(row).transition().style('display', 'none');
    //         hidden[row] = true;
    //     } else {
    //         d3.selectAll(row).transition().style('display');
    //         hidden[row] = false;
    //     }
    // }

    // var dataset = {
    //     memory: [{$mem['used_memory']}, {$mem['free_memory']}, {$mem['wasted_memory']}],
    //     keys:   [{$stats['num_cached_keys']}, {$free_keys}, 0],
    //     hits:   [{$stats['misses']}, {$stats['hits']}, 0]
    // };

    // var width = 400,
    //         height = 400,
    //         radius = Math.min(width, height) / 2,
    //         colours = ['#B41F1F', '#1FB437', '#ff7f0e'];
    // d3.scale.customColours = function() {
    //     return d3.scale.ordinal().range(colours);
    // };
    // var colour = d3.scale.customColours();
    // var pie = d3.layout.pie()
    //         .sort(null);

    // var arc = d3.svg.arc()
    //         .innerRadius(radius - 20)
    //         .outerRadius(radius - 50);
    // var svg = d3.select("#graph").append("svg")
    //         .attr("width", width)
    //         .attr("height", height)
    //         .append("g")
    //         .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

    // var path = svg.selectAll("path")
    //         .data(pie(dataset.memory))
    //         .enter().append("path")
    //         .attr("fill", function(d, i) { return colour(i); })
    //         .attr("d", arc)
    //         .each(function(d) { this._current = d; }); // store the initial values

    // d3.selectAll("input").on("change", change);
    // set_text("memory");

    // function set_text(t) {
    //     if (t=="memory") {
    //         d3.select("#stats-opcode").html(
    //             "<table><tr><th style='background:#B41F1F;'>Used</th><td>{$mem['used_memory']}</td></tr>"+
    //             "<tr><th style='background:#1FB437;'>Free</th><td>{$mem['free_memory']}</td></tr>"+
    //             "<tr><th style='background:#ff7f0e;' rowspan=\"2\">Wasted</th><td>{$mem['wasted_memory']}</td></tr>"+
    //             "<tr><td>{number_format($mem['current_wasted_percentage'],2)}%</td></tr></table>"
    //         );
    //     } else if(t=="keys") {
    //         d3.select("#stats-opcode").html(
    //             "<table><tr><th style='background:#B41F1F;'>Cached keys</th><td>"+dataset[t][0]+"</td></tr>"+
    //             "<tr><th style='background:#1FB437;'>Free Keys</th><td>"+dataset[t][1]+"</td></tr></table>"
    //         );
    //     } else if(t=="hits") {
    //         d3.select("#stats-opcode").html(
    //             "<table><tr><th style='background:#B41F1F;'>Misses</th><td>"+dataset[t][0]+"</td></tr>"+
    //             "<tr><th style='background:#1FB437;'>Cache Hits</th><td>"+dataset[t][1]+"</td></tr></table>"
    //         );
    //     }
    // }

    // function change() {
    //     path = path.data(pie(dataset[this.value])); // update the data
    //     path.transition().duration(750).attrTween("d", arcTween); // redraw the arcs
    //     set_text(this.value);
    // }

    // function arcTween(a) {
    //     var i = d3.interpolate(this._current, a);
    //     this._current = i(0);
    //     return function(t) {
    //         return arc(i(t));
    //     };
    // }
</script>
