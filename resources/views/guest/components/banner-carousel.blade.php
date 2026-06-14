@props([
    'slideCount' => 6,
])

<section id="bannerCarousel" x-data="{
    autoplayIntervalTime: 5000,
    slides: [
        @for ($i = 1; $i <= $slideCount; $i++) { 
                imgSrc: '{{ url("/storage/assets_images/images/carousel/bg{$i}.png") }}',
                imgSrcHp: '{{ url("/storage/assets_images/images/carousel/bg{$i}hp.png") }}',
                title: 'Slide Title ' + {{ $i }},
                subtitle: 'Subtitle for slide ' + {{ $i }}
            }, @endfor
    ],
    currentSlideIndex: 1,
    previousSlideIndex: null,
    isPaused: false,
    autoplayInterval: null,
    slideDirection: 'right',
    previous() {
        this.slideDirection = 'left';
        this.previousSlideIndex = this.currentSlideIndex;
        if (this.currentSlideIndex > 1) {
            this.currentSlideIndex = this.currentSlideIndex - 1;
        } else {
            this.currentSlideIndex = this.slides.length;
        }
    },
    next() {
        this.slideDirection = 'right';
        this.previousSlideIndex = this.currentSlideIndex;
        if (this.currentSlideIndex < this.slides.length) {
            this.currentSlideIndex = this.currentSlideIndex + 1;
        } else {
            this.currentSlideIndex = 1;
        }
    },
    autoplay() {
        this.autoplayInterval = setInterval(() => {
            if (!this.isPaused) {
                this.next();
            }
        }, this.autoplayIntervalTime);
    },
    setAutoplayInterval(newIntervalTime) {
        clearInterval(this.autoplayInterval);
        this.autoplayIntervalTime = newIntervalTime;
        this.autoplay();
    }
}" x-init="autoplay()" class="relative w-full overflow-hidden">
    <!-- Previous button dengan animasi hover -->
    <button type="button" class="absolute left-5 top-1/2 z-20 flex -translate-y-1/2 items-center justify-center rounded-full bg-white/30 p-2 text-red-600 transition-all duration-300 hover:scale-110 hover:bg-white/70 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:outline-offset-0" aria-label="previous slide" x-on:click="previous()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="3" class="size-5 pr-0.5 md:size-6" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
    </button>

    <!-- Next button dengan animasi hover  -->
    <button type="button" class="absolute right-5 top-1/2 z-20 flex -translate-y-1/2 items-center justify-center rounded-full bg-white/30 p-2 text-red-600 transition-all duration-300 hover:scale-110 hover:bg-white/70 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:outline-offset-0" aria-label="next slide" x-on:click="next()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="3" class="size-5 pl-0.5 md:size-6" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>

    <!-- Slides dengan efek parallax -->
    <div class="relative h-[60vh] sm:min-h-[95svh] w-full">
        <template x-for="(slide, index) in slides" :key="index">
            <div x-cloak x-show="currentSlideIndex == index + 1" x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0">

                <!-- Gradient overlay untuk teks -->
                <div class="absolute inset-0 z-10 bg-gradient-to-t from-neutral-950/60 to-transparent"></div>

                <!-- Desktop Image dengan Parallax -->
                <div class="parallax-container hidden h-full w-full overflow-hidden sm:block">
                    <img class="parallax-img duration-10000 h-full w-full scale-95 object-cover transition-transform" x-bind:src="slide.imgSrc" x-bind:alt="'Slide ' + (index + 1)" data-speed="0.4" />
                </div>

                <!-- Mobile Image dengan Parallax -->
                <div class="parallax-container h-full w-full overflow-hidden sm:hidden">
                <img
    class="parallax-img h-full w-full object-contain transition-transform"
    x-bind:src="slide.imgSrcHp">
                </div>

                <!-- Konten Slide dengan animasi -->
                {{-- <div class="absolute inset-x-0 bottom-0 z-20 flex flex-col items-center justify-end gap-2 p-8 text-center text-white opacity-90 lg:p-14" x-show="currentSlideIndex == index + 1" x-transition:enter="transition ease-out duration-1000 delay-300" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0">
                    <h2 class="text-2xl font-bold md:text-4xl lg:text-5xl" x-text="slide.title"></h2>
                    <p class="text-sm md:text-lg" x-text="slide.subtitle"></p>
                </div> --}}
            </div>
        </template>
    </div>

    <!-- Indikator Slide -->
    <div class="absolute bottom-10 left-1/2 z-20 flex -translate-x-1/2 gap-2">
        <template x-for="(slide, index) in slides" :key="index">
            <button class="h-2 w-2 rounded-full transition-all duration-300 focus:outline-none focus:ring focus:ring-white/50" :class="currentSlideIndex == index + 1 ? 'w-8 bg-white' : 'bg-white/50'" x-on:click="currentSlideIndex = index + 1" :aria-label="'Go to slide ' + (index + 1)">
            </button>
        </template>
    </div>

    <!-- Pause/Play Button dengan animasi hover -->
    <button type="button" class="absolute bottom-5 right-5 z-20 rounded-full text-red-300 opacity-50 transition-all duration-300 hover:scale-110 hover:opacity-100 focus-visible:opacity-80 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white active:outline-offset-0" aria-label="pause carousel" x-on:click="(isPaused = !isPaused), setAutoplayInterval(autoplayIntervalTime)" x-bind:aria-pressed="isPaused">
        <svg x-cloak x-show="isPaused" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-7">
            <path fill-rule="evenodd" d="M2 10a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm6.39-2.908a.75.75 0 0 1 .766.027l3.5 2.25a.75.75 0 0 1 0 1.262l-3.5 2.25A.75.75 0 0 1 8 12.25v-4.5a.75.75 0 0 1 .39-.658Z" clip-rule="evenodd">
        </svg>
        <svg x-cloak x-show="!isPaused" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-7">
            <path fill-rule="evenodd" d="M2 10a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm5-2.25A.75.75 0 0 1 7.75 7h.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-.75.75h-.5a.75.75 0 0 1-.75-.75v-4.5Zm4 0a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-.75.75h-.5a.75.75 0 0 1-.75-.75v-4.5Z" clip-rule="evenodd">
        </svg>
    </button>
