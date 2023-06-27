A laravel app to import and display a compact status of browser features (from the [Can I Use database](https://caniuse.com)) in a compact form, to allow easy browsing of which features are now usable across common browsers along with what the feature does, links to usage/spec and an example.

To import:

```shell
php artisan app:updatedata
```

or (if running through sail containers)

```shell
sail artisan app:updatedata
```
