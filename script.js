const container = document.querySelector(".container"),
      pwShowHide = document.querySelectorAll(".showHidePw"),
      pwFields = document.querySelectorAll(".password"),
      signUp = document.querySelector(".signup-link"),
      login = document.querySelector(".login-link");

    // js code to show/hide password and change icon
    pwShowHide.forEach((eyeIcon) => {
      eyeIcon.addEventListener("click", () => {
        pwFields.forEach((pwField) => {
          if (pwField.type === "password") {
            pwField.type = "text";
            pwShowHide.forEach((icon) => {
              icon.classList.replace("uil-eye-slash", "uil-eye");
            });
          } else {
            pwField.type = "password";
            pwShowHide.forEach((icon) => {
              icon.classList.replace("uil-eye", "uil-eye-slash");
            });
          }
        });
      });
    });
    
    // js code to appear signup and login form
    signUp.addEventListener("click", (e) => {
      e.preventDefault();
      container.classList.add("active");
    });
    login.addEventListener("click", (e) => {
      e.preventDefault();
      container.classList.remove("active");
    });

    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.background-slideshow img');
        let currentIndex = 0;
        
        function showNextImage() {
            // Sembunyikan gambar saat ini
            images[currentIndex].classList.remove('active');
            
            // Pindah ke gambar berikutnya
            currentIndex = (currentIndex + 1) % images.length;
            
            // Tampilkan gambar berikutnya
            images[currentIndex].classList.add('active');
        }
        
        // Ganti gambar setiap 5 detik
        setInterval(showNextImage, 5000);
    });

// Toggle between forms
document.querySelectorAll('.signup-link, .login-link').forEach(link => {
  link.addEventListener('click', (e) => {
      e.preventDefault();
      const formType = new URL(link.href).searchParams.get('form');
      document.querySelector('.login').style.display = formType === 'login' ? 'block' : 'none';
      document.querySelector('.signup').style.display = formType === 'register' ? 'block' : 'none';
      history.pushState(null, null, `auth.php?form=${formType}`);
  });
});