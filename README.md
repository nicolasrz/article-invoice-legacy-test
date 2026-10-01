# article-invoice-legacy

Le `InvoiceService` de l'article [InvoiceService ne devrait pas exister](https://nicolasrz.me/articles/invoiceservice-ne-devrait-pas-exister), dans une vraie application Symfony : Doctrine, une API de TVA en HTTP, des handlers Messenger.

Aucun test, volontairement : c'est le point de départ.

```
composer install
php bin/console doctrine:schema:create --env=test
php bin/phpunit
```
