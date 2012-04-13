/*
 * This file is part of the WebUIStats package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.define('{$define}', {
    extend:'Ext.panel.Panel',

    flex:1,

    listeners:{
        delay:50,
        render:function (el) {
            Highcharts.setOptions({
                global:{
                    useUTC:false
                }
            });
            window.chart = new Highcharts.StockChart({
                chart:{
                    animation: false,
                    renderTo:el.body.id,
                    events:{
                        load:function () {
                            this.pointsToAdd = new Array();
                            this.lastTs = new Array();

                            {$series_data}

                            var taskAddPointsAndRedraw = {
                                run:function () {

                                    var redraw = false;
                                    if (this.pointsToAdd.length) redraw = true;

                                    while (this.pointsToAdd.length) {
                                        var item = this.pointsToAdd.shift();
                                        var serie_num = item.serie_num;
                                        var points = item.points;
                                        for (var j in points) {
                                            if(this.series[serie_num].data.length) this.series[serie_num].data[0].remove(false);
                                            this.series[serie_num].addPoint(points[j], false, false, false);
                                        }
                                    }

                                    if (redraw)
                                    {
                                        this.redraw();
                                        this.series[0].show();
                                    }

                                },
                                scope:this,
                                interval:5000
                            }
                            Ext.TaskManager.start(taskAddPointsAndRedraw);
                        }
                    }
                },

                title:{
                    text:'{$title}',
                    style:{
                        fontSize:'12px'
                    },
                    floating: true
                },

                {$options}

            series : [
                {$series_config}
            ]
        });
}
}
})
;
