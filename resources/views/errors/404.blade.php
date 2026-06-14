<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Lost in Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body {
            font-family: 'Space Grotesk', sans-serif;
            background: #0f0c29;
            background: linear-gradient(to right, #0f0c29, #302b63, #24243e);
            overflow: hidden;
            height: 100vh;
        }

        .astronaut {
            position: absolute;
            width: 250px;
            animation: float 6s ease-in-out infinite;
        }

        .glow {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        }
        @keyframes float {
            0% { transform: translateY(0px) rotate(5deg); }
            50% { transform: translateY(-20px) rotate(-5deg); }
            100% { transform: translateY(0px) rotate(5deg); }
        }
        .btn-back {
            border: 1px solid white;
            color: white;
            height: 5vmin;
            padding: 12px;
            font-family: 'Nunito', sans-serif;
            text-decoration: none;
            box-shadow: 0 0 15px rgba(100, 149, 237, 0.8);
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-back:hover {
            background: white;
            color: #4D007D;
            box-shadow: 0 0 20px rgba(100, 149, 237, 1);
        }
        @keyframes twinkle {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 1; }
        }
        
        @keyframes shootingStar {
            0% { 
                transform: translateX(0) translateY(0);
                opacity: 1;
            }
            100% { 
                transform: translateX(-1000px) translateY(1000px);
                opacity: 0;
            }
        }
        
       
        .star {
            animation: twinkle 4s infinite;
        }
        
        .shooting-star {
            animation: shootingStar 3s linear infinite;
        }
        
    </style>
</head>
<body class="relative flex items-center justify-center text-white">
    <!-- Stars background -->
    <div class="absolute inset-0 overflow-hidden">
        <!-- Static stars -->
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 10%; left: 20%;"></div>
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 15%; left: 50%;"></div>
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 25%; left: 70%;"></div>
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 30%; left: 10%;"></div>
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 40%; left: 30%;"></div>
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 50%; left: 80%;"></div>
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 60%; left: 40%;"></div>
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 70%; left: 60%;"></div>
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 80%; left: 20%;"></div>
        <div class="star absolute w-1 h-1 bg-white rounded-full" style="top: 90%; left: 90%;"></div>
        
        <!-- Shooting stars -->
        <div class="shooting-star absolute w-1 h-1 bg-white rounded-full" style="top: 15%; left: 90%;"></div>
        <div class="shooting-star absolute w-1 h-1 bg-white rounded-full" style="top: 25%; left: 95%; animation-delay: 1.5s;"></div>
        <div class="shooting-star absolute w-1 h-1 bg-white rounded-full" style="top: 5%; left: 85%; animation-delay: 2.5s;"></div>
        
    </div>
    
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
        // Add random stars
        document.addEventListener('DOMContentLoaded', function() {
            const space = document.querySelector('.absolute.inset-0');
            
            // Add more random twinkling stars
            for (let i = 0; i < 50; i++) {
                const star = document.createElement('div');
                star.className = 'star absolute w-1 h-1 bg-white rounded-full';
                star.style.top = `${Math.random() * 100}%`;
                star.style.left = `${Math.random() * 100}%`;
                star.style.animationDelay = `${Math.random() * 5}s`;
                star.style.opacity = Math.random();
                space.appendChild(star);
            }
            
            // Add some random shooting stars
            for (let i = 0; i < 5; i++) {
                const shootingStar = document.createElement('div');
                shootingStar.className = 'shooting-star absolute w-1 h-1 bg-white rounded-full';
                shootingStar.style.top = `${Math.random() * 30}%`;
                shootingStar.style.left = `${80 + Math.random() * 20}%`;
                shootingStar.style.animationDelay = `${Math.random() * 10}s`;
                shootingStar.style.animationDuration = `${2 + Math.random() * 3}s`;
                space.appendChild(shootingStar);
            }

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
            
        });
        });
    </script>
</body>
</html>