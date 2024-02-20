# Kirby Headless Starter

> [!TIP]
> Send a request with a `Authorization: Bearer test` header to the [live playground](https://kirby-headless-starter.jhnn.dev) for an example response.

This starter kit provides a minimal setup for a headless Kirby site. It lets you fetch JSON-encoded data from your Kirby site using either KQL or Kirby's default template system.

Routing and JSON-encoded responses are handled by the internal [`kirby-headless`](https://github.com/johannschopplich/kirby-headless) plugin, specifically its [global routes](https://github.com/johannschopplich/kirby-headless/blob/main/src/extensions/routes.php) and [API routes](https://github.com/johannschopplich/kirby-headless/blob/main/src/extensions/api.php) for KQL.

This project works well with [`nuxt-kql`](https://nuxt-kql.byjohann.dev).

## Example Projects

- [`cacao-kit-frontend`](https://github.com/johannschopplich/cacao-kit-frontend): 🍫 Best practice Nuxt starter for Kirby with i18n & blocks
- [`kirby-nuxt-starterkit`](https://github.com/johannschopplich/kirby-nuxt-starterkit): 💚 Kirby's sample site – ported to Nuxt 3 and KirbyQL

## Key Features

- 🦭 Optional bearer token for authentication
- 🔒 Choose between **public** or **private** API
- 🧩 Extends [KQL](https://github.com/getkirby/kql) with bearer token support (new `/api/kql` route)
- 🧱 [Resolves UUIDs](https://github.com/johannschopplich/kirby-headless#field-methods) to actual file and page objects
- ⚡️ Cached KQL queries
- 🌐 Multi-language support for KQL queries
- 🗂 [Kirby templates](https://github.com/johannschopplich/kirby-headless#templates) that output JSON instead of HTML
- 😵‍💫 Seamless experience free from CORS issues
- 🍢 Build your own [API chain](https://github.com/johannschopplich/kirby-headless/blob/main/src/extensions/routes.php)
- 🦾 Express-esque [API builder](https://github.com/johannschopplich/kirby-headless#api-builder) with middleware support

## Use Cases

This starter kit is designed for developers who want to leverage Kirby's backend to serve content to a frontend application, static site generator, or mobile app. You can either opt-in to headless functionality for your existing Kirby site or use this plugin to build a headless-first CMS from scratch.

Here are scenarios where the Kirby Headless Starter is particularly useful:

- 1️⃣ If you prefer querying data with [Kirby Query Language](#kirbyql).
- 2️⃣ When you wish to utilize [Kirby's default template system](#templates) to output JSON.

Detailed instructions on how to use these features can be found in the [usage](#usage) section.

## Prerequisites

- PHP 8.1+

Kirby is not free software. However, you can try Kirby and the Starterkit on your local machine or on a test server as long as you need to make sure it is the right tool for your next project. … and when you’re convinced, [buy your license](https://getkirby.com/buy).

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

> [!NOTE]
> Make sure to set the correct requesting origin instead of the wildcard `KIRBY_HEADLESS_ALLOW_ORIGIN=*` for your deployment.

## Usage

### KirbyQL

> 📖 [See documentation in `kirby-headless` plugin](https://github.com/johannschopplich/kirby-headless#kirbyql)

### Private vs. Public API

It's recommended to secure your API with a token. To do so, set the environment variable `KIRBY_HEADLESS_API_TOKEN` to a token string of your choice.

You will then have to provide the HTTP header `Authentication: Bearer ${token}` with each request.

> [!WARNING]
> Without a token your page content will be publicly accessible to everyone.

> [!NOTE]
> The internal `/api/kql` route will always enforce bearer authentication, unless you explicitly disable it in your config (see below).

### Templates

> 📖 [See documentation in `kirby-headless` plugin](https://github.com/johannschopplich/kirby-headless#templates)

### Panel Settings

#### Preview URL to the Frontend

With the headless approach, the default preview link from the Kirby Panel won't make much sense, since it will point to the backend API itself. Thus, we have to overwrite it utilizing a custom page method in your site/page blueprints:

```yaml
options:
  # Or `site.frontendUrl` for the `site.yml`
  preview: "{{ page.frontendUrl }}"
```

Set your frontend URL in your `.env`:

```ini
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

> [!NOTE]
> See [ploi-deploy.sh](./scripts/ploi-deploy.sh) for exemplary deployment instructions.
>
> Some hosting environments require uncommenting `RewriteBase /` in [`.htaccess`](./public/.htaccess) to make site links work.

## License

[MIT](./LICENSE) License © 2022-PRESENT [Johann Schopplich](https://github.com/johannschopplich)
