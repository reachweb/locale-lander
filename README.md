![locale_lander_bg](https://github.com/reachweb/locale-lander/assets/7423993/54b3f81a-8341-4182-94bb-85e60e149ad5)

# Locale Lander

Locale Lander is a simple addon for Statamic, particularly useful for multi-site setups involving multiple languages. It redirects users to the **default language of their browser** during their first visit or displays a banner informing the user that the content is available in their native language.

## How to Install

Install using composer:

```bash
composer require reachweb/locale-lander
```

## How to Use (Redirection)

To use the redirection feature, no additional configuration is needed after installation. The addon should function right out of the box. 

## How to Use (Banner)

To use the banner feature, Locale Lander provides a tag called, you guessed it, `locale-lander`.

First, you must disable automatic redirection. Publish the config file:

```bash
php artisan vendor:publish --tag=locale-lander-config
```

and in `config/locale-lander.php` disable redirection:

```php
'enable_redirection' => false,
```

Then, you can use the tag by adding the necessary code to a file that loads on all pages of your website, usually `layout.antlers.html`.

```
{{ locale_banner }}
    {{ if entry }}
        {{ partial src="vendor/locale-lander/banner" }}
    {{ /if }}
{{ /locale_banner }}
```

The tag will return an `array` that always contains an 'entry' key. If the Entry is unavailable in this locale, `entry` will be false. Otherwise, it will contain the `title` and the `url` of the entry. A `site` key is also provided with information about the user's locale site.

As you might have guessed, the addon contains an example banner template, styled with *Tailwind CSS*, that works out of the box.

Publish the template:

```bash
php artisan vendor:publish --tag=locale-lander-views
```

and you should be good to go. If you are not using Tailwind CSS, you will need to style it yourself.

In case you want to implement your own version, note that you should set a cookie named `locale_banner_closed` to prevent showing the banner to users who have closed it.

## Limiting redirection to the homepage only

You can limit redirection so that it only applies in the homepage. 

To do so, first if you haven't already done so, publish the config:


```bash
php artisan vendor:publish --tag=locale-lander-config
```

then in `config/locale-lander.php` enable `redirect_only_homepage`:

```php
'redirect_only_homepage' => true,
```

## How it Works

This addon automatically applies a middleware in the `statamic.web` middleware group. Here's the process:

- It checks the user's **browser language** and the **sites** defined in `statamic/sites.php`.
- If the current site's locale **differs** from the user's browser, it verifies if a site for that locale exists.
- If such a site exists and the current entry is **available** in that locale, it redirects the user there.
- A **session variable** is set to avoid repetitive redirection. This prevents redirect loops and accommodates users who prefer a different locale than their browser’s default.

## Common Issues

- The addon functions with URLs resolving to an `Entry`. It won't work if the first page a user visits isn't an Entry. (Future improvements are planned for this issue.)
- If the site's default locale matches the user's, or if the user initially visits the site in their locale but chooses to visit a different one, they will be redirected back to their original locale **once**. I am currently working to resolve this issue, but it may require modifications to the language switcher.
