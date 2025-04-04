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

// Toggle Sidebar
document.getElementById('sidebarToggle').addEventListener('click', function() {
  document.body.classList.toggle('sidebar-toggled');
  document.querySelector('.sidebar').classList.toggle('toggled');
  
  if (document.querySelector('.sidebar').classList.contains('toggled')) {
      document.querySelector('.sidebar .collapse').classList.remove('show');
  }
});

// Tooltip
$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});

// Chart Peminjaman
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('peminjamanChart').getContext('2d');
  
  // Data dari PHP bisa diganti dengan AJAX jika diperlukan
  const labels = [];
  const data = [];
  
  // Generate data dummy untuk contoh
  for (let i = 6; i >= 0; i--) {
      const date = new Date();
      date.setDate(date.getDate() - i);
      labels.push(date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' }));
      data.push(Math.floor(Math.random() * 10) + 2);
  }
  
  const chart = new Chart(ctx, {
      type: 'line',
      data: {
          labels: labels,
          datasets: [{
              label: 'Jumlah Peminjaman',
              lineTension: 0.3,
              backgroundColor: 'rgba(78, 115, 223, 0.05)',
              borderColor: 'rgba(78, 115, 223, 1)',
              pointRadius: 3,
              pointBackgroundColor: 'rgba(78, 115, 223, 1)',
              pointBorderColor: 'rgba(78, 115, 223, 1)',
              pointHoverRadius: 3,
              pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
              pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
              pointHitRadius: 10,
              pointBorderWidth: 2,
              data: data,
          }],
      },
      options: {
          maintainAspectRatio: false,
          plugins: {
              legend: {
                  display: false
              }
          },
          scales: {
              y: {
                  beginAtZero: true,
                  ticks: {
                      stepSize: 1
                  }
              }
          }
      }
  });
  
  // Notifikasi
  if (document.querySelector('.toast')) {
      const toast = new bootstrap.Toast(document.querySelector('.toast'));
      toast.show();
  }
});

// Confirm sebelum hapus
document.querySelectorAll('.confirm-delete').forEach(button => {
  button.addEventListener('click', function(e) {
      if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
          e.preventDefault();
      }
  });
});

document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.sidebar');
    const content = document.getElementById('content');
    const sidebarCollapse = document.getElementById('sidebarCollapse');

    sidebarCollapse.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
    });
});