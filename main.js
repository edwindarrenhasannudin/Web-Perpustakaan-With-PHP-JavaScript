const text = "Selamat Datang di Perpustakaan Daerah Kabupaten Lampung Selatan";
let i = 0;
let isDeleting = false;
const speed = 100; // Kecepatan mengetik (ms)
const pauseBetween = 2000; // Jeda setelah selesai (ms)
const element = document.getElementById("typing-text");

// Fungsi untuk menjaga tinggi container
function maintainHeight() {
    const container = document.querySelector('.typing-container');
    const tempElement = document.createElement('h1');
    tempElement.textContent = text;
    tempElement.style.visibility = 'hidden';
    tempElement.style.position = 'absolute';
    document.body.appendChild(tempElement);
    
    container.style.minHeight = tempElement.offsetHeight + 'px';
    document.body.removeChild(tempElement);
}

function typeWriter() {
    if (!isDeleting && i < text.length) {
        element.innerHTML = text.substring(0, i + 1);
        i++;
        setTimeout(typeWriter, speed);
    } 
    else if (i === text.length && !isDeleting) {
        isDeleting = true;
        setTimeout(typeWriter, pauseBetween);
    }
    else if (isDeleting && i > 0) {
        element.innerHTML = text.substring(0, i - 1);
        i--;
        setTimeout(typeWriter, speed / 2);
    }
    else {
        isDeleting = false;
        setTimeout(typeWriter, speed);
    }
}

// Jalankan saat halaman dimuat
window.onload = function() {
  typeWriter();
};