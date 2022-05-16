# Kirby Headless Starter

This starter kit is intended for an efficient and straight forward headless usage of Kirby. Thus, you will only be able to fetch JSON-encoded data. No visual data shall be presented.

Routing and JSON-encoded responses are handled by the internal [kirby-headless](./site/plugins/kirby-headless/) plugin.

## Key Features

- 🦭 Optional bearer token for authentication
- 🧩 [KQL](https://github.com/getkirby/kql) with bearer token support
  - Post requests to `/query`
- 🗂 [Templates](./site/templates/) present JSON instead of HTML
  - Fetch either `/example` or `/example.json`

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
cp .env.example .env
```

Optionally, adapt it's values.

## Usage

### Bearer Token

If you prefer to use a token to secure your Kirby installation, set the environment variable `KIRBY_HEADLESS_API_TOKEN` with a token of your choice.

You will then have to provide the header `Bearer ${token}` with each request.

> ⚠️ Without a token the `/query` route would be publicly accessible by everyone. Be careful.

### Data Fetching

You can fetch data by using KQL or Templates.

#### Templates

Create templates just like you normally would in any Kirby project. Instead of writing HTML, we build arrays and encode them to JSON. The internal headless plugin will add the correct content type and also handles correct caching.

Example template:

```php
# /site/templates/about.php

$data = [
  'title' => $page->title()->value(),
  'layout' => $page->layout()->toLayouts()->map(fn ($layout) => $layout->toArray())->values(),
  'address' => $page->address()->value(),
  'email' => $page->email()->value(),
  'phone' => $page->phone()->value(),
  'social' => $page->social()->toStructure()->map(fn ($social) => $social->toArray())->values()
];

echo toJson($data);
```

#### KQL

> ℹ️ Keep in mind the KQL endpoint `/api/query` remains and uses the username/password authentication.

KQL is available under `/query` and requires a bearer token set.

```js
import { $fetch } from "ohmyfetch"

const apiToken = "token"

const response = await $fetch(
  "https://example.com/query",
  {
    method: 'POST'
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
  },
);

console.log(response);
```

### Deployment

> ℹ️ See [ploi-deploy.sh](./scripts/ploi-deploy.sh) for exemplary deployment instructions.

> ℹ️ Some hosting environments require to uncomment `RewriteBase /` in [`.htaccess`](public/.htaccess) to make site links work.

## Background

## Why Not Use Content Representations?

[Content representations](https://getkirby.com/docs/guide/templates/content-representations) are great. But they require a non-representing template. Otherwise, the content representation template just would not be read by Kirby. This means, you would have to create the following template structure:

- `default.php`
- `default.json.php`
- `home.php`
- `home.json.php`
- … and so on

To simplify this approach, we use the standard template structure, but encode their content as JSON via the internal [kirby-headless](./site//plugins/kirby-headless/) plugin.

## License

[MIT](./LICENSE) License © 2022 [Johann Schopplich](https://github.com/johannschopplich)
