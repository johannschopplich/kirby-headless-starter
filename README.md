# Kirby Headless Starter

> ℹ️ Send a request with a `Authorization: Bearer test` header to the [live playground](https://kirby-headless-starter.jhnn.dev) for an example response.

This starter kit is intended for an efficient and straight forward headless usage of Kirby. Thus, you will only be able to fetch JSON-encoded data. No visual data shall be presented. You can either use Kirby's default template system to build data (which will be auto-encoded to JSON) or use KQL to fetch data in your consuming application.

Routing and JSON-encoded responses are handled by the [internal routes](./site/config/routes.php).

## Key Features

- 🦭 Optional bearer token for authentication
- 🔒 **public** or **private** API
- 🧩 [KQL](https://github.com/getkirby/kql) with bearer token support via new `/api/kql` route
- ⚡️ Cached KQL queries
- 😵‍💫 No CORS issues!
- 🗂 [Templates](./site/templates/) present JSON instead of HTML
  - Fetch either `/example` or `/example.json`
  - You decide, which data you share
- 🦾 Express-esque [API builder](#api-builder) with middleware support

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

Optionally, adapt it's values.

> ℹ️ Make sure to not publish `KIRBY_HEADLESS_ALLOW_ORIGIN=*` when deploying, rather set the correct requesting origin.

## Usage

### Bearer Token

It's recommended to secure your API with a token. To do so, set the environment variable `KIRBY_HEADLESS_API_TOKEN` with a token string of your choice.

You will then have to provide the HTTP header `Authentication: Bearer ${token}` with each request.

> ⚠️ Without a token your page content will be publicly accessible to everyone.

### Public API

If the environment variable `KIRBY_HEADLESS_API_TOKEN` is left empty, the API will be publicly accessible.

> ℹ️ The internal `/api/kql` route will always enforce bearer authentication, unless you explicitly disable it in your config (see below).

### Templates

Create templates just like you normally would in any Kirby project. Instead of writing HTML, we build arrays and encode them to JSON. The internal route handler will add the correct content type and also handles caching (if enabled).

Example template:

```php
# /site/templates/about.php

$data = [
  'title' => $page->title()->value(),
  'layout' => $page->layout()->toLayouts()->toArray(),
  'address' => $page->address()->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'social' => $page->social()->toStructure()->toArray()
];

echo \Kirby\Data\Json::encode($data);
```

To fetch that data in the frontend:

```js
import { $fetch } from "ohmyfetch";

const apiToken = "test";

const response = await $fetch(
  "<website-url>/about.json",
  {
    // Optional, only if you use `KIRBY_HEADLESS_API_TOKEN`
    headers: {
      Authentication: `Bearer ${apiToken}`,
    },
  }
);

console.log(response);
```

### KQL

A new KQL endpoint supporting caching and bearer token authentication is implemented under `/api/kql`.

Fetch KQL query results like you always do, but provide an `Authentication` header with your request:

```js
import { $fetch } from "ohmyfetch";

const apiToken = "test";

const response = await $fetch("<website-url>/api/kql", {
  method: "POST",
  body: {
    query: "page('notes').children",
    select: {
      title: true,
      text: "page.text.toBlocks.toArray",
      slug: true,
      date: "page.date.toDate('d.m.Y')",
    },
  },
  headers: {
    Authentication: `Bearer ${apiToken}`,
  },
});

console.log(response);
```

To **disable** the bearer token authentication for your Kirby instance and instead use the **basic authentication** method, set the following in your [`config.php`](./site/config/config.php):

```php
'kql' => [
    'auth' => true
]
```

> ℹ️ The KQL default endpoint `/api/query` remains using basic authentication and also infers the `kql.auth` config option.

### API Builder

This headless starter includes an Express-esque API builder, defined in the [`KirbyHeadless\Api\Api` class](./src/Api.php). You can use it to re-use logic like handling a token or verifying some other incoming data.

Take a look at the [built-in routes](./site/config/routes.php) to get an idea how you can use the API builder to chain complex route logic.

It is also useful to consume POST requests including JSON data:

```php
# /site/config/routes.php
[
    'pattern' => 'post-example',
    'method' => 'POST',
    'action' => Api::createHandler(
        [Middlewares::class, 'hasBearerToken'],
        [Middlewares::class, 'hasBody'],
        function ($context) {
            // Get the data of the POST request
            $data = $context['body'];

            // Do something with `$data` here

            return Api::createResponse(201);
        }
    )
]
```

You you use one of the [built-in middlewares](./src/Middlewares.php) or write custom ones:

```php
/**
 * Check if `foo` is sent with the request
 * and bail with an 401 error if not
 *
 * @param array $context
 * @return mixed
 */
public static function hasFooParam($context)
{
    if (empty(get('foo'))) {
        return Api::createResponse(401);
    }
}
```

### Deployment

> ℹ️ See [ploi-deploy.sh](./scripts/ploi-deploy.sh) for exemplary deployment instructions.

> ℹ️ Some hosting environments require to uncomment `RewriteBase /` in [`.htaccess`](./public/.htaccess) to make site links work.

## Background

## Why Not Use Content Representations?

[Content representations](https://getkirby.com/docs/guide/templates/content-representations) are great. But they require a non-representing template. Otherwise, the content representation template just would not be read by Kirby. This means, you would have to create the following template structure:

- `default.php`
- `default.json.php`
- `home.php`
- `home.json.php`
- … and so on

To simplify this approach, we use the standard template structure, but encode each template's content as JSON via the internal [route middleware](./site/config/routes.php).

## License

[MIT](./LICENSE) License © 2022 [Johann Schopplich](https://github.com/johannschopplich)
