# Entity-Relationship Diagram for Ghorfa Project Database

## ER Diagram (Mermaid)

```mermaid
erDiagram
    USERS ||--o{ PROPERTIES : "owns"
    USERS ||--o{ REVIEWS : "writes"
    USERS ||--o{ PROPERTY_LIKES : "likes"
    USERS ||--o| LANDLORD_APPLICATIONS : "submits"
    USERS ||--o{ NOTIFICATIONS : "receives"
    USERS ||--o{ ACTIVITIES : "performs"
    USERS ||--o{ PROPERTIES : "approves"
    USERS ||--o{ LANDLORD_APPLICATIONS : "reviews"
    
    PROPERTIES ||--o{ PROPERTY_IMAGES : "has"
    PROPERTIES ||--o{ REVIEWS : "receives"
    PROPERTIES ||--o{ PROPERTY_LIKES : "liked_by"
    PROPERTIES }o--|| UNITS : "uses"
    PROPERTIES }o--o{ AMENITIES : "has"
    PROPERTIES }o--o{ RULES : "has"
    
    USERS {
        bigint id PK
        string first_name
        string last_name
        string email UK
        string password
        string phone_nb UK
        string profile_image
        date date_of_birth
        text address
        enum role "client,admin,landlord"
        timestamp email_verified_at
        timestamp last_login_at
        timestamp created_at
        timestamp updated_at
    }
    
    PROPERTIES {
        bigint id PK
        string title
        text description
        string property_type
        string listing_type
        string country
        string city
        string address
        decimal price
        decimal area_m3
        integer room_nb
        integer bathroom_nb
        integer bedroom_nb
        decimal latitude
        decimal longitude
        bigint user_id FK
        bigint unit_id FK
        enum status "pending,approved,rejected"
        timestamp approved_at
        bigint approved_by FK
        timestamp created_at
        timestamp updated_at
    }
    
    AMENITIES {
        bigint id PK
        string name
        timestamp created_at
        timestamp updated_at
    }
    
    RULES {
        bigint id PK
        string name
        timestamp created_at
        timestamp updated_at
    }
    
    PROPERTY_IMAGES {
        bigint id PK
        bigint property_id FK
        string path
        boolean is_primary
        timestamp created_at
        timestamp updated_at
    }
    
    UNITS {
        bigint id PK
        string name
        string code UK
        string symbol
        double price_in_dollar
        timestamp created_at
        timestamp updated_at
    }
    
    REVIEWS {
        bigint id PK
        bigint property_id FK
        bigint user_id FK
        tinyint rating
        text comment
        timestamp created_at
        timestamp updated_at
    }
    
    PROPERTY_LIKES {
        bigint id PK
        bigint user_id FK
        bigint property_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    AMENITY_PROPERTY {
        bigint id PK
        bigint property_id FK
        bigint amenity_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    PROPERTY_RULE {
        bigint id PK
        bigint property_id FK
        bigint rule_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    LANDLORD_APPLICATIONS {
        bigint id PK
        bigint user_id FK
        string phone
        text address
        string id_number
        string trade_license
        text notes
        enum status "pending,approved,rejected"
        text admin_notes
        bigint reviewed_by FK
        timestamp reviewed_at
        timestamp created_at
        timestamp updated_at
    }
    
    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        string type
        string title
        text message
        string notifiable_type
        bigint notifiable_id
        boolean read
        timestamp read_at
        timestamp created_at
        timestamp updated_at
    }
    
    ACTIVITIES {
        bigint id PK
        string type
        string description
        string subject_type
        bigint subject_id
        bigint user_id FK
        json properties
        timestamp created_at
        timestamp updated_at
    }
```

## Database Schema Summary

### Core Entities

1. **USERS** - User accounts with roles (client, admin, landlord)
2. **PROPERTIES** - Property listings with location, pricing, and details
3. **UNITS** - Currency/unit types for property pricing
4. **AMENITIES** - Available amenities (many-to-many with properties)
5. **RULES** - Property rules (many-to-many with properties)

### Relationship Entities

6. **PROPERTY_IMAGES** - Images associated with properties (one-to-many)
7. **REVIEWS** - User reviews for properties (many-to-many)
8. **PROPERTY_LIKES** - User likes for properties (many-to-many)
9. **AMENITY_PROPERTY** - Pivot table for property amenities
10. **PROPERTY_RULE** - Pivot table for property rules

### Application & Activity Entities

11. **LANDLORD_APPLICATIONS** - Applications to become a landlord
12. **NOTIFICATIONS** - User notifications (polymorphic)
13. **ACTIVITIES** - Activity log (polymorphic)

## Key Relationships

- **Users → Properties**: One-to-Many (user owns multiple properties)
- **Users → Reviews**: One-to-Many (user writes multiple reviews)
- **Users → Property Likes**: Many-to-Many (users can like multiple properties)
- **Properties → Amenities**: Many-to-Many (properties can have multiple amenities)
- **Properties → Rules**: Many-to-Many (properties can have multiple rules)
- **Properties → Unit**: Many-to-One (properties use a currency unit)
- **Properties → Images**: One-to-Many (property has multiple images)
- **Properties → Approved By**: Many-to-One (admin approves properties)
- **Landlord Applications → Reviewed By**: Many-to-One (admin reviews applications)
- **Notifications**: Polymorphic (can reference properties, applications, etc.)
- **Activities**: Polymorphic (can reference various entities)

## Notes

- **Polymorphic Relations**: 
  - `notifications.notifiable_type` and `notifications.notifiable_id` can reference different entities
  - `activities.subject_type` and `activities.subject_id` can reference different entities
  
- **Unique Constraints**:
  - `users.email` - unique
  - `users.phone_nb` - unique
  - `units.code` - unique
  - `property_likes(user_id, property_id)` - unique (user can only like a property once)

- **Cascade Deletes**:
  - Deleting a user cascades to their properties, reviews, likes, and applications
  - Deleting a property cascades to its images, reviews, likes, and pivot table entries
  - Deleting a unit cascades to properties using it
