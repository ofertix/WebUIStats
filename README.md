What is "WebUIStats"?
=======================
WebUIStats is the component that generates web UI interface with charts from your stats.


Screenshots
============
![Example 1](https://github.com/ofertix/WebUIStats/raw/master/doc/resources/stats1.png "Example 1")

![Example2](https://github.com/ofertix/WebUIStats/raw/master/doc/resources/stats2.png "Example2")


Requirements
============
- PHP 5.3.2 and up.
- RabbitMQ or ZMQ.


Libraries and services used
===========================

- PHP
	- Pimple
	- Symfony Components:
		- ClassLoader
		- YAML
		- Console
	- Monolog
- ExtJS 4
- HighStocks


Installation
============

The best way to install is to clone the repository and then configure as you need. See "Configuration" section.

After cloning you must install dependencies using "composer":

	php composer.phar update


Usage
=====

Generate your charts using:

	php app/generator.php interface:generate app/config/test/app.yml

Optional: You could copy generated code in `web/app` to somewhere you want.

In your browser insert the url where it is the generated code, example:

	http://localhost/WebUIStats/web/index.html


Configuration
=============

All configuration is done using a YAML files.

Config files are structured in one main file (app.yml) and some screens config files (screen_foo.yml).

app.yml:

- output_path:

	- directory where to write generated code.

- title:
	- title that will appears in the browser.

- menu:
	- menu items that links our charts.

- charts:
	- default charts options.


screen_foo.yml:

- charts:

	- chart title, options and data series.

- display:

	- set template that defines charts layout.


See config file for more details.


Extra notes
===========

Use of ZMQ is discontinued because a memory leak using ZMQ with OpenPGM PUB/SUB.
