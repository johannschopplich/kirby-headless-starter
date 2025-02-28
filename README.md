# Kirby Headless Starter

> [!TIP]
> Send a request with a `Authorization: Bearer test` header to the [live playground](https://kirby-headless-starter.byjohann.dev) for an example response.

This starter kit provides a minimal setup for a headless Kirby site. It allows you to fetch JSON-encoded data from your Kirby site using either KQL or Kirby's default template system. Under the hood, it uses the [Kirby Headless Plugin](https://kirby.tools/docs/headless) to provide a headless API.

This project works well with [`Nuxt KQL`](https://nuxt-kql.byjohann.dev).

## Example Projects

- [`cacao-kit-frontend`](https://github.com/johannschopplich/cacao-kit-frontend): 🍫 Best practice Nuxt and KQL starter for your headless Kirby CMS
- [`kirby-nuxt-starterkit`](https://github.com/johannschopplich/kirby-nuxt-starterkit): 💚 Kirby's sample site – ported to Nuxt and Kirby Query Language

## Key Features

- 🧩 Optional bearer token authentication for [KQL](https://kirby.tools/docs/headless/usage#kirby-query-language-kql) and custom API endpoints
- 🧱 Resolve fields in blocks: [UUIDs to file and page objects](https://kirby.tools/docs/headless/field-methods) or [any other field](https://kirby.tools/docs/headless/field-methods)
- ⚡️ Cached KQL queries
- 🌐 Multi-language support for KQL queries
- 😵 Built-in CORS handling
- 🍢 Express-esque [API builder](https://kirby.tools/docs/headless/api-builder) with middleware support
- 🗂 Return [JSON from templates](https://kirby.tools/docs/headless/usage#json-templates) instead of HTML

## Setup

> [!TIP]
> [📖 Read the documentation](https://kirby.tools/docs/headless#installation)

Kirby-related dependencies are managed via [Composer](https://getcomposer.org) and located in the `vendor` directory. To install them, run:

```bash
composer install
```

### Environment Variables

Duplicate the [`.env.development.example`](.env.development.example) as `.env` and adjust its values:

```bash
cp .env.development.example .env
```

> [!NOTE]
> Make sure to set the correct requesting origin instead of the wildcard `KIRBY_HEADLESS_ALLOW_ORIGIN=*` for your deployment.

### Deployment

> [!NOTE]
> See [ploi-deploy.sh](./scripts/ploi-deploy.sh) for exemplary deployment instructions.
>
> Some hosting environments require uncommenting `RewriteBase /` in [`.htaccess`](./public/.htaccess) to make site links work.

## License

[MIT](./LICENSE) License © 2022-PRESENT [Johann Schopplich](https://github.com/johannschopplich)
