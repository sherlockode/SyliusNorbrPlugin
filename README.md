# Sylius Norbr Plugin

## Overview

This plugin allows you to use Norbr to manage credit card payments on your e-commerce site.

## Installation

Install the plugin with composer :

```shell
$ composer require sherlockode/sylius-norbr-plugin
```

If your project does not use autoload, you have to enable the bundle yourself:

```php
// config/bundle.php

return [
    ...
    
    Sherlockode\SyliusNorbrPlugin\SherlockodeSyliusNorbrPlugin::class => ['all' => true],
];
```

To complete the installation, don't forget to publish assets:

```shell
$ php bin/console assets:install
```

## Configuration

Update your sylius installation by importing bundle configuration:

```yaml
# config/packages/_sylius.yaml

imports:
    # ...

    - { resource: "@SherlockodeSyliusNorbrPlugin/Resources/config/config.yaml" }
```

That's it ! Now you can enable the Norbr payment method in your admin panel.
