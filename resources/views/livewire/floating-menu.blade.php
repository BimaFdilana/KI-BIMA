<button id="scrollButton" class="fixed bottom-10 right-[4%] z-50 flex justify-center">
    <a href="#top" class="rounded-lg bg-red-600 p-2 text-white shadow-lg hover:bg-red-700 hover:text-gray-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" />
        </svg>
    </a>
</button>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const scrollButton = document.getElementById("scrollButton");
        const sectionsToHide = [document.getElementById("bannerCarousel"), document.getElementById("footer")];

        const observer = new IntersectionObserver(
            (entries) => {
                let isAnySectionVisible = entries.some((entry) => entry.isIntersecting);
                if (isAnySectionVisible) {
                    scrollButton.classList.add("hidden", "animate-flip-up", "animate-duration-500");
                } else {
                    scrollButton.classList.remove("hidden");
                }
            }, {
                root: null, // Default: viewport
                threshold: 0.1, // Elemen dianggap terlihat jika 10% masuk ke viewport
            }
        );

        // // Mulai observe setiap section yang ingin disembunyikan
        // sectionsToHide.forEach((section) => observer.observe(section));
    });
</script>
