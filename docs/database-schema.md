# Digital Invitation System - Database Schema Specification

## Core Tables

### users
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| name | varchar(255) | User's full name | NOT NULL |
| email | varchar(255) | User's email address | UNIQUE, NOT NULL |
| password | varchar(255) | Hashed password | NOT NULL |
| credits | int | Available credits | DEFAULT 0 |
| phone | varchar(20) | Contact number | NULL |
| language | varchar(5) | Preferred language | DEFAULT 'he' |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |
| deleted_at | timestamp | Soft delete timestamp | NULL |

### orders
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| user_id | bigint unsigned | Reference to users | FK |
| package_id | bigint unsigned | Reference to credit packages | FK |
| amount | decimal(10,2) | Order amount | NOT NULL |
| credits | int | Credits purchased | NOT NULL |
| status | varchar(20) | Order status | DEFAULT 'pending' |
| payment_id | varchar(255) | External payment reference | NULL |
| payment_method | varchar(50) | Payment method used | NULL |
| invoice_number | varchar(50) | Generated invoice number | UNIQUE |
| created_at | timestamp | Order creation time | |
| updated_at | timestamp | Order update time | |

### credit_packages
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| name | json | Package name (multilingual) | NOT NULL |
| description | json | Package description (multilingual) | NOT NULL |
| credits | int | Number of credits | NOT NULL |
| price | decimal(10,2) | Package price | NOT NULL |
| is_active | boolean | Package availability | DEFAULT true |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |

### invitations
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| user_id | bigint unsigned | Reference to users | FK |
| template_id | bigint unsigned | Reference to templates | FK |
| slug | varchar(100) | Unique URL identifier | UNIQUE |
| title | varchar(255) | Invitation title | NOT NULL |
| event_date | datetime | Event date and time | NOT NULL |
| event_type | varchar(50) | Type of event | NOT NULL |
| venue_name | varchar(255) | Event venue | NOT NULL |
| venue_address | text | Venue address | NOT NULL |
| venue_coordinates | point | GPS coordinates | NULL |
| content | json | Invitation content | NOT NULL |
| settings | json | Invitation settings | NOT NULL |
| is_active | boolean | Invitation status | DEFAULT true |
| views_count | int | View counter | DEFAULT 0 |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |
| expires_at | timestamp | Access expiration | NULL |

### invitation_songs
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| invitation_id | bigint unsigned | Reference to invitations | FK |
| song_id | bigint unsigned | Reference to songs | FK |
| created_at | timestamp | Record creation time | |

### invitation_effects
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| invitation_id | bigint unsigned | Reference to invitations | FK |
| effect_id | bigint unsigned | Reference to effects | FK |
| settings | json | Effect configuration | NULL |
| created_at | timestamp | Record creation time | |

### templates
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| name | json | Template name (multilingual) | NOT NULL |
| description | json | Template description (multilingual) | NULL |
| thumbnail | varchar(255) | Template preview image | NOT NULL |
| html_structure | text | Template HTML structure | NOT NULL |
| css_styles | text | Template CSS styles | NOT NULL |
| category_id | bigint unsigned | Reference to categories | FK |
| settings | json | Template settings | NOT NULL |
| is_active | boolean | Template availability | DEFAULT true |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |

### template_categories
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| name | json | Category name (multilingual) | NOT NULL |
| slug | varchar(100) | URL-friendly identifier | UNIQUE |
| parent_id | bigint unsigned | Parent category reference | FK, NULL |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |

### songs
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| title | varchar(255) | Song title | NOT NULL |
| artist | varchar(255) | Artist name | NOT NULL |
| file_path | varchar(255) | Path to audio file | NOT NULL |
| duration | int | Duration in seconds | NOT NULL |
| is_active | boolean | Song availability | DEFAULT true |
| plays_count | int | Play counter | DEFAULT 0 |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |

### effects
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| name | json | Effect name (multilingual) | NOT NULL |
| description | json | Effect description (multilingual) | NULL |
| type | varchar(50) | Effect type | NOT NULL |
| settings | json | Default settings | NOT NULL |
| is_active | boolean | Effect availability | DEFAULT true |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |

### rsvp_responses
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| invitation_id | bigint unsigned | Reference to invitations | FK |
| name | varchar(255) | Guest name | NOT NULL |
| email | varchar(255) | Guest email | NULL |
| phone | varchar(20) | Guest phone | NULL |
| guests_count | int | Number of guests | DEFAULT 1 |
| status | varchar(20) | Response status | NOT NULL |
| preferences | json | Guest preferences | NULL |
| notes | text | Additional notes | NULL |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |

### automated_messages
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| type | varchar(50) | Message type | NOT NULL |
| name | json | Template name (multilingual) | NOT NULL |
| content | json | Message content (multilingual) | NOT NULL |
| settings | json | Message settings | NOT NULL |
| is_active | boolean | Template availability | DEFAULT true |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |

### message_logs
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| id | bigint unsigned | Primary identifier | PK, AUTO_INCREMENT |
| invitation_id | bigint unsigned | Reference to invitations | FK |
| message_id | bigint unsigned | Reference to automated_messages | FK |
| recipient | varchar(255) | Message recipient | NOT NULL |
| type | varchar(20) | Delivery type (email/sms/whatsapp) | NOT NULL |
| status | varchar(20) | Delivery status | NOT NULL |
| sent_at | timestamp | Send timestamp | NULL |
| created_at | timestamp | Record creation time | |

### settings
| Column | Type | Purpose | Constraints |
|--------|------|---------|-------------|
| key | varchar(255) | Setting identifier | PK |
| value | json | Setting value | NOT NULL |
| created_at | timestamp | Record creation time | |
| updated_at | timestamp | Record update time | |

## Indexing Strategy

### Primary Search Fields
- invitations: slug, event_date, title
- templates: name, category_id
- rsvp_responses: invitation_id, status
- orders: user_id, status, created_at

### Compound Indexes
1. invitations: (user_id, event_date)
2. rsvp_responses: (invitation_id, status)
3. message_logs: (invitation_id, type, status)

## Additional Considerations

### Data Integrity
1. All foreign keys should be indexed
2. Use soft deletes where appropriate
3. JSON columns for multilingual content
4. Timestamps for auditing

### Extensibility
1. JSON columns for flexible storage
2. Generic settings table
3. Modular message system
4. Scalable category structure