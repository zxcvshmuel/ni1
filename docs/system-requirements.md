# Digital Event Invitations System - Requirements Document

## Executive Summary
A comprehensive SaaS platform for creating and managing digital event invitations, including an integrated RSVP system and extensive management capabilities. The system is primarily designed for Hebrew-speaking users but supports multiple languages.

## System Overview

### Core Functionality
- Digital invitation creation and management
- Built-in RSVP system
- Credits-based payment system
- Multi-language support
- Mobile-first design approach
- Automated communication system

### Target Users
The system primarily serves Hebrew-speaking users who need to create and manage digital invitations for various events.

## User Roles and Permissions

### System Developer
- Full system access
- Advanced technical capabilities
- System maintenance and updates

### System Administrator
- Invitation management
- Sales and customer management
- Content management (templates, songs, effects)
- Pricing package and credits management
- User management
- Template creation and management
- Customer support capabilities

### Customers
- Invitation creation and management
- RSVP management
- Statistics viewing
- Message template customization
- Credit usage tracking

### Guests
- Invitation viewing
- RSVP submission
- Preference selection (menu, etc.)

## Invitation Creation Process

### Creation Steps
1. Template selection
2. Effect selection
3. Song selection
4. Information input
5. Preview and confirmation
6. Registration/Login
7. Payment
8. Thank you page and information

### Process Features
- Session information storage with reset option
- Step-by-step editing capability
- Mobile-optimized display
- Preview functionality for mobile devices

## Content Management

### Templates and Invitations
- Basic templates: background + text + effects
- Background and effect categories
- Overlapping category support
- Local file storage
- Font management system

### Language Support
- System interface: Hebrew and English
- Invitation content: Flexible, without translation system
- Right-to-left (RTL) support for Hebrew

## Payment and Pricing System

### Credit Management
- Credit-based system instead of per-invitation payment
- Various pricing packages
- Admin capability to grant/remove credits
- No credit expiration
- No credit transfer between users
- Admin panel for credit management

## RSVP System

### Features
- Multiple guest confirmation
- Unlimited confirmations per invitation
- Guest identification details
- Customizable preference options (menu, etc.)
- Export capabilities with multiple filters
- Automatic blocking after event date
- Integration with calendar systems

### Additional Features
- Navigation/map integration
- Direct contact button
- Calendar integration
- Mobile-optimized display

## Notification and Communication System

### Admin Panel Features
- Template creation for various message types:
  * RSVP invitation
  * RSVP reminder
  * Event day reminder
  * Post-event thank you
- Timing configuration for automated messages
- Message performance tracking

### Customer Panel Features
- Enable/disable each message type
- Template content customization
- Message status tracking
- No direct message transfer capability

## Analytics and Tracking

### Key Metrics
- Website visits
- Abandonment rates
- Order/payment tracking
- Song play tracking
- Dynamic META tags
- User action logging

## Data Management and Export

### Export Capabilities
- CSV/Excel export
- Advanced filtering and search
- Multiple filter options:
  * RSVP status
  * Confirmation date
  * Guest preferences
  * Contact details

### Data Structure Principles
1. Modular Structure:
   - Clear separation between data types
   - Expandable entity relationships
   - Well-defined connections

2. Search and Filter System:
   - Optimized search indexes
   - Full-text search capability
   - Multi-parameter filtering
   - Saved search functionality

3. Future Expansion Support:
   - Flexible field addition
   - New content type support
   - Relationship expansion capability
   - Additional language support

4. Hierarchical Data Organization:
   ```
   Event
   ├── Basic Event Details
   ├── Invitation
   │   ├── Template
   │   ├── Texts
   │   └── Design Settings
   ├── RSVP
   │   ├── Guest List
   │   └── Preferences and Notes
   └── Messaging System
       ├── Active Templates
       ├── Send Log
       └── Statistics
   ```

## User Authentication System
- Registration system
- Login capability
- Password recovery
- Profile management
- Session management

## Integration Requirements
- Email service integration
- WhatsApp API for notifications
- Social media sharing buttons
- Calendar system integration
- Navigation/maps integration

## Performance and Scalability
- No significant load expected
- Basic optimization requirements
- Local storage preference
- No automatic reporting system required

## Security Requirements
- Secure user authentication
- Data privacy compliance
- Secure payment processing
- Session security
- Access control implementation

## Additional Considerations
- Mobile-first development approach
- RTL support for Hebrew content
- Expandable system architecture
- Efficient search and retrieval system
- Future feature accommodation capability