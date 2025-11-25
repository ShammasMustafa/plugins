# Custom User Orchestrator

## Setup
1. Place plugin folder in wp-content/plugins/
2. Run `composer install` (to generate autoload)
3. Build block JS with `npm install` and `npx wp-scripts build` (if you change block source)
4. Activate plugin in WP admin.

## Features
- Caches users for 1 hour at transient `cuo_users_cache`
- REST endpoint: /wp-json/custom-plugin/v1/data
- Admin page: Dashboard -> User Table (Refresh Data button)
- WP-CLI: wp custom-plugin refresh-data
- Gutenberg block: 'User Table' (Uses REST endpoint)

## Design notes
- Transients used to reduce external calls; fallback to old cache when remote fetch fails.
- PSR-4 autoloading with Composer.
- Nonces and capability checks on admin actions.
