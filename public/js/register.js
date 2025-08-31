// Profile image preview + password toggles + tiny strength hint
(function(){
    const fileInput = document.getElementById('profile_image');
    const preview = document.getElementById('imagePreview');
    const img = document.getElementById('profileImageTag');
  
    if (fileInput && preview && img) {
      fileInput.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => {
          img.src = reader.result;
          img.style.display = 'block';
          // Hide icon + text when image is present
          preview.querySelector('i')?.style.setProperty('display', 'none');
          preview.querySelector('span')?.style.setProperty('display', 'none');
        };
        reader.readAsDataURL(file);
      });
    }
  
    // Password show/hide
    document.querySelectorAll('.toggle-eye').forEach(btn => {
      btn.addEventListener('click', () => {
        const targetId = btn.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.textContent = input.type === 'password' ? '👁' : '🙈';
      });
    });
  
    // Lightweight password strength hint
    const pw = document.getElementById('password');
    const hint = document.getElementById('pwHint');
    if (pw && hint) {
      pw.addEventListener('input', () => {
        const v = pw.value || '';
        const long = v.length >= 8;
        const hasNum = /\d/.test(v);
        const hasAlpha = /[a-zA-Z]/.test(v);
        const good = long && hasNum && hasAlpha;
  
        hint.textContent = good
          ? 'Strong password ✅'
          : 'Use 8+ chars with letters & numbers';
        hint.style.color = good ? '#37d67a' : '#9aa2b1';
      });
    }
  })();
  