# Tour Operator CRM

A production-ready Laravel 11 + Filament v4 operations console for tour companies. Operators configure tours, manage bookings, assign suppliers, and track financial performance.

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ (for asset building)
- SQLite / MySQL / PostgreSQL (default uses SQLite)

## Installation

```bash
cp .env.example .env
composer install
php artisan key:generate
npm install
php artisan migrate --seed
```

Filament Shield roles and permissions are seeded automatically. Default demo accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| Manager | `manager@example.com` | `password` |
| Operator | `operator@example.com` | `password` |
| Accountant | `accountant@example.com` | `password` |
| Viewer | `viewer@example.com` | `password` |

Access the Filament panel at `http://localhost/admin`.

## Development

- Run the job worker locally if you plan to queue recompute jobs: `php artisan queue:work`
- Assets can be compiled with `npm run dev`

## Key Features

- Tours composed of ordered days with optional media gallery
- Bookings generated from tours with automatic day scheduling
- Supplier catalog with rates, vehicles, and availability protections
- Assignment management with pricing snapshots, quick rate picker, and progress tracking
- Financial overview widgets (revenue, costs, margin) and at-risk booking dashboards
- Activity log for status transitions, notifications on supplier confirmations
- Role-based access via Filament Shield & spatie/laravel-permission

## Utilities

- `php artisan bookings:regenerate-days {booking_id}` re-syncs booking days from the source tour
- `App\Jobs\RecomputeBookingFinancialsJob` recalculates totals and progress for a booking

## Testing

Pest is configured by default. Run the full test suite:

```bash
php artisan test
```

## Media & Storage

The application stores uploaded media on the `public` disk by default (`MEDIA_DISK` env variable). Ensure `php artisan storage:link` is executed in non-local environments.

## Activity Logging

Booking status transitions are recorded in `activity_log` and mirrored in the `booking_status_logs` table for quick reference within the Filament panel.

## License

MIT
