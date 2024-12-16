<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'credits' => $this->faker->numberBetween(0, 1000),
            'phone' => $this->faker->phoneNumber(),
            'language' => $this->faker->randomElement(['he', 'en']),
            'is_active' => true,
        ];
    }
}

class CreditPackageFactory extends Factory
{
    protected $model = \App\Models\CreditPackage::class;

    public function definition(): array
    {
        $credits = $this->faker->randomElement([50, 100, 200, 500, 1000]);
        $pricePerCredit = $this->faker->randomFloat(2, 0.5, 1.5);

        return [
            'name' => [
                'he' => "חבילת {$credits} קרדיטים",
                'en' => "{$credits} Credits Package"
            ],
            'description' => [
                'he' => $this->faker->realText(100),
                'en' => $this->faker->realText(100)
            ],
            'credits' => $credits,
            'price' => round($credits * $pricePerCredit, 2),
            'is_active' => true,
        ];
    }
}

class TemplateCategoryFactory extends Factory
{
    protected $model = \App\Models\TemplateCategory::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        return [
                                'name' => json_encode([
                        'he' => "קטגורית " . $name,
                        'en' => ucfirst($name)
            ],
            'slug' => Str::slug($name),
            'parent_id' => null,
        ];
    }

    public function withParent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => \App\Models\TemplateCategory::factory(),
            ];
        });
    }
}

class TemplateFactory extends Factory
{
    protected $model = \App\Models\Template::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'name' => [
                'he' => "תבנית " . $name,
                'en' => ucfirst($name)
            ],
            'description' => [
                'he' => $this->faker->realText(200),
                'en' => $this->faker->realText(200)
            ],
            'thumbnail' => 'templates/default.jpg',
            'html_structure' => $this->getDefaultHtmlStructure(),
            'css_styles' => $this->getDefaultCssStyles(),
            'category_id' => \App\Models\TemplateCategory::factory(),
            'settings' => [
                'fonts' => ['Assistant', 'Rubik'],
                'colors' => ['primary' => '#4a5568', 'secondary' => '#718096'],
                'rtl' => true
            ],
            'is_active' => true,
        ];
    }

    protected function getDefaultHtmlStructure(): string
    {
        return <<<'HTML'
<div class="invitation-container">
    <header class="invitation-header">
        <h1>{title}</h1>
        <p class="date">{date}</p>
    </header>
    <main class="invitation-content">
        <div class="details">
            <p>{content}</p>
            <p class="location">{location}</p>
        </div>
    </main>
    <footer class="invitation-footer">
        <div class="rsvp-section">
            {rsvp_form}
        </div>
    </footer>
</div>
HTML;
    }

    protected function getDefaultCssStyles(): string
    {
        return <<<'CSS'
.invitation-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
    text-align: center;
    direction: rtl;
}

.invitation-header {
    margin-bottom: 2rem;
}

.invitation-content {
    margin: 2rem 0;
}

.invitation-footer {
    margin-top: 2rem;
}
CSS;
    }
}

class EffectFactory extends Factory
{
    protected $model = \App\Models\Effect::class;

    public function definition(): array
    {
        $types = ['animation', 'transition', 'particle', 'background', 'interactive'];
        $type = $this->faker->randomElement($types);
        $name = $this->faker->words(2, true);

        return [
            'name' => [
                'he' => "אפקט " . $name,
                'en' => ucfirst($name)
            ],
            'description' => [
                'he' => $this->faker->realText(100),
                'en' => $this->faker->realText(100)
            ],
            'type' => $type,
            'settings' => $this->getDefaultSettings($type),
            'is_active' => true,
        ];
    }

