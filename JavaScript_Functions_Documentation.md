# JavaScript Functions Documentation
## Ghorfa Project - Complete JavaScript Reference

**Generated:** 2025-01-27  
**Project:** Ghorfa - Property Rental Platform

---

## Table of Contents

1. [list-property.js](#list-propertyjs)
2. [MapClickService.js](#mapclickservicejs)
3. [search.js](#searchjs)
4. [map.js](#mapjs)
5. [show.js](#showjs)
6. [profile.js](#profilejs)
7. [register.js](#registerjs)

---

## list-property.js

**File Path:** `public/js/list-property.js`  
**Purpose:** Handles property listing form functionality including image previews, map location selection, and form validation.

### Functions

#### `setupImagePreview(inputId, containerId)`
- **Parameters:**
  - `inputId` (string, default: `'images'`) - ID of the file input element
  - `containerId` (string, default: `'image-previews'`) - ID of the container for image previews
- **Purpose:** Sets up image preview functionality with remove buttons for property listing form
- **Features:**
  - Handles both new file uploads and existing images
  - Creates thumbnail previews with remove (×) buttons
  - Manages hidden inputs for removed existing images
  - Prevents duplicate file selection
  - Syncs file input with selected files using DataTransfer API
- **Internal Functions:**
  - `ensureRemovalInput(id)` - Creates hidden input for removed existing images
  - `removeRemovalInput(id)` - Removes hidden input when image is restored
  - `getObjectUrl(file)` - Creates object URL for file preview
  - `revokeObjectUrl(file)` - Revokes object URL to free memory
  - `renderPreviews()` - Renders all image previews (existing + new)
  - `syncInputFiles()` - Syncs file input with current files array

#### `initPropertyLocationMap()`
- **Parameters:** None (called as Google Maps callback)
- **Purpose:** Initializes Google Maps for property location selection
- **Features:**
  - Creates map centered on Lebanon (default) or uses existing coordinates
  - Integrates with MapClickService for click-to-set-location
  - Shows marker if coordinates already exist
  - Auto-enables click mode if no coordinates are set
  - Sets up toggle button for enabling/disabling click mode
  - Adds address search box using Google Places API
- **Dependencies:** 
  - Requires `MapClickService` class
  - Requires Google Maps API with Places library
  - Reads route from `data-reverse-geocode-endpoint` attribute

#### `updateCoordinatesStatus(lat, lng, isSet)`
- **Parameters:**
  - `lat` (number) - Latitude value
  - `lng` (number) - Longitude value
  - `isSet` (boolean) - Whether coordinates are set
- **Purpose:** Updates the status display showing current coordinates
- **Features:**
  - Displays formatted coordinates (6 decimal places)
  - Shows green color when set, gray when not set
  - Updates `#coordinatesStatus` element

#### `addAddressSearchBox()`
- **Parameters:** None
- **Purpose:** Adds a Google Places search box to the map for address lookup
- **Features:**
  - Creates styled search input in top-left of map
  - Uses Google Places SearchBox API
  - On place selection: centers map, zooms in, sets coordinates, fills address field
- **Dependencies:** Requires `propertyLocationMap` to be initialized

#### `validatePropertyLocation()`
- **Parameters:** None
- **Purpose:** Validates that property location is set before form submission
- **Features:**
  - Prevents form submission if latitude/longitude are missing
  - Shows alert message
  - Scrolls to enable button and activates click mode if needed
- **Called:** On form submit event

#### `loadCountries()` ⚠️
- **Status:** Function is called but not defined in current file
- **Issue:** Referenced in DOMContentLoaded but function body was removed
- **Expected Purpose:** Loads country list from external API and initializes Select2 dropdown

### Global Variables
- `propertyLocationMap` - Google Maps instance
- `propertyMapClickService` - MapClickService instance

### Initialization
```javascript
document.addEventListener('DOMContentLoaded', () => {
    loadCountries(); // ⚠️ Function missing
    setupImagePreview('images', 'image-previews');
    validatePropertyLocation();
});
```

---

## MapClickService.js

**File Path:** `public/js/MapClickService.js`  
**Purpose:** Reusable service class for handling map click events to get coordinates, with reverse geocoding support.

### Class: `MapClickService`

#### Constructor
```javascript
new MapClickService(map, options)
```
- **Parameters:**
  - `map` (google.maps.Map) - Google Maps instance
  - `options` (object, optional) - Configuration options:
    - `showMarker` (boolean, default: true) - Show marker on click
    - `showInfoWindow` (boolean, default: true) - Show info window with coordinates
    - `markerIcon` (object, optional) - Custom marker icon
    - `enableReverseGeocoding` (boolean, default: true) - Enable address lookup
    - `reverseGeocodeEndpoint` (string, default: '/map/reverse-geocode') - API endpoint for reverse geocoding

#### Methods

##### `enable()`
- **Purpose:** Enables click-to-get-coordinates functionality
- **Features:**
  - Adds click listener to map
  - Changes cursor to crosshair
  - Applies `clickable` CSS class to map container
  - Sets cursor on all child elements

##### `disable()`
- **Purpose:** Disables click functionality
- **Features:**
  - Removes click listener
  - Resets cursor to default
  - Removes `clickable` CSS class

##### `toggle()`
- **Purpose:** Toggles between enabled/disabled states

##### `handleMapClick(latLng)`
- **Parameters:**
  - `latLng` (google.maps.LatLng) - Clicked location
- **Purpose:** Handles map click event
- **Features:**
  - Extracts latitude/longitude
  - Shows marker if enabled
  - Performs reverse geocoding if enabled
  - Shows info window if enabled
  - Calls all registered callbacks
- **Returns:** Promise (async function)

##### `setMarker(latLng)`
- **Parameters:**
  - `latLng` (google.maps.LatLng) - Marker position
- **Purpose:** Creates/updates marker at specified location
- **Features:**
  - Removes existing marker if present
  - Creates new marker with optional custom icon
  - Supports draggable markers (if configured)

##### `showInfoWindow(latLng, coordinates)`
- **Parameters:**
  - `latLng` (google.maps.LatLng) - Location
  - `coordinates` (object) - Coordinates object with latitude, longitude, address
- **Purpose:** Displays info window with coordinate information
- **Features:**
  - Shows formatted coordinates
  - Displays address if available
  - Includes copy-to-clipboard button

##### `createInfoWindowContent(coordinates)`
- **Parameters:**
  - `coordinates` (object) - Coordinates object
- **Purpose:** Generates HTML content for info window
- **Returns:** HTML string

##### `reverseGeocode(latitude, longitude)`
- **Parameters:**
  - `latitude` (number) - Latitude
  - `longitude` (number) - Longitude
- **Purpose:** Converts coordinates to address
- **Features:**
  - Tries server-side endpoint first
  - Falls back to client-side Google Geocoder API
- **Returns:** Promise<string> - Formatted address

##### `clientSideReverseGeocode(latitude, longitude)`
- **Parameters:**
  - `latitude` (number) - Latitude
  - `longitude` (number) - Longitude
- **Purpose:** Client-side reverse geocoding using Google Maps API
- **Returns:** Promise<string> - Formatted address

##### `onClick(callback)`
- **Parameters:**
  - `callback` (function) - Function to call when map is clicked
- **Purpose:** Registers callback for click events
- **Callback receives:** `{latitude, longitude, address}` object

##### `offClick(callback)`
- **Parameters:**
  - `callback` (function) - Callback to remove
- **Purpose:** Unregisters a callback

##### `getCoordinates()`
- **Purpose:** Gets current coordinates from marker
- **Returns:** `{latitude, longitude}` object or `null`

##### `clear()`
- **Purpose:** Removes marker and closes info window

### Global Functions

#### `copyCoordinates(lat, lng)`
- **Parameters:**
  - `lat` (number) - Latitude
  - `lng` (number) - Longitude
- **Purpose:** Copies coordinates to clipboard
- **Features:**
  - Uses modern Clipboard API
  - Falls back to execCommand for older browsers
  - Shows alert confirmation

### Properties
- `map` - Google Maps instance
- `clickListener` - Google Maps event listener
- `marker` - Current marker instance
- `infoWindow` - Info window instance
- `isEnabled` - Current enabled state
- `callbacks` - Array of registered callbacks
- `options` - Configuration options

---

## search.js

**File Path:** `public/js/search.js`  
**Purpose:** Handles property search page functionality including filters, sorting, likes, and UI interactions.

### Functions

#### `clearAllFilters()`
- **Parameters:** None
- **Purpose:** Clears all filter inputs in the search form
- **Features:**
  - Clears text and number inputs
  - Unchecks all checkboxes
  - Resets all select dropdowns to first option
  - Clears custom validation messages

#### `ShowFilterToggle()`
- **Parameters:** None
- **Purpose:** Manages filter panel visibility and toggle functionality
- **Features:**
  - Handles filter toggle button click (clears filters and submits form)
  - Manages filter overlay and close button
  - Closes filters when clicking outside
  - Prevents body scroll when filters are open
- **Internal Functions:**
  - `closeFilters()` - Hides filter panel
  - `openFilters()` - Shows filter panel

#### `ShowSettingslist()`
- **Parameters:** None
- **Purpose:** Manages settings dropdown menus on property cards
- **Features:**
  - Toggles dropdown on button click
  - Closes other dropdowns when one opens
  - Closes on outside click
  - Prevents event propagation

#### `validatePriceRange()`
- **Parameters:** None
- **Purpose:** Validates min/max price inputs
- **Features:**
  - Ensures min price is not greater than max price
  - Sets custom validation message
  - Clears validation when inputs are empty

#### `initPropertySort(selectId, gridSelector)`
- **Parameters:**
  - `selectId` (string, default: `'sort-options'`) - ID of sort select element
  - `gridSelector` (string, default: `'.listings-grid'`) - Selector for listings grid
- **Purpose:** Implements client-side property sorting
- **Features:**
  - Sorts by: price (low/high), date (newest/latest), recommended (by likes)
  - Saves sort preference to localStorage
  - Updates URL with sort parameter
  - Reads initial sort from URL or localStorage
- **Sort Functions:**
  - `byPriceAsc` - Sort by price ascending
  - `byPriceDesc` - Sort by price descending
  - `byDateDesc` - Sort by creation date descending
  - `byDateAsc` - Sort by creation date ascending
  - `byLikesDesc` - Sort by likes descending

#### `initLoginButtons()`
- **Parameters:** None
- **Purpose:** Handles redirect to login for favorite buttons when user is not authenticated
- **Features:**
  - Finds buttons with `data-login-url` attribute
  - Redirects to login page on click

#### `initLikeButtons()`
- **Parameters:** None
- **Purpose:** Handles property like/unlike functionality
- **Features:**
  - Sends POST request to `/properties/{id}/like`
  - Updates heart icon (solid/outline)
  - Updates like count display
  - Updates card's `data-likes` attribute
  - Removes card from favorites page if unliked
  - Re-sorts if using recommended sort
- **API Endpoint:** `POST /properties/{propertyId}/like`
- **Response:** `{status: 'liked'|'unliked', count: number}`

#### `initSearchPage()`
- **Parameters:** None
- **Purpose:** Initializes all search page functionality
- **Calls:**
  - `ShowFilterToggle()`
  - `ShowSettingslist()`
  - `initPropertySort()`
  - `initLikeButtons()`
  - `initLoginButtons()`

### Initialization
```javascript
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSearchPage);
} else {
    initSearchPage();
}
```

---

## map.js

**File Path:** `public/js/map.js`  
**Purpose:** Handles interactive map page showing all properties with markers and search functionality.

### Global Variables
- `map` - Google Maps instance
- `markers` - Array of property markers
- `infoWindow` - Info window instance
- `mapClickService` - MapClickService instance
- `properties` - Array of property data

### Functions

#### `initializeProperties(data)`
- **Parameters:**
  - `data` (array) - Array of property objects
- **Purpose:** Initializes properties data from Blade template
- **Called:** By Blade template via `window.initializeProperties()`

#### `initMap()`
- **Parameters:** None (called as Google Maps callback)
- **Purpose:** Initializes the main map view
- **Features:**
  - Creates map centered on Lebanon
  - Adds property markers
  - Adds search box
  - Initializes MapClickService if available
- **Dependencies:** Requires Google Maps API

#### `initializeMapClickService()`
- **Parameters:** None
- **Purpose:** Sets up MapClickService for coordinate selection
- **Features:**
  - Reads reverse geocode endpoint from `window.mapConfig`
  - Creates MapClickService instance
  - Sets up toggle button
  - Updates coordinate display on click
- **Dependencies:** Requires `MapClickService` class

#### `updateCoordinatesDisplay(coordinates)`
- **Parameters:**
  - `coordinates` (object) - `{latitude, longitude}` object
- **Purpose:** Updates displayed coordinates in UI
- **Updates:** `#displayLat` and `#displayLng` elements

#### `addPropertyMarkers()`
- **Parameters:** None
- **Purpose:** Adds markers for all properties on the map
- **Features:**
  - Creates custom SVG markers with house emoji
  - Adds click listeners to show info windows
  - Fits map bounds to show all markers
- **Marker Icon:** Custom blue circle with 🏠 emoji

#### `createInfoWindowContent(property)`
- **Parameters:**
  - `property` (object) - Property data object
- **Purpose:** Creates HTML content for property info window
- **Features:**
  - Shows primary image or placeholder
  - Displays title, price, location, details
  - Includes "View Details" link and like button
- **Returns:** HTML string

#### `addSearchBox()`
- **Parameters:** None
- **Purpose:** Adds Google Places search box to map
- **Features:**
  - Styled input in top-left corner
  - Focus/blur styling effects
  - Fits map bounds to search results
- **Dependencies:** Requires Google Places API

#### `clearFilters()`
- **Parameters:** None
- **Purpose:** Clears filter form and redirects to map page
- **Features:**
  - Resets filter form
  - Redirects to map route (from `window.mapConfig.mapRoute`)

#### `toggleLike(propertyId, buttonElement)`
- **Parameters:**
  - `propertyId` (number) - Property ID
  - `buttonElement` (HTMLElement) - Like button element
- **Purpose:** Toggles like status for a property
- **Features:**
  - Shows loading state
  - Sends POST request to `/properties/{id}/like`
  - Updates button text and style
  - Handles authentication errors
  - Adds scale animation on success
- **API Endpoint:** `POST /properties/{propertyId}/like`

### Event Listeners
- Window resize: Triggers map resize event

### Configuration
Reads from `window.mapConfig`:
- `reverseGeocodeEndpoint` - API endpoint for reverse geocoding
- `storageUrl` - Base URL for property images
- `placeholderUrl` - Placeholder image URL
- `mapRoute` - Route for map page

---

## show.js

**File Path:** `public/js/show.js`  
**Purpose:** Handles property detail/show page functionality including image gallery, likes, reviews, and contact features.

### Global Variables
- `currentImageIndex` - Current image index in modal
- `imageSources` - Array of image URLs
- `totalImages` - Total number of images

### Image Modal Functions

#### `openImageModal(imageSrc)`
- **Parameters:**
  - `imageSrc` (string) - Source URL of clicked image
- **Purpose:** Opens image modal gallery
- **Features:**
  - Collects all images from gallery
  - Finds index of clicked image
  - Shows modal and updates display
  - Creates thumbnails

#### `closeImageModal()`
- **Parameters:** None
- **Purpose:** Closes image modal

#### `updateModalImage()`
- **Parameters:** None
- **Purpose:** Updates modal with current image
- **Features:** Adds loading opacity effect

#### `updateImageCounter()`
- **Parameters:** None
- **Purpose:** Updates image counter display (e.g., "1 / 5")

#### `updateNavigationButtons()`
- **Parameters:** None
- **Purpose:** Enables/disables prev/next buttons based on current index

#### `createThumbnails()`
- **Parameters:** None
- **Purpose:** Creates thumbnail strip for navigation
- **Features:** Highlights active thumbnail

#### `goToImage(index)`
- **Parameters:**
  - `index` (number) - Image index to navigate to
- **Purpose:** Navigates to specific image
- **Features:** Validates index range

#### `updateThumbnailSelection()`
- **Parameters:** None
- **Purpose:** Updates active thumbnail highlight

#### `nextImage()`
- **Parameters:** None
- **Purpose:** Navigates to next image

#### `previousImage()`
- **Parameters:** None
- **Purpose:** Navigates to previous image

### Like Functionality

#### `initLikeButton()`
- **Parameters:** None
- **Purpose:** Initializes like button functionality
- **Features:**
  - Sends POST to `/properties/{id}/like`
  - Updates heart icon and text
  - Updates like count
  - Adds scale animation
  - Shows error notification on failure
- **API Endpoint:** `POST /properties/{propertyId}/like`

### Contact Functionality

#### `initContactButtons()`
- **Parameters:** None
- **Purpose:** Initializes contact button handlers
- **Features:**
  - Handles "Call Now" (primary) button
  - Handles "Send Message" (secondary) button
  - Shows contact modal

#### `showContactModal(type)`
- **Parameters:**
  - `type` (string) - `'call'` or `'message'`
- **Purpose:** Shows contact modal (placeholder)
- **Features:**
  - Creates modal dynamically
  - Shows appropriate message based on type
  - Closes on outside click

### Navigation Functions

#### `initBackButton()`
- **Parameters:** None
- **Purpose:** Handles back button functionality
- **Features:**
  - Detects if coming from edit page
  - Goes back 2 pages if from edit, otherwise 1 page
  - Shows loading state

#### `initImageGallery()`
- **Parameters:** None
- **Purpose:** Initializes keyboard navigation for image gallery
- **Features:**
  - Makes gallery items focusable
  - Handles Enter/Space key to open modal
  - Adds ARIA attributes

#### `initActionButtons()`
- **Parameters:** None
- **Purpose:** Handles action buttons (delete, etc.)
- **Features:**
  - Confirms delete action
  - Shows loading state
  - Auto-re-enables after timeout

### Review System Functions

#### `initReviewSystem()`
- **Parameters:** None
- **Purpose:** Initializes review form functionality
- **Features:**
  - Star rating selection with text feedback
  - Character counter for review text (max 1000)
  - Form validation (rating + min 10 chars)
  - Color-coded character count
  - Loading state on submit

#### `showAllReviews()`
- **Parameters:** None
- **Purpose:** Placeholder for loading all reviews (future AJAX implementation)

#### `editUserReview(reviewId)`
- **Parameters:**
  - `reviewId` (number) - Review ID to edit
- **Purpose:** Placeholder for editing user review (future implementation)

#### `openReviewModal()`
- **Parameters:** None
- **Purpose:** Opens review submission modal
- **Features:**
  - Focuses first input
  - Prevents body scroll

#### `closeReviewModal()`
- **Parameters:** None
- **Purpose:** Closes review modal and resets form

### Utility Functions

#### `showNotification(message, type)`
- **Parameters:**
  - `message` (string) - Notification message
  - `type` (string) - `'error'`, `'success'`, or `'info'`
- **Purpose:** Shows temporary notification
- **Features:**
  - Creates styled notification element
  - Animates in from right
  - Auto-removes after 5 seconds
  - Color-coded by type

### Initialization
```javascript
document.addEventListener('DOMContentLoaded', function() {
    initLikeButton();
    initContactButtons();
    initBackButton();
    initImageGallery();
    initActionButtons();
    initReviewSystem();
    // Fade-in animation for main content
});
```

### Keyboard Navigation
- **Escape:** Close image modal
- **Arrow Left:** Previous image
- **Arrow Right:** Next image
- **Space:** Next image

### Exported Functions (Global)
- `window.openImageModal`
- `window.closeImageModal`
- `window.nextImage`
- `window.previousImage`
- `window.openReviewModal`
- `window.closeReviewModal`
- `window.showAllReviews`
- `window.editUserReview`

---

## profile.js

**File Path:** `public/js/profile.js`  
**Purpose:** Handles user profile page functionality including edit mode, avatar upload, password toggles, and dropdown menus.

### Helper Functions (IIFE)
- `$` - Query selector shorthand
- `$$` - Query all selector shorthand
- `show(el, display)` - Show element
- `hide(el)` - Hide element
- `isVisible(el)` - Check if element is visible

### Edit Mode Functions

#### Edit Mode Toggle
- **Elements:**
  - `#toggleEditBtn` - Button to enter edit mode
  - `#exitEditBtn` - Button to exit edit mode
  - `#editProfileSection` - Edit form section
- **Features:**
  - Shows/hides edit form
  - Focuses first input on enter
  - ESC key closes edit mode (if modal not open)

### Profile Image Functions

#### Profile Image Preview
- **Elements:**
  - `#profile_image` - File input
  - `#imagePreview` - Preview container
  - `#profileImageTag` - Image element
- **Features:**
  - Shows preview when file selected
  - Hides placeholder icon/text
  - Creates image element if missing

#### Inline Avatar Upload
- **Element:** `#avatarFile`
- **Features:** Auto-submits form on file selection

#### Avatar Modal
- **Elements:**
  - `#avatarClickTarget` - Trigger element
  - `#avatarModal` - Modal element
  - `.avatar-modal-close` - Close button
  - `.avatar-modal-backdrop` - Backdrop
  - `#avatarFileModal` - File input in modal
- **Functions:**
  - `openAvatarModal()` - Opens modal, prevents body scroll
  - `closeAvatarModal()` - Closes modal, restores scroll
- **Features:**
  - ESC key closes modal
  - Click outside closes modal
  - Auto-submits form on file selection
  - Focus management

### Password Functions

#### Password Eye Toggles
- **Elements:** `.toggle-eye` buttons
- **Features:**
  - Toggles password visibility
  - Updates button text (👁/🙈)
  - Updates ARIA label
  - Uses `data-target` attribute

### Character Counter

#### Textarea Character Counter
- **Elements:**
  - `#about` - Textarea
  - `.char-counter` - Counter element
- **Features:**
  - Shows current/max characters
  - Changes color when over limit
  - Reads max from `data-max` attribute (default: 500)

### Navigation Dropdown

#### Profile Dropdown
- **Elements:**
  - `.up` - Up arrow (when closed)
  - `.down` - Down arrow (when open)
  - `.profile-dropdown` - Dropdown menu
  - `.nav-profile-image` - Profile image trigger
- **Functions:**
  - `showDropdown()` - Shows dropdown
  - `hideDropdown()` - Hides dropdown
- **Features:**
  - Toggles on arrow/image click
  - Closes on outside click
  - Tracks visibility state

### Initialization
```javascript
document.addEventListener('DOMContentLoaded', () => {
    // All functionality initialized
});
```

---

## register.js

**File Path:** `public/js/register.js`  
**Purpose:** Handles user registration form functionality including profile image preview and password strength validation.

### Functions (IIFE)

#### Profile Image Preview
- **Elements:**
  - `#profile_image` - File input
  - `#imagePreview` - Preview container
  - `#profileImageTag` - Image element
- **Features:**
  - Shows preview when file selected
  - Hides placeholder icon/text
  - Uses FileReader API

#### Password Show/Hide
- **Elements:** `.toggle-eye` buttons
- **Features:**
  - Toggles password visibility
  - Updates button text (👁/🙈)
  - Uses `data-target` attribute

#### Password Strength Hint
- **Elements:**
  - `#password` - Password input
  - `#pwHint` - Hint element
- **Features:**
  - Validates: 8+ chars, has numbers, has letters
  - Shows "Strong password ✅" when valid
  - Shows requirements when invalid
  - Color-coded (green/gray)

### Initialization
```javascript
(function(){
    // All functionality initialized immediately
})();
```

---

## Summary Statistics

### Total Files: 7
### Total Functions: ~60+

### File Breakdown:
- **list-property.js:** 5 main functions + internal helpers
- **MapClickService.js:** 1 class with 13 methods + 1 global function
- **search.js:** 8 main functions
- **map.js:** 8 main functions
- **show.js:** 20+ functions
- **profile.js:** Multiple feature modules (IIFE)
- **register.js:** 3 feature modules (IIFE)

### Common Patterns:
- **Event Listeners:** DOMContentLoaded initialization
- **API Calls:** Fetch API with CSRF token
- **Error Handling:** Try-catch blocks with console.error
- **UI Feedback:** Loading states, animations, notifications
- **Accessibility:** ARIA attributes, keyboard navigation

### Dependencies:
- **Google Maps API:** Used in list-property.js, map.js, MapClickService.js
- **Google Places API:** Used for address search
- **jQuery/Select2:** Referenced in list-property.js (loadCountries)
- **Font Awesome:** Used for icons throughout

### Known Issues:
1. ⚠️ `loadCountries()` function is called but not defined in `list-property.js`
2. Some functions use global variables (could be improved with modules)

---

## Notes

- All files use vanilla JavaScript (no framework dependencies except jQuery/Select2 in one case)
- MapClickService is designed as a reusable service class
- Most files use IIFE pattern or DOMContentLoaded for initialization
- API endpoints follow RESTful conventions
- CSRF protection is implemented for all POST requests

---

**End of Documentation**

