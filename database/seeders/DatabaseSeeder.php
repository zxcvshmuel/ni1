<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles and their permissions
        $this->createRolesAndPermissions();

        // Create super admin user (developer)
        $superAdmin = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('password'),
            'credits' => 9999,
            'language' => 'he',
        ]);
        $superAdmin->assignRole('super_admin');

        // Create admin user (system manager)
        $admin = \App\Models\User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'credits' => 9999,
            'language' => 'he',
        ]);
        $admin->assignRole('admin');

        // Create test user (customer)
        $user = \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'credits' => 100,
            'language' => 'he',
        ]);
        $user->assignRole('customer');

        // Create additional users
        \App\Models\User::factory(20)->create()->each(function ($user) {
            $user->assignRole('customer');
        });

        // Create credit packages
        \App\Models\CreditPackage::factory()->create([
            'name' => ['he' => 'חבילת התחלה', 'en' => 'Starter Package'],
            'credits' => 50,
            'price' => 49.99,
        ]);
        \App\Models\CreditPackage::factory()->create([
            'name' => ['he' => 'חבילה בסיסית', 'en' => 'Basic Package'],
            'credits' => 100,
            'price' => 89.99,
        ]);
        \App\Models\CreditPackage::factory()->create([
            'name' => ['he' => 'חבילה מקצועית', 'en' => 'Pro Package'],
            'credits' => 500,
            'price' => 399.99,
        ]);

        // Create template categories
        $mainCategories = [
            'weddings' => ['he' => 'חתונות', 'en' => 'Weddings'],
            'bar_mitzvahs' => ['he' => 'בר מצווה', 'en' => 'Bar Mitzvahs'],
            'bat_mitzvahs' => ['he' => 'בת מצווה', 'en' => 'Bat Mitzvahs'],
            'birthdays' => ['he' => 'ימי הולדת', 'en' => 'Birthdays'],
            'engagements' => ['he' => 'אירוסין', 'en' => 'Engagements'],
        ];

        foreach ($mainCategories as $slug => $names) {
            $category = \App\Models\TemplateCategory::create([
                'name' => $names,
                'slug' => $slug,
            ]);

            // Create subcategories
            \App\Models\TemplateCategory::factory(2)->create([
                'parent_id' => $category->id,
            ]);

            // Create templates for each category
            \App\Models\Template::factory(5)->create([
                'category_id' => $category->id,
            ]);
        }

        // Create effects
        \App\Models\Effect::factory(20)->create();

        // Create songs
        \App\Models\Song::factory(30)->create();

        // Create message templates
        $messageTypes = ['rsvp_invitation', 'rsvp_reminder', 'event_reminder', 'thank_you'];
        foreach ($messageTypes as $type) {
            \App\Models\AutomatedMessage::factory()->create(['type' => $type]);
        }

        // Create test invitations with responses
        $testInvitations = \App\Models\Invitation::factory(5)->create([
            'user_id' => $user->id,
        ]);

        foreach ($testInvitations as $invitation) {
            // Attach random effects and songs
            $invitation->effects()->attach(
                \App\Models\Effect::inRandomOrder()->limit(2)->pluck('id')
            );
            $invitation->songs()->attach(
                \App\Models\Song::inRandomOrder()->limit(1)->pluck('id')
            );

            // Create RSVP responses
            \App\Models\RsvpResponse::factory(10)->create([
                'invitation_id' => $invitation->id,
            ]);

            // Create message logs
            foreach ($messageTypes as $type) {
                \App\Models\MessageLog::factory()->create([
                    'invitation_id' => $invitation->id,
                    'message_id' => \App\Models\AutomatedMessage::where('type', $type)->first()->id,
                ]);
            }
        }

        // Create orders for test user
        \App\Models\Order::factory(3)->create([
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        // Create settings
        $this->createDefaultSettings();
    }

    protected function createRolesAndPermissions(): void
    {
        // Create roles
        $roles = [
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'customer' => 'Customer',
        ];

        foreach ($roles as $role => $label) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }

        // Define entity permissions
        $entities = [
            'user',
            'role',
            'credit::package',
            'template',
            'template::category',
            'effect',
            'song',
            'invitation',
            'rsvp::response',
            'automated::message',
            'message::log',
            'order',
            'setting',
            'activity', // For activity logs
            'exception', // For error logs
        ];

        // Create permissions for each entity
        foreach ($entities as $entity) {
            $this->createEntityPermissions($entity);
        }

        // Additional permissions for system features
        $additionalPermissions = [
            'view_admin_panel',
            'access_filament',
            'view_pulse', // Laravel Pulse monitoring
            'view_horizon', // Laravel Horizon
            'view_telescope', // Laravel Telescope
            'impersonate_users',
            'view_exceptions',
        ];

        foreach ($additionalPermissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $this->assignRolePermissions();
    }

    protected function createEntityPermissions(string $entity): void
    {
        $actions = [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
        ];

        foreach ($actions as $action) {
            Permission::create([
                'name' => "{$action}_{$entity}",
                'guard_name' => 'web',
            ]);
        }
    }

    protected function assignRolePermissions(): void
    {
        // Super Admin gets everything
        $superAdmin = Role::where('name', 'super_admin')->first();
        $superAdmin->givePermissionTo(Permission::all());

        // Admin gets most permissions except system monitoring
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo(Permission::whereNotIn('name', [
            'view_pulse',
            'view_horizon',
            'view_telescope',
            'view_exceptions',
            'force_delete_any_user',
            'force_delete_role',
            'force_delete_any_role',
        ])->get());

        // Customer gets limited permissions
        $customer = Role::where('name', 'customer')->first();
        $customer->givePermissionTo([
            'view_any_invitation',
            'view_invitation',
            'create_invitation',
            'update_invitation',
            'delete_invitation',
            'view_any_rsvp::response',
            'view_rsvp::response',
            'create_rsvp::response',
            'update_rsvp::response',
            'delete_rsvp::response',
            'view_any_order',
            'view_order',
            'create_order',
        ]);
    }

    protected function createDefaultSettings(): void
    {
        $settings = [
            'site' => [
                'maintenance_mode' => false,
                'default_language' => 'he',
            ],
            'invitations' => [
                'max_songs_per_invitation' => 3,
                'max_effects_per_invitation' => 5,
                'credits_per_invitation' => 10,
            ],
            'rsvp' => [
                'max_guests_per_response' => 10,
                'allow_maybe_response' => true,
                'collect_dietary_preferences' => true,
            ],
            'notifications' => [
                'email_enabled' => true,
                'sms_enabled' => false,
                'whatsapp_enabled' => false,
            ],
        ];

        foreach ($settings as $group => $items) {
            foreach ($items as $name => $value) {
                \App\Models\Setting::create([
                    'group' => $group,
                    'name' => $name,
                    'payload' => json_encode(['value' => $value]),
                ]);
            }
        }
    }
}