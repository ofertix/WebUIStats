/*
 * This file is part of the WebUIStats package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.define('WebUIStatsApp.view.ui.WebUIStatsViewport', {
    extend:'Ext.container.Viewport',

    layout:{
        type:'fit'
    },

    initComponent:function () {
        var me = this;

        Ext.applyIf(me, {
            items:[
                {
                    xtype:'panel',
                    layout:{
                        align:'stretch',
                        type:'vbox'
                    },
                    dockedItems:[
                        {
                            xtype:'toolbar',
                            dock:'top',
                            items:[
                                {$menu}
                            ]
                        }
                    ],
                    items:[
                        {
                            xtype:'tabpanel',
                            id:'maintabpanel',
                            activeTab:0,
                            flex:1,
                            plugins:Ext.create('Ext.ux.TabCloseMenu', {
                                extraItemsTail:[
                                    '-',
                                    {
                                        text:'Closable',
                                        checked:true,
                                        hideOnClick:true,
                                        handler:function (item) {
                                            currentItem.tab.setClosable(item.checked);
                                        }
                                    }
                                ],
                                listeners:{
                                    aftermenu:function () {
                                        currentItem = null;
                                    },
                                    beforemenu:function (menu, item) {
                                        var menuitem = menu.child('*[text="Closable"]');
                                        currentItem = item;
                                        menuitem.setChecked(item.closable);
                                    }
                                }
                            })
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);
    }
});