    protected function getDefaultSettings(string $type): array
    {
        return match($type) {
            'animation' => [
                'duration' => '1s',
                'delay' => '0s',
                'timing' => 'ease-in-out',
                'iteration' => '1',
            ],
            'transition' => [
                'type' => 'fade',
                'duration' => '0.5s',
            ],
            'particle' => [
                'count' => 50,
                'color' => '#ffffff',
                'speed' => 2,
            ],
            'background' => [
                'type' => 'gradient',
                'colors' => ['#ffffff', '#f0f0f0'],
                'direction' => '45deg',
            ],
            'interactive' => [
                'trigger' => 'hover',
                'scale' => 1.1,
                'rotation' => '5deg',
            ],
        };
    }
}

class SongFactory extends Factory
{
    protected $model = \App\Models\Song::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'artist' => $this->faker->name(),
            'file_path' => 'songs/demo.mp3',
            'duration' => $this->faker->numberBetween(120, 300),
            'is_active' => true,
            'plays_count' => $this->faker->numberBetween(0, 1000),
        ];
    }
}

class InvitationFactory extends Factory
{
    protected $model = \App\Models\Invitation::class;

    public function definition(): array
    {
        $eventTypes = ['wedding', 'bar_mitzvah', 'bat_mitzvah', 'birthday', 'engagement'];
        $eventDate = $this->faker->dateTimeBetween('+1 month', '+6 months');
        
        return [
            'user_id' => \App\Models\User::factory(),
            'template_id' => \App\Models\Template::factory(),
            'slug' => $this->faker->unique()->slug(),
            'title' => $this->faker->sentence(3),
            'event_date' => $eventDate,
            'event_type' => $this->faker->randomElement($eventTypes),
            'venue_name' => $this->faker->company(),
            'venue_address' => $this->faker->address(),
            'venue_latitude' => $this->faker->latitude(29.5, 33.3),
            'venue_longitude' => $this->faker->longitude(34.2, 35.9),
            'content' => [
                'greeting' => $this->faker->realText(100),
                'details' => $this->faker->realText(200),
                'closing' => $this->faker->realText(50),
            ],
            'settings' => [
                'theme' => 'light',
                'font_size' => 'medium',
                'show_map' => true,
            ],
            'is_active' => true,
            'views_count' => $this->faker->numberBetween(0, 1000),
            'expires_at' => $eventDate->modify('+1 month'),
        ];
    }
}

class RsvpResponseFactory extends Factory
{
    protected $model = \App\Models\RsvpResponse::class;

    public function definition(): array
    {
        return [
            'invitation_id' => \App\Models\Invitation::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->optional(0.8)->safeEmail(),
            'phone' => $this->faker->optional(0.9)->phoneNumber(),
            'guests_count' => $this->faker->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['attending', 'not_attending', 'maybe']),
            'preferences' => [
                'food' => $this->faker->randomElement(['regular', 'vegetarian', 'vegan']),
                'allergies' => $this->faker->optional(0.2)->words(2, true),
            ],
            'notes' => $this->faker->optional(0.3)->realText(100),
        ];
    }
}

class AutomatedMessageFactory extends Factory
{
    protected $model = \App\Models\AutomatedMessage::class;

    public function definition(): array
    {
        $types = ['rsvp_invitation', 'rsvp_reminder', 'event_reminder', 'thank_you'];
        $type = $this->faker->randomElement($types);
        
        return [
            'type' => $type,
            'name' => [
                'he' => $this->getHebrewName($type),
                'en' => $this->getEnglishName($type)
            ],
            'content' => [
                'he' => [
                    'subject' => $this->getHebrewSubject($type),
                    'body' => $this->getHebrewBody($type),
                ],
                'en' => [
                    'subject' => $this->getEnglishSubject($type),
                    'body' => $this->getEnglishBody($type),
                ],
            ],
            'settings' => [
                'send_time' => match($type) {
                    'rsvp_invitation' => 'immediately',
                    'rsvp_reminder' => '7_days_before',
                    'event_reminder' => '1_day_before',
                    'thank_you' => '1_day_after',
                },
            ],
            'is_active' => true,
        ];
    }

