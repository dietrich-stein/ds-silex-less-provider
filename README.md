DS-Silex-Less service provider
================

Simple less php service provider for Silex that uses https://github.com/leafo/lessphp as parser.

Simply specify paths for your .less files and target .css and if your .less files are newer than final .css file, final .css will be regenerated

This project is a fork of FF-Silex-Less from https://github.com/darklow/ff-silex-less-provider

This version removes the ability to provide multiple .less source files and timestamp-based change detection.

This version adds a cache file setting and use of the lessphp cachedCompile feature for automatic detection of changes to imported .less files.

Installation
------------

Create a composer.json in your projects root-directory:

    {
        "require": {
            "dietrich-stein/ds-silex-less-provider": "*"
        }
    }

and run:

    curl -s http://getcomposer.org/installer | php
    php composer.phar install


Register provider
-----------------

You must specify three required parameters and one optional:
* **less.source** - Path to .less file. Changes to any .less files included by @import will trigger recompilation.
* **less.target** - Path to target .css file
* **less.cache** - Path to cache file used by the lessphp cachedCompile() function
* **less.target_mode** - Optionally you can specify file mode mask

``` php
<?php
use DS\ServiceProvider\LessServiceProvider;

// Register FF Silex Less service provider
$this->register(new LessServiceProvider(), array(
    'less.sources' => array(__DIR__.'/../../Resources/less/style.less'), // specify .less file
    'less.target' => __DIR__.'/../../web/css/style.css', // specify .css target file
    'less.cache' => __DIR__.'/../../web/css/style.cache', // specify cache file
    'less.target_mode' => 0775, // Optional
));
```

License
-------

'DS-Silex-Less' is licensed under the MIT license.