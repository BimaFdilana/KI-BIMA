<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Lost in Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: #0f0c29;
            background: linear-gradient(to right, #0f0c29, #302b63, #24243e);
            overflow: hidden;
            height: 100vh;
        }
        .btn-back {
            border: 1px solid white;
            color: white;
            height: 5vmin;
            padding: 12px;
            font-family: 'Space Grotesk', sans-serif;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .btn-back:hover {
            background: white;
            color: #4D007D;
        }
        
        .stars {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%"><circle cx="10%" cy="15%" r="1" fill="white"/><circle cx="25%" cy="30%" r="1" fill="white"/><circle cx="70%" cy="10%" r="1" fill="white"/><circle cx="85%" cy="25%" r="1" fill="white"/><circle cx="15%" cy="70%" r="1" fill="white"/><circle cx="30%" cy="85%" r="1" fill="white"/><circle cx="75%" cy="65%" r="1" fill="white"/><circle cx="90%" cy="80%" r="1" fill="white"/><circle cx="50%" cy="50%" r="1" fill="white"/><circle cx="40%" cy="20%" r="1" fill="white"/><circle cx="60%" cy="30%" r="1" fill="white"/><circle cx="20%" cy="40%" r="1" fill="white"/><circle cx="80%" cy="40%" r="1" fill="white"/></svg>');
            animation: twinkle 5s infinite alternate;
        }
        
        @keyframes twinkle {
            0% { opacity: 0.3; }
            100% { opacity: 1; }
        }
        
        /* Animasi bintang jatuh */
        @keyframes shootingStar {
            0% {
                transform: translate(0, 0);
                opacity: 1;
            }
            70% {
                opacity: 1;
            }
            100% {
                transform: translate(-100vw, 100vh);
                opacity: 0;
            }
        }
        
        .shooting-star {
            position: absolute;
            height: 2px;
            background: linear-gradient(to right, rgba(255,255,255,0), rgba(255,255,255,1));
            border-radius: 2px;
            overflow: hidden;
            z-index: 1;
            /* Ekor bintang akan ditangani dengan transformasi, tidak perlu rotasi di sini */
        }
        
        .shooting-star::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 20px;
            height: 2px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 0 10px 4px rgba(255, 255, 255, 0.7);
        }
        
        .astronaut {
            position: absolute;
            width: 250px;
            animation: float 6s ease-in-out infinite;
            z-index: 2;
        }
        
        .mars {
            position: absolute;
            width: 100px;
            bottom: -20px;
            right: 50px;
        }
        
        @keyframes float {
            0% { transform: translateY(0px) rotate(5deg); }
            50% { transform: translateY(-20px) rotate(-5deg); }
            100% { transform: translateY(0px) rotate(5deg); }
        }
        
        .glow {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        }
        
        .btn-glow {
            box-shadow: 0 0 15px rgba(100, 149, 237, 0.8);
        }
        
        .btn-glow:hover {
            box-shadow: 0 0 20px rgba(100, 149, 237, 1);
        }
    </style>
</head>
<body class="relative flex items-center justify-center text-white">
    <div class="stars"></div>
    
    <!-- Astronaut floating -->
    <img src="https://assets.codepen.io/1538474/astronaut.svg" class="astronaut" style="top: 40%; left: 10%;" />
    
    <div class="relative z-10 text-center px-6 max-w-2xl">
        <h1 class="text-8xl font-bold mb-4 glow">404</h1>
        <h2 class="text-3xl font-semibold mb-6">Houston, we have a problem!</h2>
        <p class="text-lg mb-8 opacity-90">
            The page you're looking for seems to have drifted off into the vast expanse of the cosmos. 
            Maybe our astronaut can help you find your way back home.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
           <div align="center">
                <a class="btn-back" href="{{ url()->previous() }}">Back to previous page</a>
            </div>
        </div>
        
        <div class="mt-12 text-sm opacity-70">
            <p>Error code: 404_NOT_FOUND</p>
            <p class="mt-1">Mission timestamp: <span id="timestamp"></span></p>
        </div>
    </div>
    
    <script>
        // Display current timestamp
        const now = new Date();
        const timestampElement = document.getElementById('timestamp');
        timestampElement.textContent = now.toLocaleString();
        
        // Add floating stars dynamically
        document.addEventListener('DOMContentLoaded', function() {
            const starsContainer = document.querySelector('.stars');
            for (let i = 0; i < 50; i++) {
                const star = document.createElement('div');
                star.style.position = 'absolute';
                star.style.width = '1px';
                star.style.height = '1px';
                star.style.backgroundColor = 'white';
                star.style.borderRadius = '50%';
                star.style.top = `${Math.random() * 100}%`;
                star.style.left = `${Math.random() * 100}%`;
                star.style.opacity = Math.random();
                star.style.animation = `twinkle ${2 + Math.random() * 3}s infinite alternate`;
                starsContainer.appendChild(star);
            }
            
            // Fungsi untuk membuat dan menampilkan bintang jatuh
            function createShootingStar() {
                // Buat elemen bintang jatuh
                const shootingStar = document.createElement('div');
                shootingStar.className = 'shooting-star';
                
                // Ukuran bintang jatuh (acak)
                const starWidth = Math.floor(Math.random() * 150) + 50;
                shootingStar.style.width = `${starWidth}px`;
                
                // Posisi awal (dari atas kanan layar)
                const startPositionTop = Math.random() * 30; // % dari atas layar
                const startPositionRight = Math.random() * 20; // % dari kanan layar
                
                shootingStar.style.top = `${startPositionTop}%`;
                shootingStar.style.right = `${startPositionRight}%`;
                
                // Sudut jatuh bintang (acak)
                const angle = Math.random() * 20 + 30; // antara 30-50 derajat
                
                // Ini adalah perbaikan utama - transformasi bintang dengan sudut yang tepat
                // Sebelum animasi mulai, kita putar bintang sesuai dengan arah jatuhnya
                shootingStar.style.transform = `rotate(${angle}deg)`;
                
                // Kita membuat custom animation yang menggabungkan rotasi dan translasi
                // Ini memastikan ekor bintang tetap sesuai arah jatuhnya
                const keyframes = [
                    { transform: `rotate(${angle}deg) translate(0px, 0px)`, opacity: 1 },
                    { transform: `rotate(${angle}deg) translate(-${window.innerWidth}px, ${window.innerHeight * 0.5}px)`, opacity: 0 }
                ];
                
                const animationOptions = {
                    duration: Math.random() * 1500 + 800, // 0.8-2.3 detik
                    easing: 'linear',
                    fill: 'forwards'
                };
                
                // Animasi dengan API Web Animation
                const animation = shootingStar.animate(keyframes, animationOptions);
                
                // Tambahkan ke body
                document.body.appendChild(shootingStar);
                
                // Hapus bintang setelah animasi selesai
                animation.onfinish = () => {
                    shootingStar.remove();
                };
            }
            
            // Fungsi untuk menampilkan bintang jatuh dengan jeda acak
            function scheduleNextStar() {
                // Jeda acak 2-3 detik sebelum bintang berikutnya
                const delay = Math.random() * 1000 + 2000; // 2-3 detik
                
                setTimeout(() => {
                    createShootingStar();
                    scheduleNextStar(); // Jadwalkan bintang berikutnya
                }, delay);
            }
            
            // Mulai animasi bintang jatuh dengan sedikit jeda awal
            setTimeout(() => {
                scheduleNextStar();
            }, 1000);
        });
    </script>
</body>
</html>