    protected function getHebrewName(string $type): string
    {
        return match($type) {
            'rsvp_invitation' => 'הזמנה למענה',
            'rsvp_reminder' => 'תזכורת למענה',
            'event_reminder' => 'תזכורת לאירוע',
            'thank_you' => 'תודה על ההשתתפות',
        };
    }

    protected function getEnglishName(string $type): string
    {
        return match($type) {
            'rsvp_invitation' => 'RSVP Invitation',
            'rsvp_reminder' => 'RSVP Reminder',
            'event_reminder' => 'Event Reminder',
            'thank_you' => 'Thank You',
        };
    }

    protected function getHebrewSubject(string $type): string
    {
        return match($type) {
            'rsvp_invitation' => 'הזמנה ל{title}',
            'rsvp_reminder' => 'תזכורת: אישור הגעה ל{title}',
            'event_reminder' => 'מחר: {title}',
            'thank_you' => 'תודה שהייתם חלק מ{title}',
        };
    }

    protected function getEnglishSubject(string $type): string
    {
        return match($type) {
            'rsvp_invitation' => 'Invitation to {title}',
            'rsvp_reminder' => 'Reminder: RSVP for {title}',
            'event_reminder' => 'Tomorrow: {title}',
            'thank_you' => 'Thank you for being part of {title}',
        };
    }

    protected function getHebrewBody(string $type): string
    {
        return match($type) {
            'rsvp_invitation' => 'שלום {name},\n\nהוזמנת ל{title} שיתקיים בתאריך {date}.\nנשמח לדעת האם תוכל/י להגיע:\n{rsvp_link}',
            'rsvp_reminder' => 'שלום {name},\n\nטרם אישרת הגעה ל{title}.\nנשמח לדעת האם תוכל/י להגיע:\n{rsvp_link}',
            'event_reminder' => 'שלום {name},\n\nרצינו להזכיר שמחר מתקיים {title} ב{location}.\nנתראה!',
            'thank_you' => 'שלום {name},\n\nתודה שהיית חלק מ{title}!\nשמחנו שהגעת.',
        };
    }

    protected function getEnglishBody(string $type): string
    {
        return match($type) {
            'rsvp_invitation' => 'Hello {name},\n\nYou are invited to {title} on {date}.\nPlease let us know if you can attend:\n{rsvp_link}',
            'rsvp_reminder' => 'Hello {name},\n\nWe haven\'t received your RSVP for {title} yet.\nPlease let us know if you can attend:\n{rsvp_link}',
            'event_reminder' => 'Hello {name},\n\nJust a reminder that {title} is tomorrow at {location}.\nSee you there!',
            'thank_you' => 'Hello {name},\n\nThank you for being part of {title}!\nWe were so happy to have you with us.',
        };
    }
}

class OrderFactory extends Factory
{
    protected $model = \App\Models\Order::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'credit_package_id' => \App\Models\CreditPackage::factory(),
            'amount' => function (array $attributes) {
                return \App\Models\CreditPackage::find($attributes['credit_package_id'])->price;
            },
            'credits' => function (array $attributes) {
                return \App\Models\CreditPackage::find($attributes['credit_package_id'])->credits;
            },
            'status' => $this->faker->randomElement(['completed', 'pending', 'failed']),
            'payment_id' => $this->faker->uuid(),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'invoice_number' => function () {
                $prefix = date('Ym');
                $number = str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT);
                return $prefix . $number;
            },
        ];
    }
}

class MessageLogFactory extends Factory
{
    protected $model = \App\Models\MessageLog::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['email', 'sms', 'whatsapp']);
        $status = $this->faker->randomElement(['pending', 'sent', 'failed']);
        
        return [
            'invitation_id' => \App\Models\Invitation::factory(),
            'message_id' => \App\Models\AutomatedMessage::factory(),
            'recipient' => match($type) {
                'email' => $this->faker->email(),
                'sms', 'whatsapp' => $this->faker->phoneNumber(),
            },
            'type' => $type,
            'status' => $status,
            'sent_at' => $status === 'sent' ? $this->faker->dateTimeThisMonth() : null,
        ];
    }
}