# Расширения для Symfony

## Установка

    composer require mekras/symfony-extensions-bundle

## Автоматизация загрузки настроек пакетов

Чтобы не прописывать в каждом пакете [всё необходимое](https://symfony.com/doc/current/bundles/extension.html)
для загрузки настроек, можно просто унаследовать свой класс `*Extension` от
`Mekras\SymfonyExtensionsBundle\DependencyInjection\Extension\Extension`:

```php
namespace AppBundle\Extension;

use Mekras\SymfonyExtensionsBundle\DependencyInjection\Extension\Extension;

/**
 * Расширение контейнера служб.
 */
class AppExtension extends Extension
{
}
```

Теперь ваше расширение автоматически будет загружать файлы из папки `Resources/config`:

* config.(xml|yml)
* services.(xml|yml)
