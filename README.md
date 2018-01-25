# Avatar

Avatar Generator package for Laravel 5.5

## Installation

To install, run the following in your project directory:

``` bash
$ composer require div-art/avatar
```

Then in `config/app.php` add the following to the `providers` array:

```
\DivArt\Avatar\AvatarServiceProvider::class,
```

Also in `config/app.php`, add the Facade class to the `aliases` array:

```
'Avatar' => \DivArt\Avatar\Facades\Avatar::class,
```

## Configuration

To publish Avatar's configuration file, run the following `vendor:publish` command:

```
php artisan vendor:publish --provider="DivArt\Avatar\AvatarServiceProvider"
```

This will create a avatar.php in your config directory.

## Usage

**Be sure to include the namespace for the class wherever you plan to use this library**

```
use DivArt\Avatar\Facades\Avatar;
```

## Code Examples

```php

$avatar = Avatar::make('John Doe');

$avatar->save();

```

## Method chaining

```php

$avatar = Avatar::make('John Doe')->size(100)->shape('square')->format('png')->save('avatarName');

```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.