</section>

<style>
    /* Container untuk efek parallax */
    .parallax-container {
        position: relative;
        overflow: hidden;
    }

    /* Animasi zoom otomatis pada gambar */
    /* @keyframes parallax-zoom {
        0% {
            transform: scale(0.92);
        }

        100% {
            transform: scale(1.05);
        }
    } */

    /* .parallax-img {
        animation: parallax-zoom 15s infinite alternate ease-in-out;
        will-change: transform;
        transform-origin: center center;
    } */
</style>

<script>
    // Script untuk efek parallax yang terpisah dari Alpine.js
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi parallax scroll
        initParallaxScroll();

        // Menjalankan fungsi setiap kali carousel slide berubah
        document.addEventListener('DOMNodeInserted', function(e) {
            if (e.target.classList && e.target.classList.contains('parallax-container')) {
                initParallaxScroll();
            }
        });
    });

    function initParallaxScroll() {
        const parallaxContainers = document.querySelectorAll('.parallax-container');

        // Hapus event listener lama jika ada
        window.removeEventListener('scroll', handleParallaxScroll);

        // Tambahkan event listener baru
        window.addEventListener('scroll', handleParallaxScroll);

        // Jalankan sekali untuk posisi awal
        handleParallaxScroll();

        function handleParallaxScroll() {
            parallaxContainers.forEach(function(container) {
                const img = container.querySelector('.parallax-img');
                if (!img) return;

                const rect = container.getBoundingClientRect();
                const speed = parseFloat(img.getAttribute('data-speed')) || 0.4;

                // Hanya terapkan parallax jika container terlihat di viewport
                if (rect.bottom >= 0 && rect.top <= window.innerHeight) {
                    // Posisi relatif container terhadap viewport (0 = atas viewport, 1 = bawah viewport)
                    const containerPos = rect.top / window.innerHeight;

                    // Hitung perpindahan parallax (dalam persentase tinggi container)
                    const yOffset = (containerPos * speed * 100);

                    // Terapkan transformasi ke gambar
                    img.style.transform = `translate3d(0, ${yOffset}px, 0) scale(1.1)`;
                }
            });
        }
    }

    // Tambahkan event listener untuk resize
    window.addEventListener('resize', function() {
        initParallaxScroll();
    });

    // Tambahkan MutationObserver untuk memantau perubahan DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                initParallaxScroll();
            }
        });
    });

    // Mulai observasi
    observer.observe(document.getElementById('bannerCarousel'), {
        childList: true,
        subtree: true
    });
</script>
