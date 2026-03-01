# Character Images – Where They Appear on the Website

All character images live in **`public/images/character/`**. This file lists every place each character is used in the app.

---

## By image file

| Image | Page / File | Location / Purpose |
|-------|-------------|---------------------|
| **tie.png** | Landlord Dashboard | Header: "Manage your properties" |
| **tie.png** | Landlord Dashboard | Empty state: Requests table |
| **tie.png** | Landlord Dashboard | Empty state: Active transactions table |
| **tie.png** | Profile → Info | Banner above profile card |
| **tie.png** | Profile → Transactions | Header: "Your Requests" |
| **tie.png** | Profile → Transactions | Empty state: No requests yet |
| **tie.png** | List Property | Step helper (e.g. step 2) |
| **tie.png** | Edit Property | Header + step helper |
| **wave-1.png** | Login | Left panel: "Welcome back!" |
| **wave-1.png** | List Property | Header: "Welcome" |
| **wave-2.png** | Home | Feature block: "Connect" (and list-property / edit-property step helpers) |
| **wave-2.png** | List Property | Step helper |
| **wave-2.png** | Edit Property | Step helper |
| **thinking.png** | List Property | Header helper + step helpers |
| **thinking.png** | Edit Property | Header helper + step helpers |
| **phone.png** | List Property | Step helpers (location, contact) |
| **phone.png** | Edit Property | Step helpers |
| **home-hero.png** | Home | Hero section (desktop + mobile) |
| **search-looking.png** | Home | Feature: "Search" |
| **search-looking.png** | Search | Empty state: No properties found |
| **search-looking.png** | Profile → Info (Favorites tab) | Empty state: No favorites yet |
| **search-magnifying.png** | Home | Feature: "Filter" |
| **map-navigating.png** | Home | Feature: "Map" |
| **map-navigating.png** | Map | Header: "Navigate!" |
| **success-celebrating.png** | Home | CTA: "Join us!" |
| **success-celebrating.png** | Register | Side panel: "Join us!" |
| **view-thumbs-up.png** | Property show | Request/booking helper (e.g. "Great choice!") |
| **dashboard-empty.png** | Landlord Dashboard | Empty: No requests; Empty: No transactions |
| **dashboard-empty.png** | Profile → Transactions | Empty: No requests yet |

---

## By page / view

| Page / View | Characters used |
|-------------|-----------------|
| **Home** (`resources/views/home.blade.php`) | home-hero, search-looking, search-magnifying, map-navigating, wave-2, success-celebrating |
| **Login** (`resources/views/login.blade.php`) | wave-1 |
| **Register** (`resources/views/register.blade.php`) | success-celebrating |
| **Search** (`resources/views/search.blade.php`) | search-looking (empty state) |
| **Map** (`resources/views/map.blade.php`) | map-navigating |
| **List Property** (`resources/views/list-property.blade.php`) | wave-1, thinking, tie, phone, wave-2 |
| **Edit Property** (`resources/views/edit-property.blade.php`) | tie, thinking, phone, wave-2 |
| **Property show** (`resources/views/show.blade.php`) | view-thumbs-up |
| **Profile → Info** (`resources/views/profile/info.blade.php`) | tie (banner), search-looking (Favorites empty) |
| **Profile → Transactions** (`resources/views/profile/transactions.blade.php`) | tie, dashboard-empty |
| **Landlord Dashboard** (`resources/views/landlord/dashboard.blade.php`) | tie (header), dashboard-empty (requests + transactions empty) |

---

## List-property wizard (JS)

**File:** `public/js/list-property.js`  
Step helper images (by step index):

- Step 1: thinking.png  
- Step 2: phone.png  
- Step 3: thinking.png  
- Step 4: wave-2.png  
- Step 5: phone.png  

---

## Assets in `public/images/character/`

Used in views/JS above:

- dashboard-empty.png  
- error-404.png *(if used in error views)*  
- error-500.png *(if used in error views)*  
- favorites-empty.png *(referenced in old favorites flow; profile Favorites tab now uses search-looking)*  
- home-hero.png  
- map-navigating.png  
- phone.png  
- search-looking.png  
- search-magnifying.png  
- success-celebrating.png  
- tie.png  
- thinking.png  
- view-thumbs-up.png  
- wave-1.png  
- wave-2.png  

Optional / not referenced in current views:

- loading-running.png  
- notifications-alert.png  

---

*Single reference doc for character usage. For paths, search the repo for `images/character/`.*
