# Character Placement Visual Guide

## List Property Page Layout

```
╔═══════════════════════════════════════════════════════════════════╗
║                         HERO SECTION                              ║
║  ┌─────────────────────────────────────────────────────────────┐ ║
║  │                                                               │ ║
║  │   List Your Space              [character-wave-1.png]       │ ║
║  │   Follow the steps to          (350px, floating)            │ ║
║  │   create your listing                                        │ ║
║  │                                                               │ ║
║  └─────────────────────────────────────────────────────────────┘ ║
╚═══════════════════════════════════════════════════════════════════╝

╔═══════════════════════════════════════════════════════════════════╗
║                      WIZARD PROGRESS BAR                          ║
║  ┌─────────────────────────────────────────────────────────────┐ ║
║  │                                                               │ ║
║  │  [character-thinking.png]  "Let's get started!"             │ ║
║  │  (120px, bouncing)         [Speech Bubble]                   │ ║
║  │                                                               │ ║
║  │      ①────②────③────④────⑤                                 │ ║
║  │    Basic  Loc  Det  Feat  Img                               │ ║
║  │                                                               │ ║
║  └─────────────────────────────────────────────────────────────┘ ║
╚═══════════════════════════════════════════════════════════════════╝

╔═══════════════════════════════════════════════════════════════════╗
║                          STEP 1 - BASIC INFO                      ║
║  ┌─────────────────────────────────────────────────────────────┐ ║
║  │ [character-tie.png]  Let's start with the basics!           │ ║
║  │ (80px)               Tell us about your property...         │ ║
║  └─────────────────────────────────────────────────────────────┘ ║
║                                                                   ║
║  [Form fields for title, description, property type...]          ║
╚═══════════════════════════════════════════════════════════════════╝

╔═══════════════════════════════════════════════════════════════════╗
║                         STEP 2 - LOCATION                         ║
║  ┌─────────────────────────────────────────────────────────────┐ ║
║  │ [character-phone.png]  Where's your property?               │ ║
║  │ (80px)                 Click on the map to pin...           │ ║
║  └─────────────────────────────────────────────────────────────┘ ║
║                                                                   ║
║  [Google Map for location selection...]                          ║
╚═══════════════════════════════════════════════════════════════════╝

╔═══════════════════════════════════════════════════════════════════╗
║                         STEP 3 - DETAILS                          ║
║  ┌─────────────────────────────────────────────────────────────┐ ║
║  │ [character-thinking.png]  Now for the details!              │ ║
║  │ (80px)                    Set your pricing...               │ ║
║  └─────────────────────────────────────────────────────────────┘ ║
║                                                                   ║
║  [Form fields for price, size, bedrooms, etc...]                 ║
╚═══════════════════════════════════════════════════════════════════╝

╔═══════════════════════════════════════════════════════════════════╗
║                     STEP 4 - AMENITIES & RULES                    ║
║  ┌─────────────────────────────────────────────────────────────┐ ║
║  │ [character-wave-2.png]  What makes your place special?      │ ║
║  │ (80px)                  Select amenities and rules...       │ ║
║  └─────────────────────────────────────────────────────────────┘ ║
║                                                                   ║
║  [Amenity checkboxes and house rules...]                         ║
╚═══════════════════════════════════════════════════════════════════╝

╔═══════════════════════════════════════════════════════════════════╗
║                          STEP 5 - IMAGES                          ║
║  ┌─────────────────────────────────────────────────────────────┐ ║
║  │ [character-phone.png]  Almost there! Add photos             │ ║
║  │ (80px)                 Great photos make all...             │ ║
║  └─────────────────────────────────────────────────────────────┘ ║
║                                                                   ║
║  [Image upload interface...]                                     ║
╚═══════════════════════════════════════════════════════════════════╝
```

## Edit Property Page Layout

```
╔═══════════════════════════════════════════════════════════════════╗
║                         HERO SECTION                              ║
║  ┌─────────────────────────────────────────────────────────────┐ ║
║  │                                                               │ ║
║  │   Edit Property                [character-tie.png]          │ ║
║  │   Follow the steps to          (350px, floating)            │ ║
║  │   update your listing                                        │ ║
║  │                                                               │ ║
║  └─────────────────────────────────────────────────────────────┘ ║
╚═══════════════════════════════════════════════════════════════════╝

[Same wizard progress bar and steps as list property page]
```

