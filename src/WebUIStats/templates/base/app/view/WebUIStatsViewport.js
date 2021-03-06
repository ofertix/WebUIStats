/*
 * This file is part of the WebUIStats package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.define('WebUIStatsApp.view.WebUIStatsViewport', {
    extend:'WebUIStatsApp.view.ui.WebUIStatsViewport',

    initComponent:function () {
        var me = this;
        me.callParent(arguments);

        // load screen if it is set on hash
        var jash = window.location.hash;
        var jashs = jash.split('#');
        for (var i in jashs)
        {
            if(jashs[i] != '') eval('me.' + jashs[i] + '(false);');
        }
    }
});