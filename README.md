# werx/email

Use [CodeIgniter's](https://github.com/EllisLab/CodeIgniter/) email library outside CodeIgniter.

Full Documentation at <http://ellislab.com/codeigniter/user-guide/libraries/email.html>

## Usage

```php
$email = new \werx\Email\Message();
$email->clear();
$email->from('me@example.com', 'My Name');
$email->to('you@example.com');
$email->subject('your subject line');
$email->attach($attachment, 'attachment', 'filename.html', 'text/html');
$email->message('Message body goes here.');
$email->send();
```

## Installation
This package is installable and autoloadable via Composer as [werx/email](https://packagist.org/packages/werx/email). If you aren't familiar with the Composer Dependency Manager for PHP, [you should read this first](https://getcomposer.org/doc/00-intro.md).

```bash
$ composer require werx/email --prefer-dist
```

## Testing
There are unit tests available for this package. To run them, you must have [MailCatcher](http://mailcatcher.me/) installed and running.

``` bash
$ vendor/bin/phpunit
```
