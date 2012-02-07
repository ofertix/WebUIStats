/*
 * This file is part of the WebUIStats package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.Loader.setConfig({
    enabled:true
});
Ext.Loader.setPath('Ext.ux', 'app/plugins/ux');

Ext.application({
    name:'WebUIStatsApp',

    appFolder:'app',

    launch:function () {
        Ext.QuickTips.init();

        var cmp1 = Ext.create('WebUIStatsApp.view.WebUIStatsViewport', {
            renderTo:Ext.getBody()
        });
        cmp1.show();
    }
});
