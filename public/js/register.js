// Profile image preview + password toggles + tiny strength hint + location access
(function(){
    // Wait for DOM to be ready
    function init() {
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
    
      // Password show/hide - attach directly to buttons
      function setupPasswordToggles() {
        const toggleButtons = document.querySelectorAll('.toggle-eye');
        
        toggleButtons.forEach((btn) => {
          // Remove any existing listeners
          const newBtn = btn.cloneNode(true);
          btn.parentNode.replaceChild(newBtn, btn);
          
          newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const targetId = this.getAttribute('data-target');
            if (!targetId) return;
            
            const input = document.getElementById(targetId);
            if (!input) return;
            
            // Toggle password visibility
            if (input.type === 'password') {
              input.type = 'text';
              this.textContent = '🙈';
            } else {
              input.type = 'password';
              this.textContent = '👁';
            }
          });
        });
      }
      
      setupPasswordToggles();
  
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
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
    } else {
      // DOM already loaded, run immediately
      setTimeout(init, 100);
    }

    // Automatically request location access on page load
    const addressInput = document.getElementById('address');

    if (addressInput) {
      // Request location access automatically when page loads
      window.addEventListener('DOMContentLoaded', async () => {
        // Check if geolocation is supported
        if (!navigator.geolocation) {
          console.warn('Geolocation is not supported by your browser.');
          return;
        }

        try {
          // Request location access
          const position = await new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
              enableHighAccuracy: true,
              timeout: 10000,
              maximumAge: 0
            });
          });

          const { latitude, longitude } = position.coords;

          // Reverse geocode using the API endpoint
          const reverseGeocodeEndpoint = window.reverseGeocodeEndpoint;
          if (!reverseGeocodeEndpoint) {
            console.error('Reverse geocode endpoint not configured');
            return;
          }

          const response = await fetch(reverseGeocodeEndpoint, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
              latitude: latitude,
              longitude: longitude
            })
          });

          if (!response.ok) {
            const errorData = await response.json();
            console.error('Failed to get address:', errorData.error || 'Unknown error');
            return;
          }

          const data = await response.json();
          
          if (data.formatted_address) {
            addressInput.value = data.formatted_address;
            console.log('Address retrieved successfully:', data.formatted_address);
          } else {
            console.error('No address found for this location');
          }

        } catch (error) {
          console.error('Location error:', error);
          
          // Silently fail - address is optional
          // User can still register without location
        }
      });
    }

    // Add spin animation for loading spinner
    const style = document.createElement('style');
    style.textContent = `
      @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
    `;
    document.head.appendChild(style);
  })();
  