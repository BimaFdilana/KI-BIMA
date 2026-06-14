<section class="relative h-[65svh] w-full">

    <!-- Background Image untuk Desktop (Laptop) -->
    <img src="{{ url('/storage/assets_images/images/KasirLaptop.png') }}"
        alt="Background Image Laptop"
        class="absolute inset-0 z-0 hidden h-full w-full object-cover lg:block">

    <!-- Background Image untuk Mobile (Handphone) -->
    <img src="{{ url('/storage/assets_images/images/KasirLaptop.png') }}"
        alt="Background Image HP"
        class="absolute inset-0 z-0 h-full w-full object-cover lg:hidden">

    <!-- Kontainer untuk konten di atas gambar -->
    <div
        class="absolute inset-0 z-10 flex flex-col items-start justify-center bg-gradient-to-r from-transparent via-transparent to-black/50 p-8 text-gray-800">

        <!-- Title -->
        <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl">
            Kembangkan Bisnis Anda Bersama Kedai Indonesia
        </h2>

        <!-- Deskripsi -->
        <p class="mb-6 text-lg text-gray-700 sm:text-xl">
            Bergabung dengan kami sekarang, banyak orang sudah <br> bergabung untuk mempermudah bisnis Anda.
        </p>

        <!-- Kolom Icon Orang dan Jumlah -->
        <div class="mb-6 flex items-center space-x-2 text-red-600">

            <i class="fas fa-store text-2xl"></i>

            <p class="text-xl font-bold">
                <span id="userCount" class="count-up">0</span>
                Kedai Sudah Bergabung
            </p>

        </div>

        <!-- Download Section -->
        <p class="mb-4 text-lg text-gray-900 sm:text-xl">
            Download sekarang, tersedia di:
        </p>

        <!-- App Store dan Play Store Button -->
        <div class="flex justify-center space-x-4 sm:justify-start">

            <!-- <a href="https://drive.google.com/drive/folders/1ERwkJbb1GQRzuUZZ7QB_S2G7DVQfu3jN?usp=sharing"
                target="_blank"
                class="block w-32">

                <img src="{{ url('/storage/assets_images/logo/appstore.png') }}"
                    alt="Download on the App Store"
                    class="w-full">

            </a> -->

            <a href="https://drive.google.com/drive/folders/1ERwkJbb1GQRzuUZZ7QB_S2G7DVQfu3jN?usp=sharing"
                target="_blank"
                class="block w-32">

                <img src="{{ url('/storage/assets_images/logo/playstore.png') }}"
                    alt="Get it on Google Play"
                    class="w-full">

            </a>

        </div>

    </div>

</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const userCount = document.getElementById("userCount");

        let count = 0;
        let target = {{ $tokoCount }};
        let duration = 3000;
        let step = target / (duration / 30);

        // Fungsi format angka
        function formatNumber(number) {

            if (number >= 1000) {

                return (number / 1000).toFixed(2) + 'K';

            }

            return number;
        }

        function incrementCount() {

            if (count < target) {

                count += step;

                userCount.textContent = formatNumber(Math.floor(count));

                setTimeout(incrementCount, 30);

            } else {

                userCount.textContent = formatNumber(target);

            }
        }

        incrementCount();

    });
</script>