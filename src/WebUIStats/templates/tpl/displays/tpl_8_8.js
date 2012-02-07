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

    layout:{
        align:'stretch',
        type:'vbox'
    },

    title:'{$title}',

    closable:true,

    initComponent:function () {
        var me = this;

        Ext.applyIf(me, {
            items:[
                {
                    xtype:'container',
                    layout:{
                        align:'stretch',
                        type:'vbox'
                    },
                    flex:1,
                    items:[
                        {
                            xtype:'container',
                            layout:{
                                align:'stretch',
                                type:'hbox'
                            },
                            flex:1,
                            items:[
                                {$chart1},
                                {$chart2},
                                {$chart3},
                                {$chart4}
                            ]
                        },
                        {
                            xtype:'container',
                            layout:{
                                align:'stretch',
                                type:'hbox'
                            },
                            flex:1,
                            items:[
                                {$chart5},
                                {$chart6},
                                {$chart7},
                                {$chart8}
                            ]
                        },
                        {
                            xtype:'container',
                            layout:{
                                align:'stretch',
                                type:'hbox'
                            },
                            flex:1,
                            items:[
                                {$chart9},
                                {$chart10},
                                {$chart11},
                                {$chart12}
                            ]
                        },
                        {
                            xtype:'container',
                            layout:{
                                align:'stretch',
                                type:'hbox'
                            },
                            flex:1,
                            items:[
                                {$chart13},
                                {$chart14},
                                {$chart15},
                                {$chart16}
                            ]
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);
    }
});