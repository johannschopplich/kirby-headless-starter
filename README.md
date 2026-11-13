<div align="center">

# Kirby Headless Starter

A minimal Kirby setup for headless use – JSON from KQL queries or Kirby's template system, behind bearer-token authentication.

[Live Playground](https://kirby-headless-starter.byjohann.dev) •
[Documentation](https://kirby.tools/docs/headless) •
[Nuxt Kirby](https://nuxt-kirby.byjohann.dev)

</div>

> [!TIP]
> Send a request with an `Authorization: Bearer test` header to the [live playground](https://kirby-headless-starter.byjohann.dev) for an example response.

## Key Features

- 🧩 Optional bearer token authentication for [KQL](https://kirby.tools/docs/headless/usage/kql) and custom API endpoints
- 🧱 Resolve fields in blocks: [UUIDs to file and page objects, or any other field](https://kirby.tools/docs/headless/usage/field-methods)
- ⚡️ Cached KQL queries
- 🌐 Multi-language KQL queries
- 🍢 Express-esque [API builder](https://kirby.tools/docs/headless/usage/api-builder) with middleware support
- 🗂 Return [JSON from templates](https://kirby.tools/docs/headless/usage/json-templates) instead of HTML

## Architecture

This starter is built on the [Kirby Headless plugin](https://kirby.tools/docs/headless) and pairs with [Nuxt Kirby](https://nuxt-kirby.byjohann.dev) on the frontend.

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

Kirby is not free software – you can try it as long as you need to, but [buy a license](https://getkirby.com/buy) once you take a project to production.

## Deployment

Pushes to `main` deploy through [`.github/workflows/deploy.yml`](./.github/workflows/deploy.yml). The workflow opens an SSH connection to `DEPLOY_HOST` and sends no command, so the server decides what a deploy does. A copy without the `DEPLOY_HOST` repository variable skips the job. To deploy from your copy:

1. On the server, add a deploy key to `authorized_keys` with a forced command (`command="…"`) that updates the site, for example by pulling the repository and running `composer install`
2. Set the repository secret `DEPLOY_SSH_KEY` to that key's private half
3. Set the repository variable `DEPLOY_HOST` to the server's host name
4. Set the repository variable `DEPLOY_KNOWN_HOSTS` to the output of `ssh-keyscan <host>`
5. Set the repository variable `DEPLOY_USER` to the SSH user on the server

For your deployment, set `KIRBY_CORS_ALLOW_ORIGIN` to your frontend's origin instead of the wildcard.

> [!NOTE]
> Some hosting environments require uncommenting `RewriteBase /` in [`.htaccess`](./public/.htaccess) to make site links work.

## License

[MIT](./LICENSE) License © 2022-PRESENT [Johann Schopplich](https://github.com/johannschopplich)
