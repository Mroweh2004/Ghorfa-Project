# 🎨 CHARACTERS TO GENERATE - QUICK REFERENCE

## 🔴 GENERATE THESE 2 FIRST (High Priority)

---

### 1. `dashboard-empty.png`
**Save to:** `public/images/character/dashboard-empty.png`

**Used in 3 locations:**
- Profile Transactions (when no requests)
- Landlord Dashboard Requests (when empty)
- Landlord Dashboard Transactions (when empty)

**Pose Description:**
- Hand on chin
- Slightly tilted head
- Confused but friendly expression
- "Hmm, thinking" pose
- One eyebrow raised
- Business casual with tie

**AI Prompt:**
```
3D cartoon businessman character, confused expression, hand on chin, 
slightly tilted head to the side, puzzled look, one eyebrow raised, 
questioning gesture, friendly demeanor, business casual attire with tie, 
soft lighting, transparent background, 1024x1024px, high quality render
```

**Size:** 320x320px minimum
**Format:** PNG with transparent background

---

### 2. `favorites-empty.png`
**Save to:** `public/images/character/favorites-empty.png`

**Used in 1 location:**
- Profile Favorites (when user has no saved properties)

**Pose Description:**
- Sad/disappointed expression
- Shoulders slightly slumped
- Empty hands showing "nothing here"
- Sympathetic look
- Pouty face
- Business casual with tie

**AI Prompt:**
```
3D cartoon businessman character, sad disappointed expression, 
shoulders slightly slumped, both hands open showing empty palms, 
sympathetic look, pouty face, business casual attire with tie, 
soft lighting, transparent background, 1024x1024px, high quality render
```

**Size:** 280x280px minimum
**Format:** PNG with transparent background

---

## 🟡 GENERATE THESE NEXT (Medium Priority - Error Pages)

---

### 3. `error-404.png`
**Save to:** `public/images/character/error-404.png`

**Future use:** 404 Error Page

**Pose Description:**
- Scratching head
- Confused/lost look
- One hand raised in questioning gesture
- Looking around
- Friendly but puzzled

**AI Prompt:**
```
3D cartoon businessman character, scratching head with one hand, 
confused lost expression, other hand raised in questioning gesture, 
looking around lost, friendly demeanor, business casual attire with tie, 
soft lighting, transparent background, 1024x1024px, high quality render
```

**Size:** 350x350px minimum

---

### 4. `error-500.png`
**Save to:** `public/images/character/error-500.png`

**Future use:** 500 Server Error Page

**Pose Description:**
- Worried/apologetic expression
- Both hands up in "sorry" gesture
- Palms facing forward
- Slightly leaning back
- Concerned face

**AI Prompt:**
```
3D cartoon businessman character, worried apologetic expression, 
both hands raised up in sorry gesture, palms facing forward, 
slightly leaning back, concerned face, regretful look, 
business casual attire with tie, soft lighting, transparent background, 
1024x1024px, high quality render
```

**Size:** 350x350px minimum

---

## 🟢 GENERATE THESE LATER (Low Priority - Enhancements)

---

### 5. `loading-running.png`
**Save to:** `public/images/character/loading-running.png`

**Future use:** Loading states across website

**Pose Description:**
- Dynamic running pose
- One leg forward (mid-stride)
- Arms pumping
- Determined expression
- Motion/energy

**AI Prompt:**
```
3D cartoon businessman character, dynamic running pose, 
one leg forward in mid-stride, arms pumping in running motion, 
determined energetic expression, motion blur effect, 
business casual attire with tie flowing, soft lighting, 
transparent background, 1024x1024px, high quality render
```

**Size:** 300x300px minimum

---

### 6. `notifications-alert.png`
**Save to:** `public/images/character/notifications-alert.png`

**Future use:** Notifications page

**Pose Description:**
- One finger raised (like "notice this!")
- Alert/excited expression
- Slightly leaning forward
- Engaging gesture
- Other hand on hip

**AI Prompt:**
```
3D cartoon businessman character, alert attentive expression, 
one index finger raised up like "notice this!", excited look, 
slightly leaning forward, engaging gesture, other hand on hip, 
business casual attire with tie, soft lighting, transparent background, 
1024x1024px, high quality render
```

**Size:** 280x280px minimum

---

## ✅ AFTER GENERATING

### Save Files:
1. Save all images to: `public/images/character/`
2. Ensure file names match exactly (lowercase, dash-separated)
3. Verify PNG format with transparent background

### Test:
```bash
php artisan cache:clear
php artisan view:clear
```

Then visit:
- Profile → Transactions (should see `dashboard-empty.png`)
- Profile → Favorites (should see `favorites-empty.png` if no favorites)
- Landlord Dashboard (should see `dashboard-empty.png` in empty tables)

---

## 🎯 GENERATION ORDER RECOMMENDATION

**Day 1:** Generate `dashboard-empty.png` and `favorites-empty.png`
→ Test these immediately

**Day 2:** Generate `error-404.png` and `error-500.png`
→ We'll implement error pages after

**Day 3:** Generate `loading-running.png` and `notifications-alert.png`
→ Enhancement features

---

## 📏 TECHNICAL SPECS FOR ALL

- **Resolution:** 1024x1024px (will be scaled down automatically)
- **Format:** PNG
- **Background:** Transparent
- **Character Style:** 3D cartoon businessman (match existing characters)
- **Lighting:** Soft, even lighting from top-left
- **Shadow:** Subtle (CSS will add drop-shadow)
- **Color:** Consistent with existing character palette

---

## 🖼️ VISUAL REFERENCE

All new characters should match the style of your existing characters:
- Same character model/face
- Same clothing style (business casual with tie)
- Same 3D rendering quality
- Same lighting and shadows
- Same proportions

**Existing characters to match:** Check `wave-1.png`, `tie.png`, `thinking.png` for style reference.

---

**Priority:** Start with `dashboard-empty.png` (most used!)
**Estimated Time:** 10-15 minutes per character with AI tools
**Total Images Needed:** 6 characters
