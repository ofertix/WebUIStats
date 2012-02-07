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
                                {$chart1}
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
                                {$chart2}
                            ]
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);
    }
});