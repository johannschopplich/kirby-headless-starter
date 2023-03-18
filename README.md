# Kirby Headless Starter

> ℹ️ Send a request with a `Authorization: Bearer test` header to the [live playground](https://kirby-headless-starter.jhnn.dev) for an example response.

This starter kit provides a minimal setup for a headless Kirby site. Only the API is exposed, no visual data is presented. You will only be able to fetch JSON-encoded data – either by using Kirby's default template system or use KQL to fetch data in your consuming application.

Routing and JSON-encoded responses are handled by the internal [`kirby-headless`](https://github.com/johannschopplich/kirby-headless) plugin, specifically its [global routes](https://github.com/johannschopplich/kirby-headless/blob/main/src/extensions/routes.php) and [API routes](https://github.com/johannschopplich/kirby-headless/blob/main/src/extensions/api.php) for KQL.

This project works well with [`nuxt-kql`](https://nuxt-kql.jhnn.dev).

## Example Projects

- [`cacao-kit-frontend`](https://github.com/johannschopplich/cacao-kit-frontend): 🍫 Best practice Nuxt starter for Kirby with i18n & blocks
- [`kirby-nuxt-starterkit`](https://github.com/johannschopplich/kirby-nuxt-starterkit): 💚 Kirby's sample site – ported to Nuxt 3 and KirbyQL

## Key Features

- 🦭 Optional bearer token for authentication
- 🔒 **public** or **private** API
- 🧩 [KQL](https://github.com/getkirby/kql) with bearer token support via new `/api/kql` route
- ⚡️ Cached KQL queries
- 🌐 Multi-lang support for KQL queries
- 🗂 [Templates](./site/templates/) present JSON instead of HTML
- 😵‍💫 No CORS issues!
- 🍢 Build your own [API chain](https://github.com/johannschopplich/kirby-headless/blob/main/src/extensions/routes.php)
- 🦾 Express-esque [API builder](https://github.com/johannschopplich/kirby-headless#api-builder) with middleware support

## Use Cases

If you intend to fetch data from a headless Kirby instance, you have two options with this plugin:

- 1️⃣ use [Kirby's default template system](#templates)
- 2️⃣ use [KQL](#kirbyql)

Head over to the [usage](#usage) section for instructions.

## Prerequisites

- PHP 8.0+

> Kirby is not a free software. You can try it for free on your local machine but in order to run Kirby on a public server you must purchase a [valid license](https://getkirby.com/buy).

## Setup

### Composer

Kirby-related dependencies are managed via [Composer](https://getcomposer.org) and located in the `vendor` directory. To install them, run:

```bash
composer install
```

### Environment Variables

Duplicate the [`.env.development.example`](.env.development.example) as `.env`:

```bash
cp .env.development.example .env
```

Optionally, adapt its values.

> ℹ️ Make sure to set the correct requesting origin instead of the wildcard `KIRBY_HEADLESS_ALLOW_ORIGIN=*` for your deployment.

## Usage

### Private vs. Public API

It's recommended to secure your API with a token. To do so, set the environment variable `KIRBY_HEADLESS_API_TOKEN` to a token string of your choice.

You will then have to provide the HTTP header `Authentication: Bearer ${token}` with each request.

> ⚠️ Without a token your page content will be publicly accessible to everyone.

> ℹ️ The internal `/api/kql` route will always enforce bearer authentication, unless you explicitly disable it in your config (see below).

### Templates

> 📖 [See documentation in `kirby-headless` plugin](https://github.com/johannschopplich/kirby-headless#templates)

### KirbyQL

> 📖 [See documentation in `kirby-headless` plugin](https://github.com/johannschopplich/kirby-headless#kirbyql)

### Panel Settings

#### Preview URL to the Frontend

With the headless approach, the default preview link from the Kirby Panel won't make much sense, since it will point to the backend API itself. Thus, we have to overwrite it utilizing a custom page method in your site/page blueprints:

```yaml
options:
  # Or `site.frontendUrl` for the `site.yml`
  preview: "{{ page.frontendUrl }}"
```

Set your frontend URL in your `.env`:

```
KIRBY_HEADLESS_FRONTEND_URL=https://example.com
```

If left empty, the preview button will be disabled.

#### Redirect to the Panel

Editors visiting the headless Kirby site may not want to see any API response, but use the Panel solely. To let them automatically be redirected to the Panel, set the following option in your Kirby configuration:

```php
# /site/config/config.php
return [
    'headless' => [
        'panel' => [
            'redirect' => false
        ]
    ]
];
```

### Deployment

> ℹ️ See [ploi-deploy.sh](./scripts/ploi-deploy.sh) for exemplary deployment instructions.

> ℹ️ Some hosting environments require uncommenting `RewriteBase /` in [`.htaccess`](./public/.htaccess) to make site links work.

## License

[MIT](./LICENSE) License © 2022-2023 [Johann Schopplich](https://github.com/johannschopplich)
