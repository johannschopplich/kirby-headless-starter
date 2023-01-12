# Kirby Headless

> 👉 Head over to the [Kirby Headless Starter](https://github.com/johannschopplich/kirby-headless-starter) repository for a complete setup ready to be used in production!

This plugin converts your Kirby site into a truly headless-first CMS. No visual data shall be presented. You will only be able to fetch JSON-encoded data – either by using Kirby's default template system or use KQL to fetch data in your consuming application.

Kirby's global routing will be overwritten by the plugin's [global routes](./src/extensions/routes.php) and [API routes](./src/extensions/api.php) for KQL.

## Key Features

- 🦭 Optional bearer token for authentication
- 🔒 **public** or **private** API
- 🧩 [KQL](https://github.com/getkirby/kql) with bearer token support via new `/api/kql` route
- ⚡️ Cached KQL queries
- 🌐 Multilang support for KQL queries
- 🗂 [Templates](#templates) present JSON instead of HTML
- 😵‍💫 No CORS issues!
- 🍢 Build your own [API chain](./src/extensions/routes.php)
- 🦾 Express-esque [API builder](#api-builder) with middleware support

## Use Cases

If you intend to fetch data from a headless Kirby instance, you have two options with this plugin:

- 1️⃣ use [Kirby's default template system](#templates)
- 2️⃣ use [KQL](#kirbyql)

Head over to the [usage](#usage) section for instructions.

## Requirements

- Kirby 3.8+

> Kirby is not a free software. You can try it for free on your local machine but in order to run Kirby on a public server you must purchase a [valid license](https://getkirby.com/buy).

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby-headless`.

### Composer

```bash
composer require johannschopplich/kirby-headless
```

## Setup

If you're not using the [Kirby Headless Starter](https://github.com/johannschopplich/kirby-headless-starter) but adding the plugin to an existing Kirby project, you have to make sure that the following configuration options are set in your `config.php`:

```php
# /site/config/config.php
return [
    // Enable basic authentication for the API and thus KQL
    'api' => [
        'basicAuth' => true
    ],

    // Default to token-based authentication
    'kql' => [
        'auth' => 'bearer'
    ]
];
```

## Usage

### Private vs. Public API

It's recommended to secure your API with a token. To do so, set the `headless.token` Kirby configuration option:

```php
# /site/config/config.php
return [
    'headless' => [
        'token' => 'test'
    ]
];
```

You will then have to provide the HTTP header `Authentication: Bearer ${token}` with each request.

> ⚠️ Without a token your page content will be publicly accessible to everyone.

> ℹ️ The internal `/api/kql` route will always enforce bearer authentication, unless you explicitly disable it in your config (see below).

### Templates

Create templates just like you normally would in any Kirby project. Instead of writing HTML, we build arrays and encode them to JSON. The internal route handler will add the correct content type and also handles caching (if enabled).

<details>
<summary>👉 Example template</summary>

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

</details>

<details>
<summary>👉 Fetch that data in the frontend</summary>

```js
import { $fetch } from "ofetch";

const API_TOKEN = "test";

const response = await $fetch("<website-url>/about", {
  headers: {
    Authentication: `Bearer ${API_TOKEN}`,
  },
});

console.log(response);
```

</details>

### KirbyQL

A new KQL endpoint supporting caching and bearer token authentication is implemented under `/api/kql`.

Fetch KQL query results like you normally would, but provide an `Authentication` header with your request:

<details>
<summary>👉 Fetch example</summary>

```js
import { $fetch } from "ofetch";

const API_TOKEN = "test";

const response = await $fetch("<website-url>/api/kql", {
  method: "POST",
  body: {
    query: "page('notes').children",
    select: {
      title: true,
      text: "page.text.toBlocks",
      slug: true,
      date: "page.date.toDate('d.m.Y')",
    },
  },
  headers: {
    Authentication: `Bearer ${API_TOKEN}`,
  },
});

console.log(response);
```

</details>

#### Basic Authentication for KQL

To **disable** the bearer token authentication for your Kirby instance and instead use the **basic authentication** method, set the following option in your `config.php`:

```php
'kql' => [
    'auth' => true
]
```

> ℹ️ The KQL default endpoint `/api/query` remains using basic authentication and also infers the `kql.auth` config option.

### Panel Settings

#### Preview URL to the Frontend

With the headless approach, the default preview link from the Kirby Panel won't make much sense, since it will point to the backend API itself. Thus, we have to overwrite it utilizing a custom page method in your site/page blueprints:

```yaml
options:
  # Or `site.frontendUrl` for the `site.yml`
  preview: "{{ page.frontendUrl }}"
```

Set your frontend URL in your `config.php`:

```php
# /site/config/config.php
return [
    'headless' => [
        'panel' => [
            // Preview URL for the Panel preview button
            'frontendUrl' => 'https://example.com'
        ]
    ]
];
```

If left empty, the preview button will be disabled.

#### Redirect to the Panel

Editors visiting the headless Kirby site may not want to see any API response, but use the Panel solely. To let them automatically be redirected to the Panel, set the following option in your Kirby configuration:

```php
# /site/config/config.php
return [
    'headless' => [
        'panel' => [
            // Redirect to the Panel if no authorization header is sent,
            // useful for editors visiting the site directly
            'redirect' => false
        ]
    ]
];
```

A middleware checks if an `Authentication` header is set, which is not the case in the browser context.

### Cross Origin Resource Sharing (CORS)

CORS is enabled by default. You can enhance the default CORS configuration by setting the following options in your `config.php`:

```php
# /site/config/config.php
return [
    'headless' => [
        // Default CORS configuration
        'cors' => [
            'allowOrigin' => '*',
            'allowMethods' => 'GET, POST, OPTIONS',
            'allowHeaders' => '*',
            'maxAge' => '86400',
        ]
    ]
];
```

## Advanced

### API Builder

This headless starter includes an Express-esque API builder, defined in the [`KirbyHeadless\Api\Api` class](./src/classes/Api.php). You can use it to re-use logic like handling a token or verifying some other incoming data.

Take a look at the [built-in routes](./src/extensions/routes.php) to get an idea how you can use the API builder to chain complex route logic.

It is also useful to consume POST requests including JSON data:

<details>
<summary>👉 Example custom route</summary>

```php
# /site/config/config.php
return [
    'routes' => [
        [
            'pattern' => 'post-example',
            'method' => 'POST',
            'action' => Api::createHandler(
                [\KirbyHeadless\Api\Middlewares::class, 'hasBearerToken'],
                [\KirbyHeadless\Api\Middlewares::class, 'hasBody'],
                function ($context) {
                    // Get the data of the POST request
                    $data = $context['body'];

                    // Do something with `$data` here

                    return Api::createResponse(201);
                }
            )
        ]
    ]
];
```

</details>

You you use one of the [built-in middlewares](./src/classes/Middlewares.php) or write custom ones in by extending the middleware class or creating a custom class defining your custom middleware functions:

<details>
<summary>👉 Example custom middleware</summary>

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

</details>

## FAQ

## Why Not Use Content Representations?

[Content representations](https://getkirby.com/docs/guide/templates/content-representations) are great. But they require a non-representing template. Otherwise, the content representation template just would not be read by Kirby. This means, you would have to create the following template structure:

- `default.php`
- `default.json.php`
- `home.php`
- `home.json.php`
- … and so on

To simplify this approach, we use the standard template structure, but encode each template's content as JSON via the internal [route middleware](./src/extensions/routes.php).

## License

[MIT](./LICENSE) License © 2022-2023 [Johann Schopplich](https://github.com/johannschopplich)
