<div align="center">

# Kirby Headless Starter

A minimal Kirby setup for headless use – JSON from KQL queries or Kirby's template system, behind bearer-token authentication.

[Live Playground](https://kirby-headless-starter.byjohann.dev) •
[Documentation](https://kirby.tools/docs/headless) •
[Nuxt Kirby](https://nuxt-kirby.byjohann.dev)

</div>

> [!TIP]
> Send a request with an `Authorization: Bearer test` header to the [live playground](https://kirby-headless-starter.byjohann.dev) for an example response.

Under the hood, this starter uses the [Kirby Headless plugin](https://kirby.tools/docs/headless) to provide a headless API. It pairs well with [Nuxt Kirby](https://nuxt-kirby.byjohann.dev) on the frontend.

## Key Features

- 🧩 Optional bearer token authentication for [KQL](https://kirby.tools/docs/headless/usage/kql) and custom API endpoints
- 🧱 Resolve fields in blocks: [UUIDs to file and page objects](https://kirby.tools/docs/headless/usage/field-methods) or [any other field](https://kirby.tools/docs/headless/usage/field-methods)
- ⚡️ Cached KQL queries
- 🌐 Multi-language support for KQL queries
- 🍢 Express-esque [API builder](https://kirby.tools/docs/headless/advanced/api-builder) with middleware support
- 🗂 Return [JSON from templates](https://kirby.tools/docs/headless/usage/json-templates) instead of HTML

## Example Projects

- [`cacao-kit-frontend`](https://github.com/johannschopplich/cacao-kit-frontend): 🍫 Best practice Nuxt and KQL starter for your headless Kirby CMS
- [`kirby-nuxt-starterkit`](https://github.com/johannschopplich/kirby-nuxt-starterkit): 💚 Kirby's sample site – ported to Nuxt and Kirby Query Language

## Development

> [!TIP]
> [📖 Read the documentation](https://kirby.tools/docs/headless/getting-started/installation)

1. Create your `.env` from the example:

   ```bash
   cp .env.development.example .env
   ```

2. Install dependencies:

   ```bash
   composer install
   ```

3. Run the PHP server – or use a dev server of your choice (e.g. Laravel Valet):

   ```bash
   composer start
   ```

Linting and formatting run through pnpm: `pnpm install`, then `pnpm run lint` or `pnpm run format`.

> [!NOTE]
> Set the correct requesting origin instead of the wildcard in `KIRBY_CORS_ALLOW_ORIGIN` for your deployment.

Kirby is not free software – you can try it as long as you need to, but [buy a license](https://getkirby.com/buy) once you take a project to production.

## Deployment

Deployment runs through [`scripts/ploi-deploy.sh`](./scripts/ploi-deploy.sh) on [ploi.io](https://ploi.io) – adapt it to your hosting environment as needed.

> [!NOTE]
> Some hosting environments require uncommenting `RewriteBase /` in [`.htaccess`](./public/.htaccess) to make site links work.

## License

[MIT](./LICENSE) License © 2022-PRESENT [Johann Schopplich](https://github.com/johannschopplich)
