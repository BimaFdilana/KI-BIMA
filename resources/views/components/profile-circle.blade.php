@auth
    <!-- Settings Dropdown -->
    <div class="relative ms-3">

        <!-- Avatar Dropdown - With Gender-Based Avatar -->
        <div class="relative ms-3" x-data="{ isOpen: false }">
            <button @click="isOpen = !isOpen" @click.away="isOpen = false" class="flex items-center focus:outline-none">
                <x-user-avatar :user="Auth::user()" :show-status="true" size="sm" />
            </button>

            <div x-show="isOpen" x-transition:enter="dropdown-enter" x-transition:enter-start="dropdown-enter" x-transition:enter-end="dropdown-enter-active" x-transition:leave="dropdown-exit" x-transition:leave-start="dropdown-exit" x-transition:leave-end="dropdown-exit-active" class="absolute right-0 z-10 mt-2 w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-lg">
                <div class="{{ Auth::user()->getAvatarGradientClass() }} p-4 text-white">
                    <div class="flex items-center space-x-3">
                        <x-user-avatar :user="Auth::user()" :show-status="true" size="md" />
                        <div>
                            <p class="font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-xs opacity-80">{{ Auth::user()->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') }}</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    <a href="{{ route('profile') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-user mr-3 w-5 text-gray-400"></i>
                        <span>Profile</span>
                        @if (!Auth::user()->profile_completed)
                            <span class="ml-auto rounded-full bg-yellow-100 px-2 py-0.5 text-xs text-yellow-800">Incomplete</span>
                        @endif
                    </a>
                    <a href="" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-cog mr-3 w-5 text-gray-400"></i>
                        <span>Settings</span>
                    </a>
                    <a href="" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-envelope mr-3 w-5 text-gray-400"></i>
                        <span>Messages</span>
                    </a>
                </div>

                @role('programmer')
                    @if (env('TELESCOPE_ENABLED') == true)
                        <a href="{{ route('telescope') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-rocket mr-3 w-5 text-gray-400"></i>
                            <span>Debug</span>
                        </a>
                    @endif
                @endrole


                <div class="bg-gray-50 p-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full rounded-md px-4 py-2 text-center text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endauth
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const weatherIcon = document.getElementById("weather-icon");
            const temperatureElement = document.getElementById("temperature");
            const apiKey = "f2b48aa70562d65e11a72f268776dc4b";

            // Check if geolocation is supported
            if (navigator.geolocation) {
                // Request geolocation
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Fetch weather data based on user's coordinates
                    const apiUrl =
                        `https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&appid=${apiKey}&units=metric`;

                    fetch(apiUrl)
                        .then(response => response.json())
                        .then(data => {
                            const description = data.weather[0].description;
                            const temperatureCelsius = Math.round(data.main.temp);
                            const iconCode = data.weather[0].icon;

                            // Display temperature
                            temperatureElement.textContent = `${temperatureCelsius}°C`;

                            // Set weather icon
                            const iconClass = getWeatherIconClass(iconCode);
                            weatherIcon.classList.add(iconClass);
                            weatherIcon.setAttribute('aria-label', description);
                        })
                        .catch(error => {
                            console.error("Error fetching weather data:", error);
                            temperatureElement.textContent = "Error fetching weather data";
                        });
                });
            } else {
                console.log("Geolocation is not supported by this browser.");
                temperatureElement.textContent = "Geolocation is not supported";
            }
        });

        // Function to get weather icon class based on icon code
        function getWeatherIconClass(iconCode) {
            switch (iconCode) {
                case "01d":
                case "01n":
                    return "bi-sun";
                case "02d":
                case "02n":
                    return "bi-cloud-sun";
                case "03d":
                case "03n":
                    return "bi-cloud";
                case "04d":
                case "04n":
                    return "bi-clouds";
                case "09d":
                case "09n":
                    return "bi-cloud-rain";
                case "10d":
                case "10n":
                    return "bi-cloud-sun-rain";
                case "11d":
                case "11n":
                    return "bi-lightning";
                case "13d":
                case "13n":
                    return "bi-snow";
                case "50d":
                case "50n":
                    return "bi-cloud-haze";
                default:
                    return "bi-question-circle";
            }
        }
    </script>
@endpush
