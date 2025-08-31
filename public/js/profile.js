// public/js/profile.js
(function () {
    const $  = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  
    document.addEventListener('DOMContentLoaded', () => {
      /* =========================
         Edit mode toggle
      ========================= */
      const toggleBtn   = $('#toggleEditBtn');
      const exitBtn     = $('#exitEditBtn');
      const editSection = $('#editProfileSection');
  
      function show(el, display = '')   { if (el) el.style.display = display; }
      function hide(el)                 { if (el) el.style.display = 'none'; }
      function isVisible(el)            { return !!el && getComputedStyle(el).display !== 'none'; }
  
      if (toggleBtn && editSection) {
        toggleBtn.addEventListener('click', () => {
          show(editSection, 'block');
          show(exitBtn, 'flex');
          hide(toggleBtn);
          const firstInput = $('#first_name') || editSection.querySelector('input,select,textarea,button');
          firstInput && firstInput.focus();
        });
      }
  
      if (exitBtn && editSection) {
        exitBtn.addEventListener('click', () => {
          hide(editSection);
          show(toggleBtn, 'inline-block');
          hide(exitBtn);
          toggleBtn && toggleBtn.focus();
        });
      }
  
      // Allow ESC to exit edit mode (when open and modal not open)
      document.addEventListener('keydown', (e) => {
        const avatarModal = $('#avatarModal');
        const modalOpen = avatarModal?.getAttribute('aria-hidden') === 'false';
        if (e.key === 'Escape' && isVisible(editSection) && !modalOpen) {
          exitBtn?.click();
        }
      });
  
      /* =========================
         Profile image preview (edit form)
      ========================= */
      const profileImageInput = $('#profile_image');
      const imagePreview      = $('#imagePreview');
      const profileImageTag   = $('#profileImageTag');
  
      if (profileImageInput && imagePreview) {
        profileImageInput.addEventListener('change', () => {
          const file = profileImageInput.files?.[0];
          if (!file) return;
  
          const reader = new FileReader();
          reader.onload = (e) => {
            // Hide placeholder icon/text if present
            const icon = imagePreview.querySelector('i');
            const span = imagePreview.querySelector('span');
            if (icon) icon.style.display = 'none';
            if (span) span.style.display = 'none';
  
            // Show image (ensure <img> exists)
            if (profileImageTag) {
              profileImageTag.src = e.target.result;
              profileImageTag.style.display = 'block';
            } else {
              const img = document.createElement('img');
              img.id = 'profileImageTag';
              img.src = e.target.result;
              img.alt = 'Profile image preview';
              img.style.maxWidth = '100%';
              img.style.height = 'auto';
              imagePreview.appendChild(img);
            }
          };
          reader.readAsDataURL(file);
        });
      }
  
      /* =========================
         Inline avatar upload (pencil icon)
      ========================= */
      const avatarInput = $('#avatarFile');
      if (avatarInput) {
        avatarInput.addEventListener('change', () => {
          if (avatarInput.files && avatarInput.files.length > 0) {
            avatarInput.closest('form')?.submit();
          }
        });
      }
  
      /* =========================
         Avatar modal open/close
      ========================= */
      const avatarClickTarget = $('#avatarClickTarget');
      const avatarModal       = $('#avatarModal');
      const avatarModalClose  = $('.avatar-modal-close');
      const avatarBackdrop    = $('.avatar-modal-backdrop');
      const avatarInputModal  = $('#avatarFileModal');
  
      function openAvatarModal() {
        if (!avatarModal) return;
        avatarModal.classList.add('open');
        avatarModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        avatarModalClose?.focus();
      }
  
      function closeAvatarModal() {
        if (!avatarModal) return;
        avatarModal.classList.remove('open');
        avatarModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        avatarClickTarget?.focus();
      }
  
      // Triggers
      if (avatarClickTarget) {
        avatarClickTarget.addEventListener('click', openAvatarModal);
        avatarClickTarget.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openAvatarModal(); }
        });
      }
      avatarBackdrop?.addEventListener('click', closeAvatarModal);
      avatarModalClose?.addEventListener('click', closeAvatarModal);
  
      // ESC closes modal
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && avatarModal?.getAttribute('aria-hidden') === 'false') {
          closeAvatarModal();
        }
      });
  
      // Auto-submit inside modal
      if (avatarInputModal) {
        avatarInputModal.addEventListener('change', () => {
          avatarInputModal.closest('form')?.submit();
        });
      }
  
      /* =========================
         Password eye toggles
      ========================= */
      $$('.toggle-eye').forEach((btn) => {
        btn.addEventListener('click', function () {
          const targetId = this.getAttribute('data-target');
          const input = document.getElementById(targetId);
          if (!input) return;
  
          if (input.type === 'password') {
            input.type = 'text';
            this.textContent = '🙈';
            this.setAttribute('aria-label', 'Hide password');
          } else {
            input.type = 'password';
            this.textContent = '👁';
            this.setAttribute('aria-label', 'Show password');
          }
        });
      });
  
      /* =========================
         Textarea character counter
      ========================= */
      const aboutTextarea = $('#about');
      const charCounter   = $('.char-counter');
      if (aboutTextarea && charCounter) {
        const max = parseInt(charCounter.getAttribute('data-max'), 10) || 500;
        const update = () => {
          const len = aboutTextarea.value.length;
          charCounter.textContent = `${len}/${max}`;
          charCounter.style.color = len > max ? 'var(--error)' : 'var(--muted)';
        };
        aboutTextarea.addEventListener('input', update);
        update(); // init
      }
  
      /* =========================
         Nav profile dropdown
      ========================= */
      const upArrow         = $('.up');
      const downArrow       = $('.down');
      const dropdown        = $('.profile-dropdown');
      const navProfileImage = $('.nav-profile-image');
  
      let dropdownVisible = false;
  
      function showDropdown() {
        if (!upArrow || !downArrow || !dropdown) return;
        upArrow.style.display = 'none';
        downArrow.style.display = 'block';
        dropdown.style.display = 'flex';
        dropdownVisible = true;
      }
  
      function hideDropdown() {
        if (!upArrow || !downArrow || !dropdown) return;
        upArrow.style.display = 'block';
        downArrow.style.display = 'none';
        dropdown.style.display = 'none';
        dropdownVisible = false;
      }
  
      // Guard against missing elements
      if (upArrow && downArrow && dropdown) {
        upArrow.addEventListener('click', (e) => { e.stopPropagation(); showDropdown(); });
        downArrow.addEventListener('click', (e) => { e.stopPropagation(); hideDropdown(); });
        navProfileImage?.addEventListener('click', (e) => {
          e.stopPropagation();
          dropdownVisible ? hideDropdown() : showDropdown();
        });
  
        document.addEventListener('click', (e) => {
          if (!dropdown.contains(e.target) && e.target !== upArrow && e.target !== downArrow && e.target !== navProfileImage) {
            hideDropdown();
          }
        });
  
        hideDropdown(); // init to hidden
      }
    });
  })();
  