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

use Symfony\Component\Yaml\Yaml;

class Config
{
    static public function loadConfig($yml_config_file)
    {
        $config = Yaml::parse($yml_config_file);
        $config['output_path'] = realpath(dirname($yml_config_file)) . '/' . $config['output_path'];

        $screens = array();
        self::configExtractCharts($config, $screens);

        // load charts yml files
        $config_screens = array();
        foreach ($screens as $chart_file)
        {
            $config_screens[$chart_file] = Yaml::parse(realpath(dirname($yml_config_file)) . '/' . Config::filterJs($chart_file) . '.yml');
        }

        return array(
            'app' => $config,
            'screens' => $config_screens);
    }

    static protected function configExtractCharts($array, &$result)
    {
        foreach ($array as $key => $item)
        {
            if (is_array($item)) $r = self::configExtractCharts($item, $result);
            if ($key == 'screen') $result[] = $item;
        }
    }

    static public function filterJs($text)
    {
        return preg_replace('/[^ a-zA-Z0-9]/', '_', $text);
    }


}