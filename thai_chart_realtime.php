<?php
header("Content-Type:text/html; charset=big5-hkscs");
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');
function json($data)
{
    $encode               = json_encode($data);
    $jsonp_callback_key   = 'callback';
    $jsonp_get            = $_GET[$jsonp_callback_key];

    //純json 格式
    if (empty($_GET[$jsonp_callback_key]))
    {
        return $encode;
    }

    //jsonp 方法
    return "{$jsonp_get}($encode)";
}

?>
<!DOCTYPE html>
<html>
<head>
    <script
            src="https://code.jquery.com/jquery-3.2.1.js"
            integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.3/highcharts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.3/highcharts-more.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=big5-hkscs">
    <script>

    </script>
    <div id="form" style="min-width:100px;height:50px" align="middle">
        <form name="myForm">
            要搜尋的資訊:
            <select name="search" id="search" class="search">
            </select>
        </form>
    </div>
        <div id="container" style="min-width:784px;height:400px" align="middle">
            <script>
                function addOption(pos){
                    $.getJSON('http://60.249.6.104:8787/api/get/3/data/realtime', function (data) {
                        $.each(data, function (i, datas) {
                            //console.log(i)
                            //console.log(datas)
                            var objSelect = document.myForm.search;
                            // 取得欄位值
                            strName = i;
                            strValue = i;
                            // 建立Option物件
                            var objOption = new Option(strName, strValue);
                            objSelect.add(objOption, pos);

                        })
                    })
                }
                addOption()

                Highcharts.setOptions({
                    global: {
                        useUTC: false
                    }
                });
                function activeLastPointToolip(chart) {
                    var points = chart.series[0].points;
                    chart.tooltip.refresh(points[points.length -1]);
                };
                var chart;
                $(function () {
                    $.getJSON('http://60.249.6.104:8787/api/get/3/data/realtime', function (keys,data) {
                        var keys = keys
                        var _data = data;
                        dataArray = new Array();
                        for (w in _data) {
                            dataArray.push(w);
                        }
                        var chart = Highcharts.chart('container', {
                            chart: {
                                type: 'area',
                                animation: Highcharts.svg, // don't animate in old IE
                                marginRight: 140,
                                events: {
                                    load: function () {
                                        var series = this.series[0],
                                            chart = this;
                                        setInterval(function () {
                                            $.getJSON('http://60.249.6.104:8787/api/get/3/data/realtime', function (i, data) {
                                                var updateData = data;
                                                var key = i;//{pv_volt: 641, pv_cur: 83, pv_power: 539, Rediation: 511, pv_Temp: 511, …}
                                                updateArray = new Array();
                                                for (q in updateData) {
                                                    updateArray.push(q);
                                                }
                                                var x = (new Date()).getTime() // current time
                                                var y = key['pv_volt'];
                                                //updateData[0]取value
                                                series.addPoint([x, y], true, true);
                                                activeLastPointToolip(chart)
                                            })
                                        }, 10000);
                                    }
                                }
                            },
                            title: {
                                text: 'pv_volt'
                            },
                            credits: {
                                enabled: false
                            },
                            xAxis: {
                                type: 'datetime',
                                tickPixelInterval: 200
                            },
                            yAxis: {
                                title: {
                                    text: '值'
                                },
                                plotLines: [{
                                    value: 0,
                                    width: 1,
                                    color: '#808080'
                                }]
                            },
                            tooltip: {
                                formatter: function () {
                                    return '<b>' + this.series.name + '</b><br/>' +
                                        Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
                                        Highcharts.numberFormat(this.y, 2);
                                }
                            },
                            legend: {
                                itemHoverStyle: {
                                    color: 'pink'
                                },
                                align: 'right',
                                verticalAlign: 'top',
                                layout: 'vertical',
                                itemMarginBottom: 10,
                                itemMarginTop: 13,
                                symbolPadding: 10,
                                reversed: true,
                                shadow: true,
                                x: 0,
                                width: 100

                            },
                            exporting: {
                                enabled: false
                            },
                            series: [{
                                name: 'pv_volt',
                                data: (function () {
                                    var data = [],
                                        time = (new Date()).getTime(),
                                        i;
                                    for (i = -19; i <= 0; i += 1) {
                                        data.push({
                                            x: time + i * 10000,
                                            y: keys['pv_volt']
                                        });
                                    }
                                    return data;
                                }())
                            }]
                        },function (c) {
                            activeLastPointToolip(c)
                        });
                        var $cSel = $('select[name="search"]'); //指定要處理的特定元素物件名稱
                        $(".search").change(function(){
                            chart.update({
                                chart: {
                                    events: {
                                        load: function () {
                                            var label = this.label('載入中，請稍候', 100, 120)
                                                .attr({
                                                    fill: Highcharts.getOptions().colors[0],
                                                    padding: 10,
                                                    r: 5,
                                                    zIndex: 8
                                                });
                                            var series = this.series[0],
                                                chart = this;
                                            setInterval(function () {
                                                $.getJSON('http://60.249.6.104:8787/api/get/3/data/realtime', function (i, data) {
                                                    var updateData = data;
                                                    var key = i;//{pv_volt: 641, pv_cur: 83, pv_power: 539, Rediation: 511, pv_Temp: 511, …}
                                                    updateArray = new Array();
                                                    for (q in updateData) {
                                                        updateArray.push(q);
                                                    }
                                                    var x = (new Date()).getTime() // current time
                                                    var y = key[$cSel.val()];
                                                    //updateData[0]取value
                                                    series.addPoint([x, y], true, true);
                                                    activeLastPointToolip(chart)
                                                })
                                            }, 10000);
                                        }
                                    }
                                },
                                title: {
                                    text: $cSel.val()
                                },
                                series: {
                                    name: $cSel.val(),
                                    data: (function () {
                                        var data = [],
                                            time = (new Date()).getTime(),
                                            i;
                                        for (i = -19; i <= 0; i += 1) {
                                            data.push({
                                                x: time + i * 10000,
                                                y: keys[$cSel.val()]
                                            });
                                        }
                                        return data;
                                    }())
                                }
                            });
                        });
                    });
                });

            </script>

    </div>

    </div>
    <title>測試</title>
</head>
</html>
