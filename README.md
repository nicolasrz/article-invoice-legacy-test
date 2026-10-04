# article-invoice-legacy-test

The code behind the articles [InvoiceService shouldn't exist](https://nicolasrz.me/en/articles/invoiceservice-ne-devrait-pas-exister) and [Tester du code qui n'a pas été pensé pour](https://nicolasrz.me/articles/tester-du-code-qui-n-a-pas-ete-pense-pour) (in French): a catch-all `InvoiceService`, inside a real Symfony application (Doctrine, an HTTP VAT API, Messenger handlers).

`src/` stays bad, on purpose. `createInvoice` is tested in two ways:

- `tests/CreateInvoiceTest.php`: Detroit, with real objects and an SQLite database; only the VAT API is faked (`tests/FakeTaxApi.php`);
- `tests/CreateInvoiceMockedTest.php`: London, everything is mocked.

```
composer install
php bin/phpunit --testdox
```

Two branches replay the refactorings from the article:

- `demo/renommage`: `computeVat()` becomes `vatFor()`. The London test breaks, the Detroit test stays green;
- `demo/big-bang`: `createInvoice` is rewritten from scratch. The London test breaks everywhere, the Detroit test stays green.
