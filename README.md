# Locale Lander

Locale Lander is a simple addon for Statamic, particularly useful for multi-site setups involving multiple languages. It redirects users to the default language of their browser during their first visit.

## How to Install

Install using composer:

``` bash
composer require reachweb/locale-lander
```

By default, the addon is disabled to prevent any unintended breakage. 

To enable it, publish the config file:

``` bash
php artisan vendor:publish --tag locale-lander-config
```

and set enable to `true`:

```php
return [
    'enable' => true,
];
```
Once enabled, you are all set.

## How to Use

Typically, no additional configuration is needed after installation. The addon should function right out of the box.

## How it Works

This addon is actually a single middleware. Here's the process:

- It checks the user's **browser language** and the **sites** defined in `statamic/sites.php`.
- If the current site's locale **differs** from the user's browser, it verifies if a site for that locale exists.
- If such a site exists and the current entry is **available** in that locale, it redirects the user there.
- A **session variable** is set to avoid repetitive redirection. This prevents redirect loops and accommodates users who prefer a different locale than their browser’s default.

## Common Issues

- The addon functions with URLs resolving to an `Entry`. It won't work if the first page a user visits isn't an Entry. (Future improvements are planned for this issue.)
- If the site's default locale matches the user's, or if the user initially visits the site in their locale but chooses to visit a different one, they will be redirected back to their original locale once. I am currently working to resolve this issue, but it may require modifications to the language switcher.
