<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl">
        <div>
            <ul class="flex flex-col text-gray-700 md:hidden lg:hidden xl:hidden">
                <li class="bg-white shadow-lg" x-data="accordion(1)">
                    <h2 @click="handleClick()" class="flex cursor-pointer flex-row items-center justify-between p-3 font-semibold">
                        <span>Belanja</span>
                        <svg :class="handleRotate()" class="h-6 w-6 transform fill-current text-red-600 transition-transform duration-500" viewBox="0 0 20 20">
                            <path d="M13.962,8.885l-3.736,3.739c-0.086,0.086-0.201,0.13-0.314,0.13S9.686,12.71,9.6,12.624l-3.562-3.56C5.863,8.892,5.863,8.611,6.036,8.438c0.175-0.173,0.454-0.173,0.626,0l3.25,3.247l3.426-3.424c0.173-0.172,0.451-0.172,0.624,0C14.137,8.434,14.137,8.712,13.962,8.885 M18.406,10c0,4.644-3.763,8.406-8.406,8.406S1.594,14.644,1.594,10S5.356,1.594,10,1.594S18.406,5.356,18.406,10 M17.521,10c0-4.148-3.373-7.521-7.521-7.521c-4.148,0-7.521,3.374-7.521,7.521c0,4.147,3.374,7.521,7.521,7.521C14.148,17.521,17.521,14.147,17.521,10">
                            </path>
                        </svg>
                    </h2>
                    <div x-ref="tab" :style="handleToggle()" class="max-h-0 overflow-hidden border-l-2 border-purple-600 transition-all duration-500">
                        <p class="p-3 text-gray-300">
                            Shipping time is set by our delivery partners, according to the delivery method chosen by you.
                            Additional details can be found in the order confirmation
                        </p>
                    </div>
                </li>
                <li class="bg-gray-100 shadow-lg" x-data="accordion(2)">
                    <h2 @click="handleClick()" class="flex cursor-pointer flex-row items-center justify-between p-3 font-semibold">
                        <span>How do I track my order?</span>
                        <svg :class="handleRotate()" class="h-6 w-6 transform fill-current text-red-600 transition-transform duration-500" viewBox="0 0 20 20">
                            <path d="M13.962,8.885l-3.736,3.739c-0.086,0.086-0.201,0.13-0.314,0.13S9.686,12.71,9.6,12.624l-3.562-3.56C5.863,8.892,5.863,8.611,6.036,8.438c0.175-0.173,0.454-0.173,0.626,0l3.25,3.247l3.426-3.424c0.173-0.172,0.451-0.172,0.624,0C14.137,8.434,14.137,8.712,13.962,8.885 M18.406,10c0,4.644-3.763,8.406-8.406,8.406S1.594,14.644,1.594,10S5.356,1.594,10,1.594S18.406,5.356,18.406,10 M17.521,10c0-4.148-3.373-7.521-7.521-7.521c-4.148,0-7.521,3.374-7.521,7.521c0,4.147,3.374,7.521,7.521,7.521C14.148,17.521,17.521,14.147,17.521,10">
                            </path>
                        </svg>
                    </h2>
                    <div class="max-h-0 overflow-hidden border-l-2 border-purple-600 transition-all duration-500" x-ref="tab" :style="handleToggle()">
                        <p class="p-3 text-gray-300">
                            Once shipped, you’ll get a confirmation email that includes a tracking number and additional
                            information regarding tracking your order.
                        </p>
                    </div>
                </li>
                <li class="bg-gray-700 shadow-lg" x-data="accordion(3)">
                    <h2 @click="handleClick()" class="flex cursor-pointer flex-row items-center justify-between p-3 font-semibold">
                        <span>What’s your return policy?</span>
                        <svg :class="handleRotate()" class="h-6 w-6 transform fill-current text-red-600 transition-transform duration-500" viewBox="0 0 20 20">
                            <path d="M13.962,8.885l-3.736,3.739c-0.086,0.086-0.201,0.13-0.314,0.13S9.686,12.71,9.6,12.624l-3.562-3.56C5.863,8.892,5.863,8.611,6.036,8.438c0.175-0.173,0.454-0.173,0.626,0l3.25,3.247l3.426-3.424c0.173-0.172,0.451-0.172,0.624,0C14.137,8.434,14.137,8.712,13.962,8.885 M18.406,10c0,4.644-3.763,8.406-8.406,8.406S1.594,14.644,1.594,10S5.356,1.594,10,1.594S18.406,5.356,18.406,10 M17.521,10c0-4.148-3.373-7.521-7.521-7.521c-4.148,0-7.521,3.374-7.521,7.521c0,4.147,3.374,7.521,7.521,7.521C14.148,17.521,17.521,14.147,17.521,10">
                            </path>
                        </svg>
                    </h2>
                    <div class="max-h-0 overflow-hidden border-l-2 border-purple-600 transition-all duration-500" x-ref="tab" :style="handleToggle()">
                        <p class="p-3 text-gray-300">
                            We allow the return of all items within 30 days of your original order’s date. If you’re interested
                            in returning your items, send us an email with your order number and we’ll ship a return label.
                        </p>
                    </div>
                </li>
                <li class="bg-gray-700 shadow-lg" x-data="accordion(4)">
                    <h2 @click="handleClick()" class="flex cursor-pointer flex-row items-center justify-between p-3 font-semibold">
                        <span>How do I make changes to an existing order?</span>
                        <svg :class="handleRotate()" class="h-6 w-6 transform fill-current text-red-600 transition-transform duration-500" viewBox="0 0 20 20">
                            <path d="M13.962,8.885l-3.736,3.739c-0.086,0.086-0.201,0.13-0.314,0.13S9.686,12.71,9.6,12.624l-3.562-3.56C5.863,8.892,5.863,8.611,6.036,8.438c0.175-0.173,0.454-0.173,0.626,0l3.25,3.247l3.426-3.424c0.173-0.172,0.451-0.172,0.624,0C14.137,8.434,14.137,8.712,13.962,8.885 M18.406,10c0,4.644-3.763,8.406-8.406,8.406S1.594,14.644,1.594,10S5.356,1.594,10,1.594S18.406,5.356,18.406,10 M17.521,10c0-4.148-3.373-7.521-7.521-7.521c-4.148,0-7.521,3.374-7.521,7.521c0,4.147,3.374,7.521,7.521,7.521C14.148,17.521,17.521,14.147,17.521,10">
                            </path>
                        </svg>
                    </h2>
                    <div class="max-h-0 overflow-hidden border-l-2 border-purple-600 transition-all duration-500" x-ref="tab" :style="handleToggle()">
                        <p class="p-3 text-gray-300">
                            Changes to an existing order can be made as long as the order is still in “processing” status.
                            Please contact our team via email and we’ll make sure to apply the needed changes. If your order has
                            already been shipped, we cannot apply any changes to it. If you are unhappy with your order when it
                            arrives, please contact us for any changes you may require.
                        </p>
                    </div>
                </li>
                <li class="bg-gray-700 shadow-lg" x-data="accordion(5)">
                    <h2 @click="handleClick()" class="flex cursor-pointer flex-row items-center justify-between p-3 font-semibold">
                        <span>What shipping options do you have?</span>
                        <svg :class="handleRotate()" class="h-6 w-6 transform fill-current text-red-600 transition-transform duration-500" viewBox="0 0 20 20">
                            <path d="M13.962,8.885l-3.736,3.739c-0.086,0.086-0.201,0.13-0.314,0.13S9.686,12.71,9.6,12.624l-3.562-3.56C5.863,8.892,5.863,8.611,6.036,8.438c0.175-0.173,0.454-0.173,0.626,0l3.25,3.247l3.426-3.424c0.173-0.172,0.451-0.172,0.624,0C14.137,8.434,14.137,8.712,13.962,8.885 M18.406,10c0,4.644-3.763,8.406-8.406,8.406S1.594,14.644,1.594,10S5.356,1.594,10,1.594S18.406,5.356,18.406,10 M17.521,10c0-4.148-3.373-7.521-7.521-7.521c-4.148,0-7.521,3.374-7.521,7.521c0,4.147,3.374,7.521,7.521,7.521C14.148,17.521,17.521,14.147,17.521,10">
                            </path>
                        </svg>
                    </h2>
                    <div class="max-h-0 overflow-hidden border-l-2 border-purple-600 transition-all duration-500" x-ref="tab" :style="handleToggle()">
                        <p class="p-3 text-gray-300">
                            For USA domestic orders we offer FedEx and USPS shipping.
                        </p>
                    </div>
                </li>
                <li class="bg-gray-700 shadow-lg" x-data="accordion(6)">
                    <h2 @click="handleClick()" class="flex cursor-pointer flex-row items-center justify-between p-3 font-semibold">
                        <span>What payment methods do you accept?</span>
                        <svg :class="handleRotate()" class="h-6 w-6 transform fill-current text-red-600 transition-transform duration-500" viewBox="0 0 20 20">
                            <path d="M13.962,8.885l-3.736,3.739c-0.086,0.086-0.201,0.13-0.314,0.13S9.686,12.71,9.6,12.624l-3.562-3.56C5.863,8.892,5.863,8.611,6.036,8.438c0.175-0.173,0.454-0.173,0.626,0l3.25,3.247l3.426-3.424c0.173-0.172,0.451-0.172,0.624,0C14.137,8.434,14.137,8.712,13.962,8.885 M18.406,10c0,4.644-3.763,8.406-8.406,8.406S1.594,14.644,1.594,10S5.356,1.594,10,1.594S18.406,5.356,18.406,10 M17.521,10c0-4.148-3.373-7.521-7.521-7.521c-4.148,0-7.521,3.374-7.521,7.521c0,4.147,3.374,7.521,7.521,7.521C14.148,17.521,17.521,14.147,17.521,10">
                            </path>
                        </svg>
                    </h2>
                    <div class="max-h-0 overflow-hidden border-l-2 border-purple-600 transition-all duration-500" x-ref="tab" :style="handleToggle()">
                        <p class="p-3 text-gray-300">
                            Any method of payments acceptable by you. For example: We accept MasterCard, Visa, American Express,
                            PayPal, JCB Discover, Gift Cards, etc.
                        </p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="hidden md:block lg:block" x-data="{ activeStep: 1 }">
            <div class="flex flex-col space-y-4">
                <div class="grid grid-flow-col rounded-full bg-gray-200 p-2 text-center font-bold text-gray-700 shadow-lg shadow-zinc-50">
                    <button @click="activeStep = 1" :class="{ 'bg-red-600 text-white': activeStep === 1 }" class="rounded-full p-3 transition-colors duration-200 hover:bg-red-600 hover:text-white focus:outline-none">Jualan</button>
                    <button @click="activeStep = 2" :class="{ 'bg-red-600 text-white': activeStep === 2 }" class="rounded-full p-3 transition-colors duration-200 hover:bg-red-600 hover:text-white focus:outline-none">Belanja</button>
                    <button @click="activeStep = 3" :class="{ 'bg-red-600 text-white': activeStep === 3 }" class="rounded-full p-3 transition-colors duration-200 hover:bg-red-600 hover:text-white focus:outline-none">Laporan</button>
                    <button @click="activeStep = 4" :class="{ 'bg-red-600 text-white': activeStep === 4 }" class="rounded-full p-3 transition-colors duration-200 hover:bg-red-600 hover:text-white focus:outline-none">Analisis</button>
                    <button @click="activeStep = 5" :class="{ 'bg-red-600 text-white': activeStep === 5 }" class="rounded-full p-3 transition-colors duration-200 hover:bg-red-600 hover:text-white focus:outline-none">Informasi</button>
                    <button @click="activeStep = 6" :class="{ 'bg-red-600 text-white': activeStep === 6 }" class="rounded-full p-3 transition-colors duration-200 hover:bg-red-600 hover:text-white focus:outline-none">Komunitas</button>
                </div>

                {{-- Step 1 --}}
                <div x-show="activeStep === 1" class="py-3">
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Bagian Gambar (1/3) -->
                        <div>
                            <img src="{{ url('/storage/assets_images/images/carousel/bg1.png') }}" alt="Video Thumbnail" class="animate__animated animate__fadeIn animate__delay-1s h-[35svh] w-full rounded-lg object-cover shadow-lg">
                        </div>
                        <div class="col-span-2 flex flex-col">
                            <h2 class="animate__fadeInUp animate__animated mb-4 text-2xl font-bold text-gray-800">
                                Jualan di Kedai Indonesia kini lebih canggih! Fitur lengkap kami memudahkan anda dalam mengelola dan memulai bisnis.
                            </h2>
                            <ul class="space-y-3">
                                <li class="animate__animated animate__fadeInLeft animate__faster text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Fitur scanner infra merah dan printer struk
                                </li>
                                <li class="animate__animated animate__fadeInLeft animate__fast text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Identifikasi produk melalui barcode
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Multi kasir yang dapat mempercepat penjualan
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Laporan barang expired
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div x-show="activeStep === 2" class="py-3">
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Bagian Gambar (1/3) -->
                        <div>
                            <img src="{{ url('/storage/assets_images/images/carousel/bg1.png') }}" alt="Video Thumbnail" class="animate__animated animate__fadeIn animate__delay-1s h-[35svh] w-full rounded-lg object-cover shadow-lg">
                        </div>
                        <div class="col-span-2 flex flex-col">
                            <h2 class="animate__fadeInUp animate__animated mb-4 text-2xl font-bold text-gray-800">
                                Nikmati kemudahan dalam memilih produk dan bertransaksi dengan berbagai fitur yang memudahkan.
                            </h2>
                            <ul class="space-y-3">
                                <li class="animate__animated animate__fadeInLeft animate__faster text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Notifikasi Belanja Real-Time
                                </li>
                                <li class="animate__animated animate__fadeInLeft animate__fast text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Pencarian Produk yang Cepat dan Mudah
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Pembayaran Aman dan Cepat
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Diskon dan Penawaran Khusus untuk Pelanggan Setia
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div x-show="activeStep === 3" class="py-3">
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Bagian Gambar (1/3) -->
                        <div>
                            <img src="{{ url('/storage/assets_images/images/carousel/bg1.png') }}" alt="Video Thumbnail" class="animate__animated animate__fadeIn animate__delay-1s h-[35svh] w-full rounded-lg object-cover shadow-lg">
                        </div>
                        <div class="col-span-2 flex flex-col">
                            <h2 class="animate__fadeInUp animate__animated mb-4 text-2xl font-bold text-gray-800">
                                Anda dapat menghasilkan laporan yang terperinci dan membantu pengambilan keputusan yang lebih baik untuk bisnis Anda.
                            </h2>
                            <ul class="space-y-3">
                                <li class="animate__animated animate__fadeInLeft animate__faster text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Laporan Penjualan yang Akurat
                                </li>
                                <li class="animate__animated animate__fadeInLeft animate__fast text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Laporan Stok Barang yang Real-Time
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Laporan Pembayaran dan Transaksi
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Laporan Kinerja Kasir
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div x-show="activeStep === 4" class="py-3">
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Bagian Gambar (1/3) -->
                        <div>
                            <img src="{{ url('/storage/assets_images/images/carousel/bg1.png') }}" alt="Video Thumbnail" class="animate__animated animate__fadeIn animate__delay-1s h-[35svh] w-full rounded-lg object-cover shadow-lg">
                        </div>
                        <div class="col-span-2 flex flex-col">
                            <h2 class="animate__fadeInUp animate__animated mb-4 text-2xl font-bold text-gray-800">
                                Dengan informasi yang tepat, Anda dapat merencanakan langkah selanjutnya dengan lebih baik.
                            </h2>
                            <ul class="space-y-3">
                                <li class="animate__animated animate__fadeInLeft animate__faster text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Analisis Pembelian dan Preferensi Pelanggan
                                </li>
                                <li class="animate__animated animate__fadeInLeft animate__fast text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Analisis Performa Penjualan per Kategori
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Perbandingan Penjualan Bulanan dan Tahunan
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Data Analitik untuk Pengambilan Keputusan
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

                {{-- Step 5 --}}
                <div x-show="activeStep === 5" class="py-3">
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Bagian Gambar (1/3) -->
                        <div>
                            <img src="{{ url('/storage/assets_images/images/carousel/bg1.png') }}" alt="Video Thumbnail" class="animate__animated animate__fadeIn animate__delay-1s h-[35svh] w-full rounded-lg object-cover shadow-lg">
                        </div>
                        <div class="col-span-2 flex flex-col">
                            <h2 class="animate__fadeInUp animate__animated mb-4 text-2xl font-bold text-gray-800">
                                Kedai Indonesia menyediakan berbagai sumber informasi yang relevan untuk membantu Anda berkembang.
                            </h2>
                            <ul class="space-y-3">
                                <li class="animate__animated animate__fadeInLeft animate__faster text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Panduan Penggunaan Sistem dengan Lengkap
                                </li>
                                <li class="animate__animated animate__fadeInLeft animate__fast text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Berita dan Update Terkini
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Tips Bisnis untuk Pengusaha Pemula
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Pemberitahuan Diskon dan Penawaran Terbaik
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

                {{-- Step 6 --}}
                <div x-show="activeStep === 6" class="py-3">
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Bagian Gambar (1/3) -->
                        <div>
                            <img src="{{ url('/storage/assets_images/images/carousel/bg1.png') }}" alt="Video Thumbnail" class="animate__animated animate__fadeIn animate__delay-1s h-[35svh] w-full rounded-lg object-cover shadow-lg">
                        </div>
                        <div class="col-span-2 flex flex-col">
                            <h2 class="animate__fadeInUp animate__animated mb-4 text-2xl font-bold text-gray-800">
                                Kedai Indonesia menyediakan platform bagi para pengusaha untuk berbagi pengalaman dan belajar satu sama lain.
                            </h2>
                            <ul class="space-y-3">
                                <li class="animate__animated animate__fadeInLeft animate__faster text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Diskusi dan Forum Pengusaha
                                </li>
                                <li class="animate__animated animate__fadeInLeft animate__fast text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Acara dan Webinar Bisnis
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Grup Diskusi untuk Produk Baru
                                </li>
                                <li class="animate__animated animate__fadeInLeft text-base">
                                    <i class="fas fa-check-circle mr-2 text-green-400"></i>Rekomendasi Produk dan Layanan
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('accordion', {
            tab: 0
        });

        Alpine.data('accordion', (idx) => ({
            init() {
                this.idx = idx;
            },
            idx: -1,
            handleClick() {
                this.$store.accordion.tab = this.$store.accordion.tab === this.idx ? 0 : this.idx;
            },
            handleRotate() {
                return this.$store.accordion.tab === this.idx ? 'rotate-180' : '';
            },
            handleToggle() {
                return this.$store.accordion.tab === this.idx ?
                    `max-height: ${this.$refs.tab.scrollHeight}px` : '';
            }
        }));
    })
</script>