## Character State Changes

### Progress Bar Helper (Dynamic)
```
Step 1: character-thinking.png   "Let's get started!"
Step 2: character-phone.png      "Pin your location!"
Step 3: character-thinking.png   "Set your pricing!"
Step 4: character-wave-2.png     "Add amenities!"
Step 5: character-phone.png      "Almost done!"
```

### Step Indicators (Dynamic States)
```
Pending:   ① (Gray circle)
Active:    ① (Blue circle with glow)
Completed: ✓ (Green circle with checkmark)
```

## Image Sizes Reference

| Location | Dimension | Animation |
|----------|-----------|-----------|
| Hero Character | 350px | Float (3s) |
| Progress Helper | 120px | Bounce (2s) |
| Step Helpers | 80px | None |
| Mobile Hero | 250px | Float (3s) |
| Mobile Helper | 60px | Bounce (2s) |

## Color Coding

```css
Primary Blue:   #2563eb (Active steps)
Success Green:  #10b981 (Completed steps)
Gray:           #9ca3af (Pending steps)
Light Blue BG:  #eff6ff (Helper cards)
```

## Responsive Breakpoints

```
Desktop (> 1100px):  All features active
Tablet (768-1100px): Helper moves relative, characters scale
Mobile (< 768px):    Vertical stack, smaller characters
```

## Animation Timeline

```
Page Load:
  0.0s → Hero fades in
  0.2s → Progress bar slides in
  0.4s → Helper character bounces in
  0.6s → Speech bubble pops up

Step Change:
  0.0s → Old content fades out
  0.1s → New content fades in
  0.2s → Speech bubble text updates
  0.3s → Avatar image changes

Step Complete:
  0.0s → Circle pulses (scale 1.0 → 1.2 → 1.0)
  0.2s → Checkmark pops (scale 0 → 1.3 → 1.0 with rotation)
  0.5s → Animation complete
```

## File Structure

```
c:\Ghorfa-Project\
├── public/
│   ├── images/
│   │   ├── character-wave-1.png       ← SAVE HERE
│   │   ├── character-wave-2.png       ← SAVE HERE
│   │   ├── character-tie.png          ← SAVE HERE
│   │   ├── character-phone.png        ← SAVE HERE
│   │   ├── character-thinking.png     ← SAVE HERE
│   │   ├── character-ok.png           ← SAVE HERE
│   │   ├── character-thumbs-down.png  ← SAVE HERE
│   │   ├── character-facepalm.png     ← SAVE HERE
│   │   └── IMAGE-MAPPING.md
│   ├── css/
│   │   └── list-property.css          ← Modified
│   └── js/
│       └── list-property.js           ← Modified
├── resources/
│   └── views/
│       ├── list-property.blade.php    ← Modified
│       └── edit-property.blade.php    ← Modified
├── CHARACTER-IMAGES-SETUP.md          ← Full guide
├── CHARACTER-DESIGN-SUMMARY.md        ← Implementation details
├── QUICK-START.md                     ← This guide
└── VISUAL-GUIDE.md                    ← You are here
```

## Testing Sequence

1. **Load page** → See hero character floating
2. **View progress bar** → See helper with speech bubble
3. **Read Step 1** → See tie character with blue card
4. **Click "Next"** → Speech bubble changes to "Pin your location!"
5. **Continue to Step 2** → See phone character
6. **Go back to Step 1** → Step 1 circle turns green with ✓
7. **Click Step 1 circle** → Jump back to Step 1
8. **Resize window** → Characters adapt responsively

## Success Criteria

✅ All 8 images saved in correct folder
✅ Filenames match exactly
✅ Hero character visible and animating
✅ Progress helper shows with bubble
✅ Speech bubble updates on navigation
✅ Step helpers appear on each step
✅ Completed steps show green checkmark
✅ Clickable navigation works
✅ Responsive on mobile
✅ No console errors

**You're all set! The character-driven experience is ready to go! 🎉**
