# Character Images Setup Guide

This guide explains how to set up the character images for the property listing pages.

## Required Images

You need to save the following images to `public/images/` directory:

### 1. **character-wave-1.png** 
- Image: First businessman waving (Image 1)
- Used in: Homepage hero section on list-property page

### 2. **character-wave-2.png**
- Image: Second businessman waving with hand on hip (Image 2)
- Used in: Step 4 (Amenities & Rules) helper

### 3. **character-tie.png**
- Image: Businessman adjusting tie (Image 3)
- Used in: 
  - Step 1 (Basic Info) helper on list-property page
  - Edit page hero section
  - Step 1 (Basic Info) helper on edit-property page

### 4. **character-facepalm.png**
- Image: Businessman with hand on face/glasses (Image 4 - facepalm/stressed)
- Used in: Error states or validation messages (optional)

### 5. **character-ok.png**
- Image: Businessman with OK hand gesture (Image 5)
- Used in: Success confirmations (optional)

### 6. **character-thumbs-down.png**
- Image: Businessman with thumbs down (Image 6)
- Used in: Negative feedback or warnings (optional)

### 7. **character-phone.png**
- Image: Businessman holding phone (Image 7)
- Used in:
  - Step 2 (Location) helper on both pages
  - Step 5 (Images) helper on both pages

### 8. **character-thinking.png**
- Image: Businessman thinking with hand on chin (Image 8)
- Used in:
  - Wizard progress helper avatar (changes per step)
  - Step 3 (Details/Pricing) helper on both pages

## How to Add Images

1. Save each of the 8 images from your screenshot with their respective filenames
2. Place them in: `c:\Ghorfa-Project\public\images\`
3. Ensure filenames match exactly (case-sensitive on Linux servers):
   - `character-wave-1.png`
   - `character-wave-2.png`
   - `character-tie.png`
   - `character-facepalm.png`
   - `character-ok.png`
   - `character-thumbs-down.png`
   - `character-phone.png`
   - `character-thinking.png`

## Character Usage Map

### List Property Page (`list-property.blade.php`)
- **Hero Section**: character-wave-1.png
- **Progress Helper**: character-thinking.png (dynamic - changes per step)
- **Step 1 Helper**: character-tie.png
- **Step 2 Helper**: character-phone.png
- **Step 3 Helper**: character-thinking.png
- **Step 4 Helper**: character-wave-2.png
- **Step 5 Helper**: character-phone.png

### Edit Property Page (`edit-property.blade.php`)
- **Hero Section**: character-tie.png
- **Progress Helper**: character-thinking.png (dynamic)
- **Step 1 Helper**: character-tie.png
- **Step 2 Helper**: character-phone.png
- **Step 3 Helper**: character-thinking.png
- **Step 4 Helper**: character-wave-2.png
- **Step 5 Helper**: character-phone.png

## Dynamic Speech Bubbles

The wizard progress helper changes messages based on the current step:
- Step 1: "Let's get started!" / "Let's update!" (thinking.png)
- Step 2: "Pin your location!" (phone.png)
- Step 3: "Set your pricing!" (thinking.png)
- Step 4: "Add amenities!" (wave-2.png)
- Step 5: "Almost done!" / "Upload photos!" (phone.png)

## Design Features

✨ **Character-driven UI elements:**
- Floating animation on hero characters
- Bouncing animation on progress helper
- Speech bubble with pointer
- Context-specific helper boxes on each step
- Dynamic avatar and message changes as user progresses

## Technical Implementation

The character system includes:
- CSS animations (float, bounce, fadeInUp)
- JavaScript dynamic image/text switching in `list-property.js`
- Responsive layouts that adapt character size on mobile
- Filter effects (drop-shadow) for depth

## Notes

- Images should be PNG format with transparent backgrounds
- Recommended dimensions: 300-400px width for best quality
- The system gracefully handles missing images (alt text displays)
- All paths use Laravel's `asset()` helper for proper URL generation
