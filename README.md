# article-invoice-legacy-test

Le code des articles [InvoiceService ne devrait pas exister](https://nicolasrz.me/articles/invoiceservice-ne-devrait-pas-exister) et [Tester du code qui n'a pas été pensé pour](https://nicolasrz.me/articles/tester-du-code-qui-n-a-pas-ete-pense-pour) : un `InvoiceService` fourre-tout, dans une vraie application Symfony (Doctrine, une API de TVA en HTTP, des handlers Messenger).

`src/` reste mauvais, volontairement. `createInvoice` est testée de deux façons :

- `tests/CreateInvoiceTest.php` : Detroit, avec les vrais objets et une base SQLite ; seule l'API de TVA est fausse (`tests/FakeTaxApi.php`) ;
- `tests/CreateInvoiceMockedTest.php` : London, tout est mocké.

```
composer install
php bin/phpunit --testdox
```

Deux branches rejouent les refactorings de l'article :

- `demo/renommage` : `computeVat()` devient `vatFor()`. Le test London casse, le test Detroit reste vert ;
- `demo/big-bang` : `createInvoice` est réécrite de fond en comble. Le test London casse de partout, le test Detroit reste vert.
