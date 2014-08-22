# werx/email

This is a port of the [CodeIgniter](https://github.com/EllisLab/CodeIgniter/) email class updated to work outside of CodeIgniter.

Full Documentation at <http://ellislab.com/codeigniter/user-guide/libraries/email.html>

## Usage

```php
$email = new \werx\email\Message();
$email->clear();
$email->from('me@example.com', 'My Name');
$email->to('you@example.com');
$email->subject('your subject line');
$email->attach($attachment, 'attachment', 'filename.html', 'text/html');
$email->message('Message body goes here.');
$email->send();
```

## Installation
This library is on Packagist at [werx/email](https://packagist.org/packages/werx/email). It can be installed and auto loaded with [composer](https://getcomposer.org).

Example composer.json

``` javascript
{
	"require": {
		"werx/email": "dev-master"
	}
}
```

Don't forget to include Composer's auto loader.

``` php
<?php
require 'vendor/autoload.php';
```

## Testing
There are unit tests available for this package. To run them, you must have [MailCatcher](http://mailcatcher.me/) installed and running.

```
vendor/bin/phpunit
```
