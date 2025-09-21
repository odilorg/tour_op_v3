<?php

namespace Database\Seeders;

use App\Enums\BookingAssignmentRole;
use App\Enums\BookingAssignmentStatus;
use App\Enums\BookingStatus;
use App\Enums\PaymentDirection;
use App\Enums\SupplierRateServiceType;
use App\Enums\SupplierRateUnit;
use App\Enums\SupplierType;
use App\Enums\VehicleType;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Currency;
use App\Models\Supplier;
use App\Models\SupplierRate;
use App\Models\Tour;
use App\Models\TourDay;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seedCurrencies();
        $this->seedPermissionsAndRoles();
        $users = $this->seedUsers();
        [$suppliers, $rates] = $this->seedSuppliers();
        $tour = $this->seedTour();
        $this->seedBooking($tour, $suppliers, $rates, $users);
    }

    protected function seedCurrencies(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'UZS', 'name' => 'Uzbekistani Som', 'symbol' => 'лв'],
            ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => '₽'],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['code' => $currency['code']], $currency);
        }
    }

    protected function seedPermissionsAndRoles(): void
    {
        $permissions = [
            'dashboard.view',
            'booking.view',
            'booking.create',
            'booking.update',
            'booking.delete',
            'supplier.view',
            'supplier.create',
            'supplier.update',
            'supplier.delete',
            'tour.view',
            'tour.create',
            'tour.update',
            'tour.delete',
            'currency.view',
            'currency.manage',
            'payment.manage',
            'report.export',
            'filament_access',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'admin' => $permissions,
            'manager' => [
                'filament_access',
                'dashboard.view',
                'booking.view', 'booking.create', 'booking.update', 'booking.delete',
                'supplier.view', 'supplier.create', 'supplier.update',
                'tour.view',
                'payment.manage',
                'report.export',
            ],
            'operator' => [
                'filament_access',
                'dashboard.view',
                'booking.view', 'booking.create', 'booking.update',
                'supplier.view',
                'tour.view', 'tour.create', 'tour.update',
            ],
            'accountant' => [
                'filament_access',
                'dashboard.view',
                'booking.view',
                'payment.manage',
                'report.export',
            ],
            'viewer' => [
                'filament_access',
                'dashboard.view',
                'booking.view',
                'supplier.view',
                'tour.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }

    protected function seedUsers(): array
    {
        $users = [
            'admin' => User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]),
            'manager' => User::factory()->create([
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
            ]),
            'operator' => User::factory()->create([
                'name' => 'Operator User',
                'email' => 'operator@example.com',
                'password' => Hash::make('password'),
            ]),
            'accountant' => User::factory()->create([
                'name' => 'Accountant User',
                'email' => 'accountant@example.com',
                'password' => Hash::make('password'),
            ]),
            'viewer' => User::factory()->create([
                'name' => 'Viewer User',
                'email' => 'viewer@example.com',
                'password' => Hash::make('password'),
            ]),
        ];

        foreach ($users as $role => $user) {
            $user->assignRole($role);
        }

        return $users;
    }

    protected function seedSuppliers(): array
    {
        $suppliers = [
            Supplier::create([
                'type' => SupplierType::GUIDE,
                'name' => 'City Explorer Guides',
                'contact_name' => 'John Guide',
                'phone' => '+1234567890',
                'email' => 'guide@example.com',
            ]),
            Supplier::create([
                'type' => SupplierType::TRANSPORT_COMPANY,
                'name' => 'Rapid Wheels Transport',
                'contact_name' => 'Sasha Driver',
                'phone' => '+998123456',
                'email' => 'transport@example.com',
            ]),
            Supplier::create([
                'type' => SupplierType::HOTEL,
                'name' => 'Grand Plaza Hotel',
                'contact_name' => 'Reception Desk',
                'phone' => '+4412345678',
                'email' => 'hotel@example.com',
            ]),
            Supplier::create([
                'type' => SupplierType::RESTAURANT,
                'name' => 'Silk Road Dining',
                'contact_name' => 'Chef Anna',
                'phone' => '+4411122233',
                'email' => 'restaurant@example.com',
            ]),
        ];

        Vehicle::create([
            'supplier_id' => $suppliers[1]->id,
            'type' => VehicleType::MINIVAN,
            'plate' => 'ABC-001',
            'seats' => 8,
        ]);

        $rates = [
            SupplierRate::create([
                'supplier_id' => $suppliers[0]->id,
                'service_type' => SupplierRateServiceType::GUIDE_FULL_DAY,
                'unit' => SupplierRateUnit::PER_DAY,
                'amount_minor' => 15000,
                'currency_code' => 'USD',
                'description' => 'Full day guide service',
            ]),
            SupplierRate::create([
                'supplier_id' => $suppliers[1]->id,
                'service_type' => SupplierRateServiceType::TRANSPORT_FULL_DAY,
                'unit' => SupplierRateUnit::PER_DAY,
                'vehicle_type' => VehicleType::MINIVAN,
                'amount_minor' => 20000,
                'currency_code' => 'USD',
                'description' => 'Minivan full day',
            ]),
            SupplierRate::create([
                'supplier_id' => $suppliers[2]->id,
                'service_type' => SupplierRateServiceType::HOTEL_ROOM_NIGHT_STANDARD,
                'unit' => SupplierRateUnit::PER_NIGHT,
                'amount_minor' => 12000,
                'currency_code' => 'USD',
                'description' => 'Standard room',
            ]),
            SupplierRate::create([
                'supplier_id' => $suppliers[3]->id,
                'service_type' => SupplierRateServiceType::RESTAURANT_SET_MENU_DINNER,
                'unit' => SupplierRateUnit::PER_MEAL,
                'amount_minor' => 5000,
                'currency_code' => 'USD',
                'description' => 'Dinner menu',
            ]),
        ];

        return [$suppliers, $rates];
    }

    protected function seedTour(): Tour
    {
        $tour = Tour::create([
            'title' => 'Silk Road Discovery',
            'slug' => 'silk-road-discovery',
            'description' => 'Three day classic highlights tour.',
            'default_currency_code' => 'USD',
        ]);

        $days = [
            ['day_index' => 1, 'title' => 'Arrival & City Tour', 'description' => 'Airport pickup and half-day city exploration.'],
            ['day_index' => 2, 'title' => 'Cultural Immersion', 'description' => 'Museum visits and local experiences.'],
            ['day_index' => 3, 'title' => 'Departure', 'description' => 'Free morning and airport transfer.'],
        ];

        foreach ($days as $day) {
            TourDay::create(array_merge($day, ['tour_id' => $tour->id]));
        }

        return $tour;
    }

    protected function seedBooking(Tour $tour, array $suppliers, array $rates, array $users): void
    {
        $booking = Booking::create([
            'tour_id' => $tour->id,
            'operator_id' => $users['operator']->id,
            'reference_code' => 'BK-' . Str::upper(Str::random(6)),
            'customer_name' => 'Nomad Travel Group',
            'customer_phone' => '+12025550123',
            'customer_email' => 'client@example.com',
            'start_date' => now()->addWeek()->toDateString(),
            'end_date' => now()->addWeek()->addDays(2)->toDateString(),
            'party_size' => 4,
            'status' => BookingStatus::REQUESTED->value,
            'currency_code' => 'USD',
            'markup_percent' => 15,
        ]);

        $booking->load('tour.days');
        $booking->generateDaysFromTour();

        $days = $booking->days()->get();

        $assignments = [
            [
                'booking_day_id' => $days[0]->id,
                'supplier_id' => $suppliers[0]->id,
                'role' => BookingAssignmentRole::GUIDE->value,
                'service_type' => SupplierRateServiceType::GUIDE_FULL_DAY->value,
                'unit' => SupplierRateUnit::PER_DAY->value,
                'qty' => 1,
                'rate_minor' => $rates[0]->amount_minor,
                'cost_minor' => $rates[0]->amount_minor,
                'currency_code' => 'USD',
                'status' => BookingAssignmentStatus::COMPANY_CONFIRMED->value,
            ],
            [
                'booking_day_id' => $days[0]->id,
                'supplier_id' => $suppliers[1]->id,
                'role' => BookingAssignmentRole::TRANSPORT->value,
                'service_type' => SupplierRateServiceType::TRANSPORT_FULL_DAY->value,
                'unit' => SupplierRateUnit::PER_DAY->value,
                'vehicle_type' => VehicleType::MINIVAN->value,
                'qty' => 1,
                'rate_minor' => $rates[1]->amount_minor,
                'cost_minor' => $rates[1]->amount_minor,
                'currency_code' => 'USD',
                'status' => BookingAssignmentStatus::SUPPLIER_CONFIRMED->value,
            ],
            [
                'booking_day_id' => $days[1]->id,
                'supplier_id' => $suppliers[2]->id,
                'role' => BookingAssignmentRole::HOTEL->value,
                'service_type' => SupplierRateServiceType::HOTEL_ROOM_NIGHT_STANDARD->value,
                'unit' => SupplierRateUnit::PER_NIGHT->value,
                'qty' => 2,
                'rate_minor' => $rates[2]->amount_minor,
                'cost_minor' => $rates[2]->amount_minor,
                'currency_code' => 'USD',
                'status' => BookingAssignmentStatus::PENDING->value,
            ],
            [
                'booking_day_id' => $days[2]->id,
                'supplier_id' => $suppliers[3]->id,
                'role' => BookingAssignmentRole::RESTAURANT->value,
                'service_type' => SupplierRateServiceType::RESTAURANT_SET_MENU_DINNER->value,
                'unit' => SupplierRateUnit::PER_MEAL->value,
                'qty' => 4,
                'rate_minor' => $rates[3]->amount_minor,
                'cost_minor' => $rates[3]->amount_minor,
                'currency_code' => 'USD',
                'status' => BookingAssignmentStatus::PENDING->value,
            ],
        ];

        foreach ($assignments as $assignment) {
            $booking->assignments()->create($assignment);
        }

        BookingPayment::create([
            'booking_id' => $booking->id,
            'direction' => PaymentDirection::INCOMING->value,
            'method' => 'Bank Transfer',
            'amount_minor' => 60000,
            'currency_code' => 'USD',
            'paid_at' => now(),
            'reference' => 'INV-001',
        ]);

        BookingPayment::create([
            'booking_id' => $booking->id,
            'direction' => PaymentDirection::OUTGOING->value,
            'method' => 'Wire',
            'amount_minor' => 30000,
            'currency_code' => 'USD',
            'paid_at' => now(),
            'reference' => 'SUP-001',
        ]);

        $booking->refresh();
        $booking->recalculateProgress();
        $booking->statusLogs()->create([
            'old_status' => null,
            'new_status' => $booking->status->value,
            'changed_at' => now(),
            'note' => 'Booking seeded',
        ]);
    }
}
