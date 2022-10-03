# Zend Rector

This project contains [Rector rules](https://github.com/rectorphp/rector) to update your code from old Zend to not-so-old Zend.

After upgrading, consider using other frameworks, like [Symfony](https://symfony.com/) 😏

## Install

Install rector and this package:

```bash
composer require rector/rector --dev
composer require revenkroz/zend-rector --dev
```

## Use Sets

Add set to your `rector.php`:

```php
use Rector\Config\RectorConfig;
use Revenkroz\ZendRector\Set\ZendSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        ZendSetList::ZEND_3,
    ]);
};
```
