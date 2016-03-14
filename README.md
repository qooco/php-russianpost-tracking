php-russianpost-tracking
====================

PHP library of tracking mailing via API Russian Post.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist vertx/php-russianpost-tracking "*"
```

or add

```
"vertx/php-russianpost-tracking": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the package is installed, simply use it in your code by  :

```php
<? $client = \vertx\russianpost\tracking\Client(); ?>```

For batch processing use this:

```php
<? $batchClient = \vertx\russianpost\tracking\BatchClient(); ?>```