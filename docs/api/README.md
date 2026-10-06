# API documentation — Payment Gateway for Redsys & WooCommerce Lite

Start at [`INDEX.md`](INDEX.md): one row per public surface (hook, endpoint, class, function, option). Grep it first; open the full document only on a hit.

The plugin predates Keel, so the reference is backfilled progressively and lives under `docs/reference/`:

- [`../reference/hooks-and-extension-points.md`](../reference/hooks-and-extension-points.md) — every action and filter the plugin fires.
- [`../reference/endpoints.md`](../reference/endpoints.md) — the `wc-api` notification callbacks and the other reachable handlers.
- [`../reference/classes.md`](../reference/classes.md) and [`../reference/functions.md`](../reference/functions.md) — partial by design; each states what has not been read yet.

Usage and configuration are in `docs/usage/`. A surface that is added, changed or removed is documented in the same slice, and its `INDEX.md` row with it.
