# SQL Designer frontend

The interactive SQL Designer application is built with Vue 3, Vuex, Vue Router, and Vue Flow. Laravel serves the application shell and API from `../backend`.

## Development

The project runs entirely in Docker. From the repository root, start the development stack with:

```sh
make up
```

The `node` container installs frontend dependencies and starts Vite. Do not run Vite or `npm run build` directly on the host.

## Production build

Build frontend assets through the repository-level Docker workflow:

```sh
make build-frontend
```

Generated assets are copied into `backend/public` for Laravel and Nginx to serve.

See the root [README](../README.md) for the project overview and contribution information.
