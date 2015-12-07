<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use reportmanager\models\ClassSearch;

use bburim\flot\Chart as Chart;
use bburim\flot\Plugin as Plugin;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model reportmanager\models\Reports */
/* @var $dataProvider yii\data\ActiveDataProvider */


if(count($model->columns) > 0):

    $models = $dataProvider->models;
    if(count($models) <= 0) return;

    $columns = $model->columns;
    if(count($columns) <= 0) return;

    $x_column = NULL;
    foreach($columns as $col) {
        if($col->alias === $model->graph_x) {
            $x_column = $col;
            break;
        }
    }
    $plot_data = [];
    foreach($columns as $col) {
        if($x_column && $col->alias === $x_column->alias) {
            continue;
        }
        $plot_data[] = [
            'label' => $col->label,
            'data' => array_map(function($item) use($col, $x_column){
                $alias_y = $col->alias;
                if(is_object($x_column)) {
                    $alias_x = $x_column->alias;
                    return [$x_column->functionObj->prepareGraph($item->$alias_x),$col->functionObj->prepareGraph($item->$alias_y)];
                } else {
                    return [$col->functionObj->prepareGraph($item->$alias_y)];
                }
            },$models),
            'lines'  => ['show' => true],
            'points' => ['show' => true],
        ];

    }
?>

<div class="reports-view-graph">
    <?= Chart::widget([
        'data' => $plot_data,
        'options' => [
            'xaxis' => [
                'mode' => is_object($x_column) && $x_column->functionObj->type === 'date' ? 'time' : 'none',
//                'timeformat' => $x_column->value,
//                'minTickSize' => [1, 'month'],
//                'monthNames' => $monthNames,
//                'min' => new JsExpression('localStorage.getItem("report-range-min")'),
//                'max' => new JsExpression('localStorage.getItem("report-range-max")'),
            ],
            'legend' => [
                'position'          => 'nw',
                'show'              => true,
                'margin'            => 10,
                'backgroundOpacity' => 0.5,
                'labelFormatter'    => new JsExpression('function(label, series){
                    plotData = $("#main_plot").data("plot").getData();
                    idx = plotData.indexOf(series);
                    return \'&nbsp; <a href="#" id="legend-item-\' + idx + \'" style="font-weight: bold;" onClick="togglePlot(\' + idx + \'); return false;">\'+label+\'</a>\';
                }'),
            ],
            'grid' => [
                'hoverable' => true,
                'clickable' => true,
                'autoHighlight' => true,
            ],
        ],
        'htmlOptions' => [
            'id' => 'main_plot',
            'style' => 'width:100%;height:400px;'
        ],
        'plugins' => [
            Plugin::TIME,
            Plugin::SELECTION,
        ],
    ])
    ?>

    <?php $this->registerJs('
        var plot = $("#main_plot").data("plot");
        $("#range_plot").bind("plotselected", function (event, ranges) {
            $.each(plot.getXAxes(), function(_, axis) {
                var opts = axis.options;
                opts.min = ranges.xaxis.from;
                opts.max = ranges.xaxis.to;
                localStorage.setItem("report-range-min",opts.min);
                localStorage.setItem("report-range-max",opts.max);
            });
            plot.setupGrid();
            plot.draw();
            plot.clearSelection();
        });

        $("<div id=\'tooltip\'></div>").css({
            position: "absolute",
            display: "none",
            border: "1px solid #fdd",
            padding: "2px",
            "background-color": "#fee",
            opacity: 0.80
        }).appendTo("body");

        $("#main_plot").bind("plothover", function (event, pos, item) {

            if (item) {
                var date = new Date(item.datapoint[0]),
                    sold = item.datapoint[1];
                var week=new Date(date);
                week.setDate(week.getDate() + 6);

                $("#tooltip").html(""
                    + "<p>" + item.series.label + "</p>"
                    + "<p>Период: " + '.
                    ''
//                        (call_user_func(function($type){
//                            switch($type) {
//                            case 'month':
//                                return 'plot.getOptions().xaxis.monthNames[date.getMonth()] + " " + date.getFullYear()';
//                            case 'week':
//                                return 'date.toLocaleDateString() + " - " + week.toLocaleDateString()';
//                            case 'day':
//                                return 'date.toLocaleDateString()';
//                            }
//                        },$report->dateScale))
                        .' + "</p> <p>Продано: " + sold + " шт.<p>"
                )
                    .css({top: item.pageY+5, left: item.pageX+5})
                    .fadeIn(200);
            } else {
                $("#tooltip").hide();
            }
        });

        $("#main_plot").bind("plotclick", function (event, pos, item) {
            if (item) {
                $("#clickdata").text(" - click point " + item.dataIndex + " in " + item.series.label);
                plot.highlight(item.series, item.datapoint);
            }
        });

        togglePlot = function(seriesIdx) {
            var plot = $("#main_plot").data("plot");
            var plotData = plot.getData();
            plotData[seriesIdx].lines.show = !plotData[seriesIdx].lines.show;
            plotData[seriesIdx].points.show = !plotData[seriesIdx].points.show;
            $("#legend-item-" + seriesIdx).css({
                "font-weight": plotData[seriesIdx].lines.show?"bold":"normal"
            });
            plot.setData(plotData);
            var axes = plot.getAxes();
//            axes.yaxis.options.max = 1500;

            // Redraw
            plot.setupGrid();
            plot.draw();
        }
    ') ?>

</div>
<?php endif ?>
