<?php

/*
 * This file is part of the WebUIStats package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebUIStats\Generator;

use Symfony\Component\Console\Output\OutputInterface;

class Creator
{
    protected $output;
    protected $config;

    public function __construct(OutputInterface $output, $yml_config_file)
    {
        $this->output = $output;

        $this->config = Config::loadConfig($yml_config_file);
    }

    public function create()
    {
        //    $this->output->writeln(print_r($this->config, true));

        // create app
        $path_to = realpath($this->config['app']['output_path']);
        $this->output->write('<info>Creating app to "' . $path_to . '"... </info>');
        $this->createApp();
        $this->output->writeln('<info>Ok</info>');

        // create menus
        $this->output->write('<info>Creating menus... </info>');
        $this->createMenus();
        $this->output->writeln('<info>Ok</info>');

        // create charts screens
        $this->output->write('<info>Creating screens... </info>');
        $this->createScreens();
        $this->output->writeln('<info>Ok</info>');
    }

    protected function createApp()
    {
        // copy base
        $this->recursiveCopy(__DIR__ . '/../../../templates/base/', $this->config['app']['output_path']);

        $path = $this->config['app']['output_path'] . '/index.html';
        $content = file_get_contents($path);
        $content = str_replace('{$title}', $this->config['app']['title'], $content);
        file_put_contents($path, $content);
    }

    protected function recursiveCopy($src, $dst)
    {
        if (is_dir($src)) {
            if (!file_exists($dst)) mkdir($dst);
            $files = scandir($src);
            foreach ($files as $file)
                if ($file != "." && $file != "..") $this->recursiveCopy("$src/$file", "$dst/$file");
        }
        else if (file_exists($src)) copy($src, $dst);
    }

    protected function createMenus()
    {
        // TODO: recursive menus

        $menus = array();
        $menus_functions = array();
        foreach ($this->config['app']['menu'] as $menu)
        {
            list($m, $f) = $this->tplMenu($menu['title'], $menu['items']);
            $menus[] = $m;
            $menus_functions[] = $f;
        }
        $menus = implode('                            ,', $menus);
        $menus_functions = implode('                        ,', $menus_functions);

        $path = $this->config['app']['output_path'] . '/app/view/ui/WebUIStatsViewport.js';
        $content = file_get_contents($path);
        $content = str_replace('{$menu}', $menus, $content);
        $content = str_replace('{$menu_functions}', $menus_functions, $content);
        file_put_contents($path, $content);
    }

    protected function tplMenu($text, $items)
    {
        $tpl_menu_items = array();
        $tpl_menu_functions_items = array();
        foreach ($items as $item)
        {
            list($m, $f) = $this->tplMenuItem($item['title'], $item['screen']);
            $tpl_menu_items[] = $m;
            $tpl_menu_functions_items[] = $f;
        }
        $text = addslashes($text);
        $tpl = "
                                {
                                    xtype: 'splitbutton',
                                    text: '$text',
                                    menu: {
                                        xtype: 'menu',
                                        width: 180,
                                        items: [
                                            " . implode('                                        ,', $tpl_menu_items) . "
                                        ]
                                    }
                                }
    ";
        $tpl_func = implode(",\n", $tpl_menu_functions_items);
        return array($tpl, $tpl_func);
    }

    protected function tplMenuItem($text, $screen)
    {
        $func_name = 'load_' . Config::filterJs($screen);
        $text = addslashes($text);
        $item = "
                                            {
                                                xtype: 'menuitem',
                                                text: '$text',
                                                handler: function() {
                                                    me." . $func_name . "(true);
                                                }
                                            }
    ";
        $item_func = "
    " . $func_name . ": function(setHash) {
        if(setHash) window.location.href = window.location.href + '#" . $func_name . "';
        var tabpanels = Ext.ComponentQuery.query('#maintabpanel');
        var tabpanel = tabpanels[0];
        tabpanel.add(
            Ext.create('WebUIStatsApp.view.displays." . Config::filterJs($screen) . "')
        ).show();
    }";
        return array($item, $item_func);
    }

    protected function createScreens()
    {
        foreach ($this->config['screens'] as $key => $screen)
        {
            $this->createScreen($key, $screen);
        }
    }

    protected function createScreen($key, $config)
    {
        $this->createCharts($key, $config['charts']);
        $this->createDisplay($key, $config['display']);
    }

    protected function createCharts($key_screen, $config)
    {
        foreach ($config as $key => $chart)
        {
            $this->createChart($key_screen . '_' . $key, $chart);
        }
    }

    protected function createChart($key, $config)
    {
        $tpl_path = __DIR__ . '/../../../templates/tpl/charts/base.js';
        $content = file_get_contents($tpl_path);
        $content = str_replace('{$define}', 'WebUIStatsApp.view.charts.' . Config::filterJs($key), $content);
        $content = str_replace('{$title}', $config['title'], $content);

        // series
        $series_config_js = array();
        $series_data_js = array();
        $i = 0;
        foreach ($config['series'] as $key_serie => $serie)
        {
            // TODO: if tab is closed, intervals still works

            $type = "type: 'line',";
            if (isset($serie['type'])) $type = "type: '" . $serie['type'] . "',";
            $series_config_js[] = "
                    {
                        name : '" . addslashes($serie['title']) . "',
                        " . $type . "
                        data : []
                    }
            ";
            $series_data_js_temp = "
                            Ext.data.JsonP.request(
                                {
                                    scope: this,
                                    url: '" . addslashes($serie['url']) . "',
                                    callback: function(success, data)
                                    {
                                        if (success)
                                        {
                                            // stats
                                            // get last timestamp to query to server for new data
                                            if(data.stats.length) this.lastTs[$i] = (new Date(data.stats[data.stats.length-1][0]));
                                            else this.lastTs[$i] = (new Date(0));
                                            // delete last item (it should be changing)
                                            data.stats.pop();
                                            // enqueue new points to add to chart
                                            this.pointsToAdd.push({
                                                                    serie_num: $i,
                                                                    points: data.stats
                                                                  });
";
            if ($i == 0) {
                $series_data_js_temp .= "
                                            // events
                                            var events;
                                            if(typeof(data.events) != 'undefined') events = data.events;
                                            this.eventsSerie = this.addSeries(
                                                {
                                                    type: 'flags',
                                                    name : 'events',
                                                    data : events,
                                                    shape: 'squarepin'
                                                }
                                            );
                                            this.dataEvents = data.events;
";
            }
            if (isset($config['interval'])) {
                $series_data_js_temp .= "
                                            var task = {
                                                run:function () {

                                                    var dateFrom = this.lastTs[$i].getFullYear() + '-' + (this.lastTs[$i].getMonth()+1) + '-' + this.lastTs[$i].getDate() + ' ' + this.lastTs[$i].getHours() + ':' + this.lastTs[$i].getMinutes() + ':' + this.lastTs[$i].getSeconds();
                                                    Ext.data.JsonP.request(
                                                        {
                                                            scope:this,
                                                            url:'" . addslashes($serie['url']) . "?dateFrom=' + dateFrom,
                                                            callback:function (success, data) {
                                                                if (success) {
                                                                    // stats
                                                                    if(data.stats.length >= 2)
                                                                    {
                                                                        // get last timestamp to query to server for new data
                                                                        this.lastTs[$i] = (new Date(data.stats[data.stats.length-1][0]));
                                                                        // delete last item (it should be changing)
                                                                        data.stats.pop();
                                                                        // enqueue new points to add to chart
                                                                        var data2add = {
                                                                            serie_num: $i,
                                                                            points: data.stats
                                                                        }
                                                                        this.pointsToAdd.push(data2add);
                                                                    }
";
                if ($i == 0) {
                    $series_data_js_temp .= "

                                                                    // events
                                                                    if (typeof(data.events) != 'undefined')
                                                                    {
                                                                        if(data.events.length)
                                                                        {
                                                                            // first new data item date has same date than last item in the chart?
                                                                            var firstNewDate = data.events[0].x;
                                                                            var lastGraphItemDate = this.dataEvents[this.dataEvents.length-1][0];
                                                                            if(firstNewDate == lastGraphItemDate)
                                                                            {
                                                                                // yes: delete it
                                                                                this.dataEvents.pop();
                                                                            }
                                                                            // push new data
                                                                            for(var i in data.events)
                                                                            {
                                                                                this.dataEvents.push(data.events[i]);
                                                                            }
                                                                            // set new data to chart
                                                                            this.eventsSerie.setData(this.dataEvents);
                                                                        }
                                                                    }
                                                                    ";
                }
                $series_data_js_temp .= "
                                                                }
                                                            }
                                                        });
                                                },
                                                scope:this,
                                                interval:" . ($config['interval'] * 1000) . "
                                            }
                                            Ext.TaskManager.start(task);
                                            ";
            }
            $series_data_js_temp .= "
                                        }
                                    }
                                }
                            );
            ";
            $series_data_js[] = $series_data_js_temp;
            $i++;
        }

        $content = str_replace('{$series_config}', implode("                    ,\n", $series_config_js), $content);
        $content = str_replace('{$series_data}', implode('', $series_data_js), $content);

        // options
        $config_options_default = array();
        if (isset($this->config['app']['charts']['default']['options'])) $config_options_default = $this->config['app']['charts']['default']['options'];
        $config_options_chart = array();
        if (isset($config['options'])) $config_options_chart = $config['options'];
        $config_options = array_merge($config_options_default, $config_options_chart);

        $js_options = "\n";
        $this->recursive_options($config_options, $js_options);
        $content = str_replace('{$options}', $js_options, $content);

        // write
        $dir = $this->config['app']['output_path'] . '/app/view/charts/';
        if (!file_exists($dir)) mkdir($dir);
        $path = $dir . Config::filterJs($key) . '.js';
        file_put_contents($path, $content);
    }

    protected function recursive_options($config_options, &$js_options)
    {
        foreach ($config_options as $koption => $option)
        {
            $items = array();
            foreach ($option as $k => $value)
            {
                if (is_array($value)) {
                    $js_options_aux = '';
                    $this->recursive_options(array($k => $value), $js_options_aux);
                    $items[] = $js_options_aux;
                }
                else
                {
                    if (is_string($value)) $value = '"' . $value . '"';
                    if ($value === true) $value = 'true';
                    if ($value === false) $value = 'false';
                    $items[] = "$k: $value";
                }
            }
            $js_options .= '                ' . $koption . ': {' . "\n";
            $js_options .= '                  ' . implode("                    ,\n", $items);
            $js_options .= "\n" . '                },' . "\n";
        }
    }

    protected function createDisplay($key, $config)
    {
        $tpl_path = __DIR__ . '/../../../templates/tpl/displays/' . $config['template'] . '.js';
        $content = file_get_contents($tpl_path);
        $content = str_replace('{$define}', 'WebUIStatsApp.view.displays.' . Config::filterJs($key), $content);
        $content = str_replace('{$title}', addslashes($config['title']), $content);

        $i = 1;
        foreach ($config['charts'] as $chart)
        {
            $chart = $key . '_' . $chart;
            $content = str_replace('{$chart' . $i . '}', "Ext.create('WebUIStatsApp.view.charts." . Config::filterJs($chart) . "')", $content);
            $i++;
        }

        // write
        $dir = $this->config['app']['output_path'] . '/app/view/displays/';
        if (!file_exists($dir)) mkdir($dir);
        $path = $dir . $key . '.js';
        file_put_contents($path, $content);
    }
}

