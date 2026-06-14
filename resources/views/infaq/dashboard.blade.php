@extends('layouts.admin')
@section('nav_title', 'Dashboard Infaq')
@section('page_title', 'Dashboard Infaq')

@section('content')
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <div class="mb-6 flex flex-col items-start justify-between md:flex-row md:items-center">
            <h1 class="text-3xl font-bold text-gray-800">Ringkasan Infaq</h1>
            <form action="{{ route('infaq.dashboard') }}" method="GET" class="mt-4 flex w-full flex-col items-end space-y-2 md:mt-0 md:w-auto md:flex-row md:space-x-4 md:space-y-0">
                <div class="w-full flex-1 md:w-auto">
                    <label for="start_date" class="mb-1 block text-sm font-medium text-gray-700">Dari</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-lg border-2 border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="w-full flex-1 md:w-auto">
                    <label for="end_date" class="mb-1 block text-sm font-medium text-gray-700">Sampai</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-lg border-2 border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit" class="w-full rounded-lg bg-blue-600 px-6 py-2.5 font-semibold text-white shadow-md transition-colors duration-200 hover:bg-blue-700 md:w-auto">
                    Filter
                </button>
            </form>
        </div>
        <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Card Total Infaq --}}
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Infaq</p>
                        <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h3>
                        <p class="@if ($totalAmountPercentageChange >= 0) text-green-500 @else text-red-500 @endif mt-1 flex items-center text-xs">
                            @if ($totalAmountPercentageChange > 0)
                                <i class="fas fa-arrow-up mr-1"></i>
                            @elseif($totalAmountPercentageChange < 0)
                                <i class="fas fa-arrow-down mr-1"></i>
                            @endif
                            {{ number_format(abs($totalAmountPercentageChange), 0) }}% dari periode lalu
                        </p>
                    </div>
                    <div class="flex items-center justify-center rounded-full bg-blue-100 p-3">
                        <i class="fa-solid fa-rupiah-sign text-xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Card Infaq Tercapai --}}
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Infaq Tercapai</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $infaqTercapai }}</h3>
                        <p class="mt-1 flex items-center text-xs text-blue-500">
                            @if ($infaqTercapai > 0)
                                {{ number_format(($infaqTercapai / ($infaqTercapai + $infaqBelumTercapai)) * 100, 0) }}% dari total infaq
                            @else
                                Belum ada infaq yang tercapai
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center justify-center rounded-full bg-green-100 p-3">
                        <i class="fa-solid fa-circle-check text-xl text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Card Infaq Aktif --}}
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Infaq Aktif</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ number_format($activeInfaqCount) }}</h3>
                        <p class="mt-1 flex items-center text-xs text-gray-500">
                            Tidak ada perubahan
                        </p>
                    </div>
                    <div class="flex items-center justify-center rounded-full bg-orange-100 p-3">
                        <i class="fa-solid fa-hand-holding-dollar text-xl text-orange-600"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Rata-rata Infaq</p>
                        <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($averageAmount, 0, ',', '.') }}</h3>
                        <p class="@if ($averageAmountPercentageChange >= 0) text-green-500 @else text-red-500 @endif mt-1 flex items-center text-xs">
                            @if ($averageAmountPercentageChange > 0)
                                <i class="fas fa-arrow-up mr-1"></i>
                            @elseif($averageAmountPercentageChange < 0)
                                <i class="fas fa-arrow-down mr-1"></i>
                            @endif
                            {{ number_format(abs($averageAmountPercentageChange), 0) }}% dari periode lalu
                        </p>
                    </div>
                    <div class="flex items-center justify-center rounded-full bg-purple-100 p-3">
                        <i class="fa-solid fa-chart-line text-xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl bg-white p-6 shadow-lg transition-shadow duration-300 hover:shadow-xl">
                <h2 class="mb-4 text-xl font-bold text-gray-800">Infaq per Kategori</h2>
                <div class="relative h-64">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            <div class="rounded-xl bg-white p-6 shadow-lg transition-shadow duration-300 hover:shadow-xl">
                <h2 class="mb-4 text-xl font-bold text-gray-800">Infaq per Metode Pembayaran</h2>
                <div class="relative h-64">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <div class="rounded-xl bg-white p-6 shadow-lg transition-shadow duration-300 hover:shadow-xl">
                <h2 class="mb-4 text-xl font-bold text-gray-800">Progress Infaq</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama Infaq</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Target</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Terkumpul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($infaqProgress as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center whitespace-nowrap">
                                            <img src="{{ $item['image_url'] ?? 'https://placehold.co/400x400?text=' . $item['initials'] }}" alt="Image" class="h-10 w-10 rounded-sm object-cover">
                                            <div class="ml-3">
                                                <p class="font-medium text-gray-900">{{ $item['name'] }}</p>
                                                <p class="text-xs text-gray-500">({{ ucfirst($item['category']) ?? 'N/A' }})</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">Rp {{ number_format($item['dana_dibutuhkan'], 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">Rp {{ number_format($item['total_donations_sum'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            // Mengakses data dari array
                                            $progress = $item['dana_dibutuhkan'] > 0 ? ($item['total_donations_sum'] / $item['dana_dibutuhkan']) * 100 : 0;
                                            $progressColor = 'bg-blue-600';
                                            if ($progress >= 100) {
                                                $progressColor = 'bg-green-600';
                                            }
                                            $progress = min($progress, 100);
                                        @endphp
                                        <div class="relative pt-1">
                                            <div class="mb-2 flex h-2 overflow-hidden rounded-full bg-blue-200 text-xs">
                                                <div style="width:{{ $progress }}%" class="{{ $progressColor }} flex flex-col justify-center whitespace-nowrap rounded-full text-center text-white shadow-none transition-all duration-500"></div>
                                            </div>
                                            <div class="text-xs text-gray-500">{{ round($progress, 2) }}%</div>
                                        </div>
                                    </td>
                                    <td class="inline-flex items-center space-x-2 px-6 py-4">
                                        <button data-id="{{ $item['id'] }}" tooltip title="View Data" data-name="{{ $item['name'] }}" class="view-history-btn inline-flex h-8 w-8 items-center justify-center rounded bg-blue-100 p-1 text-blue-600 transition-all hover:scale-125 hover:bg-blue-200 hover:text-blue-600">
                                            <i class="fad fa-eye"></i>
                                        </button>
                                        <button type="button" data-id="{{ $item['id'] }}" tooltip title="Edit Data" data-name="{{ $item['name'] }}" class="edit-btn inline-flex h-8 w-8 items-center justify-center rounded bg-yellow-100 p-1 text-yellow-600 transition-all hover:scale-125 hover:bg-yellow-200 hover:text-yellow-600">
                                            <i class="fad fa-pen"></i>
                                        </button>
                                        <button type="button" tooltip title="Delete Data" data-id="{{ $item['id'] }}" class="delete-btn inline-flex h-8 w-8 items-center justify-center rounded bg-red-100 p-1 text-red-600 transition-all hover:scale-125 hover:bg-red-200 hover:text-red-600">
                                            <i class="fad fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data infaq aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="donationHistoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-4xl rounded-xl bg-white p-6 shadow-2xl">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h3 class="text-2xl font-semibold text-gray-800" id="modalTitle">Riwayat Infaq</h3>
                        <button id="closeModalBtn" class="rounded-full p-2 text-gray-500 transition-colors duration-200 hover:bg-gray-200 hover:text-gray-800 focus:outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="mt-4 max-h-[70vh] overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="sticky top-0 bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Donatur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Toko</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Metode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Catatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody id="donationsTableBody" class="divide-y divide-gray-200 bg-white">
                            </tbody>
                        </table>
                        <div id="loadingIndicator" class="mt-4 hidden text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Chart.js script
        const categoryData = @json($donationsByCategory);
        const categoryLabels = categoryData.map(item => item.category);
        const categoryTotals = categoryData.map(item => item.total);

        const paymentData = @json($donationsByPaymentMethod);
        const paymentLabels = paymentData.map(item => item.label);
        const paymentTotals = paymentData.map(item => item.total);

        const generateColors = (num) => {
            const colors = [];
            for (let i = 0; i < num; i++) {
                const hue = (i * 137.508) % 360;
                colors.push(`hsl(${hue}, 70%, 50%)`);
            }
            return colors;
        };

        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryTotals,
                    backgroundColor: generateColors(categoryLabels.length),
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': Rp ' + tooltipItem.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
        const paymentChart = new Chart(paymentCtx, {
            type: 'pie',
            data: {
                labels: paymentLabels,
                datasets: [{
                    data: paymentTotals,
                    backgroundColor: generateColors(paymentLabels.length),
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': Rp ' + tooltipItem.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Modal script
        const modal = document.getElementById('donationHistoryModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const donationsTableBody = document.getElementById('donationsTableBody');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const modalTitle = document.getElementById('modalTitle');

        document.querySelectorAll('.view-history-btn').forEach(button => {
            button.addEventListener('click', function() {
                const infaqId = this.getAttribute('data-id');
                const infaqName = this.getAttribute('data-name');
                modal.classList.remove('hidden');
                modalTitle.textContent = `Riwayat Infaq untuk "${infaqName}"`;
                loadingIndicator.classList.remove('hidden');
                donationsTableBody.innerHTML = '';

                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;

                fetch(`/infaq/${infaqId}/donations?start_date=${startDate}&end_date=${endDate}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingIndicator.classList.add('hidden');
                        if (data.length > 0) {
                            data.forEach(donation => {
                                const row = document.createElement('tr');
                                row.classList.add('hover:bg-gray-50');
                                row.innerHTML = `
                                    <td class="whitespace-nowrap px-6 py-4">${donation.donor_name}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${donation.toko_name}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${donation.formatted_amount}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${donation.payment_method_label}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${donation.status}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${donation.note}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${donation.date}</td>
                                `;
                                donationsTableBody.appendChild(row);
                            });
                        } else {
                            const row = document.createElement('tr');
                            row.innerHTML = `<td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat infaq dalam periode ini.</td>`;
                            donationsTableBody.appendChild(row);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        loadingIndicator.classList.add('hidden');
                        const row = document.createElement('tr');
                        row.innerHTML = `<td colspan="4" class="px-6 py-4 text-center text-red-500">Gagal memuat data. Silakan coba lagi.</td>`;
                        donationsTableBody.appendChild(row);
                    });
            });
        });

        closeModalBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    </script>
@endpush
