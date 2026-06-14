@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('page_title', 'Analytics Dashboard')

@section('content')
    <div class="container mx-auto py-6">
        <!-- Header -->
        <div class="mb-8 flex flex-col items-center justify-between md:flex-row">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Sales Analytics Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500" id="dateRangeDisplay">May 14, 2025 - May 20, 2025</p>
            </div>

            <!-- Filter Form -->
            <div class="mt-4 w-full md:mt-0 md:w-auto">
                <form id="filterForm" class="flex flex-col gap-3 md:flex-row">
                    <div>
                        <select id="period"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="days">Daily</option>
                            <option value="weeks">Weekly</option>
                            <option value="months">Monthly</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div id="customDateStart" class="hidden">
                        <input type="date" id="startDate"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div id="customDateEnd" class="hidden">
                        <input type="date" id="endDate"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button id="applyButton" type="submit"
                        class="rounded-lg bg-red-600 px-6 py-2 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Apply
                    </button>
                </form>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl bg-white p-6 shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Sales</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-800" id="totalSales">Rp 3,001,687</h3>
                    </div>
                    <div class="rounded-lg bg-blue-100 p-3">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                </div>
                <div class="mt-4" id="salesGrowth">
                    <span class="text-sm font-medium text-green-500">
                        <i class="fas fa-arrow-up mr-1"></i> 100%
                    </span>
                    <span class="ml-1 text-sm text-gray-500">vs previous period</span>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Best Sales Day</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-800" id="bestSalesDay">May 17, 2025</h3>
                    </div>
                    <div class="rounded-lg bg-green-100 p-3">
                        <i class="fas fa-calendar-check text-green-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-gray-800" id="bestSalesDayAmount">
                        Rp 1,044,401
                    </span>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Top Product</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-800" id="topProduct">Mili</h3>
                    </div>
                    <div class="rounded-lg bg-purple-100 p-3">
                        <i class="fas fa-box text-purple-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-gray-800" id="topProductQuantity">
                        1,204 units sold
                    </span>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Low Stock Alert</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-800" id="lowStockCount">2</h3>
                    </div>
                    <div class="rounded-lg bg-red-100 p-3">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-red-500">
                        Items require restocking
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div id="SalesTrend" class="rounded-xl bg-white p-6 shadow-md">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Daily Sales Trend</h3>
                <div class="loading-trend flex h-64 items-center justify-center">
                    <div class="h-12 w-12 animate-spin rounded-full border-b-2 border-gray-900"></div>
                </div>
                <div class="isi-chart hidden h-64">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>

            <div id="TopProducts" class="rounded-xl bg-white p-6 shadow-md">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Top Products by Quantity</h3>
                <div class="loading-trend flex h-64 items-center justify-center">
                    <div class="h-12 w-12 animate-spin rounded-full border-b-2 border-gray-900"></div>
                </div>
                <div class="isi-chart hidden h-64">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div id="ProductRevenue" class="rounded-xl bg-white p-6 shadow-md">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Product Revenue Comparison</h3>
                <div class="loading-trend flex h-64 items-center justify-center">
                    <div class="h-12 w-12 animate-spin rounded-full border-b-2 border-gray-900"></div>
                </div>
                <div class="isi-chart hidden h-64">
                    <canvas id="productRevenueChart"></canvas>
                </div>
            </div>

            <div id="InventoryStatus" class="rounded-xl bg-white p-6 shadow-md">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Inventory Status</h3>
                <div class="loading-trend flex h-64 items-center justify-center">
                    <div class="h-12 w-12 animate-spin rounded-full border-b-2 border-gray-900"></div>
                </div>
                <div class="isi-chart hidden h-64">
                    <canvas id="inventoryStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->

        <!-- Tables -->
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div id="tableTopSellingProducts" class="rounded-xl bg-white p-6 shadow-md">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Top Selling Products</h3>
                    <div class="loading-table"><i class="fas fa-spinner fa-spin"></i></div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-3 text-left text-sm font-medium text-gray-500">Product</th>
                                <th class="py-3 text-center text-sm font-medium text-gray-500">Qty Sold</th>
                                <th class="py-3 text-right text-sm font-medium text-gray-500">Revenue</th>
                                <th class="py-3 text-center text-sm font-medium text-gray-500">Sales Trend</th>
                            </tr>
                        </thead>
                        <tbody id="bestSellingTable">
                            <!-- To be filled by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tableLowSellingProducts" class="rounded-xl bg-white p-6 shadow-md">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Low Selling Products</h3>
                    <div class="loading-table"><i class="fas fa-spinner fa-spin"></i></div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-3 text-left text-sm font-medium text-gray-500">Product</th>
                                <th class="py-3 text-center text-sm font-medium text-gray-500">Qty Sold</th>
                                <th class="py-3 text-right text-sm font-medium text-gray-500">Revenue</th>
                                <th class="py-3 text-center text-sm font-medium text-gray-500">Sales Trend</th>
                            </tr>
                        </thead>
                        <tbody id="lowSellingTable">
                            <!-- To be filled by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="tableInventoryAlerts" class="rounded-xl bg-white p-6 shadow-md">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Inventory Alerts</h3>
                <div class="loading-table"><i class="fas fa-spinner fa-spin"></i></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 text-left text-sm font-medium text-gray-500">Product</th>
                            <th class="py-3 text-center text-sm font-medium text-gray-500">Remaining</th>
                            <th class="py-3 text-right text-sm font-medium text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTable">
                        <!-- To be filled by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Chart.js Global Configuration
        Chart.defaults.font.family = "'Helvetica', 'Arial', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = "#64748b";

        // Global Chart Instances
        let salesTrendChart, topProductsChart, productRevenueChart, inventoryStatusChart;
        // Store product mini charts
        let productMiniCharts = {};

        // Function to show/hide loading table
        function toggleLoadingTable(show) {
            const loadingTableBestBuy = document.querySelector(`#SalesTrend .loading-trend`);
            const loadingTableBestSell = document.querySelector(`#TopProducts .loading-trend`);
            const loadingTableTopSelling = document.querySelector(`#ProductRevenue .loading-trend`);
            const loadingTableLowSelling = document.querySelector(`#InventoryStatus .loading-trend`);
            const loadingTableBestBuyChart = document.querySelector(`#tableTopSellingProducts .loading-table`);
            const loadingTableLowSellingChart = document.querySelector(`#tableLowSellingProducts .loading-table`);
            const loadingTableInventoryAlerts = document.querySelector(`#tableInventoryAlerts .loading-table`);
            const loadingTableBestSellChart = document.querySelector(`#SalesTrend .isi-chart`);
            const loadingTableTopSellingChart = document.querySelector(`#TopProducts .isi-chart`);
            const loadingTableProductRevenueChart = document.querySelector(`#ProductRevenue .isi-chart`);
            const loadingTableInventoryStatusChart = document.querySelector(`#InventoryStatus .isi-chart`);
            if (show) {
                loadingTableBestBuy.classList.remove('hidden');
                loadingTableBestSell.classList.remove('hidden');
                loadingTableTopSelling.classList.remove('hidden');
                loadingTableLowSelling.classList.remove('hidden');
                loadingTableBestBuyChart.classList.remove('hidden');
                loadingTableLowSellingChart.classList.remove('hidden');
                loadingTableInventoryAlerts.classList.remove('hidden');
                loadingTableBestSellChart.classList.add('hidden');
                loadingTableTopSellingChart.classList.add('hidden');
                loadingTableProductRevenueChart.classList.add('hidden');
                loadingTableInventoryStatusChart.classList.add('hidden');
            } else {
                loadingTableBestBuy.classList.add('hidden');
                loadingTableBestSell.classList.add('hidden');
                loadingTableTopSelling.classList.add('hidden');
                loadingTableLowSelling.classList.add('hidden');
                loadingTableBestBuyChart.classList.add('hidden');
                loadingTableLowSellingChart.classList.add('hidden');
                loadingTableInventoryAlerts.classList.add('hidden');
                loadingTableBestSellChart.classList.remove('hidden');
                loadingTableTopSellingChart.classList.remove('hidden');
                loadingTableProductRevenueChart.classList.remove('hidden');
                loadingTableInventoryStatusChart.classList.remove('hidden');
            }
        }
        $(document).ready(function() {
            // Then fetch actual data
            fetchData();

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                $('#applyButton').prop('disabled', true);
                $('#applyButton').html('<span class="loading loading-spinner loading-xs"></span> Applying');

                setTimeout(fetchData, 500);
            });

            $('#period').on('change', function() {
                const isCustom = $(this).val() === 'custom';
                $('#customDateStart').toggle(isCustom);
                $('#customDateEnd').toggle(isCustom);
            });
        });

        // Main function to fetch data from API
        function fetchData() {
            // Get filter values
            const period = $('#period').val();
            let startDate = null;
            let endDate = null;

            if (period === 'custom') {
                startDate = $('#startDate').val();
                endDate = $('#endDate').val();

                if (!startDate || !endDate) {
                    alert('Please select both start and end dates');
                    return;
                }
            }

            $.ajax({
                url: '/dashboard/api/analytics',
                method: 'GET',
                data: {
                    period,
                    start_date: startDate,
                    end_date: endDate
                },
                beforeSend: function() {
                    toggleLoadingTable(true);
                },
                success: function(response) {

                    // Process data
                    processAnalyticsData(response);
                    console.log(response);

                    // Re-enable the apply button
                    $('#applyButton').prop('disabled', false);
                    $('#applyButton').html('Apply');

                    // Hide loading table
                    toggleLoadingTable(false);
                },
                error: function() {
                    alert('Failed to load data. Please try again later.');

                    // Re-enable the apply button
                    $('#applyButton').prop('disabled', false);
                    $('#applyButton').html('Apply');

                    // Hide loading table
                    toggleLoadingTable(false);
                }
            });
        }


        // Function to process and display analytics data
        function processAnalyticsData(data) {
            // Update KPI Cards
            updateKPICards(data);

            // Update Charts
            updateSalesTrendChart(data.sales_by_day);
            updateTopProductsChart(data.best_selling_products);
            updateProductRevenueChart(data.best_selling_products);
            updateInventoryStatusChart(data.inventory_status);

            // Update Tables
            updateBestSellingTable(data.best_selling_products);
            updateLowSellingTable(data.worst_selling_products);
            updateInventoryTable(data.inventory_status);

            // Update date range display
            $('#dateRangeDisplay').text(`${formatDate(data.meta.start_date)} - ${formatDate(data.meta.end_date)}`);
        }

        // Helper Functions
        function formatDate(dateStr) {
            return moment(dateStr).format('MMM D, YYYY');
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        function formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        }

        // Update KPI Cards
        function updateKPICards(data) {
            $('#totalSales').text(formatCurrency(data.total_sales));

            // Find best sales day
            const bestDay = data.sales_by_day.reduce((max, day) =>
                max.total_sales > day.total_sales ? max : day
            );

            $('#bestSalesDay').text(formatDate(bestDay.date));
            $('#bestSalesDayAmount').text(formatCurrency(bestDay.total_sales));

            // Find top product by quantity
            const topProduct = data.best_selling_products[0]; // Already sorted in data
            $('#topProduct').text(topProduct.name);
            $('#topProductQuantity').text(`${formatNumber(topProduct.total_quantity)} ${topProduct.unit} sold`);

            // Count low stock items
            const lowStockItems = data.inventory_status.filter(item => item.stock_level === "Low").length;
            $('#lowStockCount').text(lowStockItems);

            // Sales growth
            if (data.sales_growth) {
                const growthElement = $('#salesGrowth');
                growthElement.empty();

                const growthIcon = data.sales_growth.is_positive ?
                    '<i class="fas fa-arrow-up mr-1"></i>' :
                    '<i class="fas fa-arrow-down mr-1"></i>';

                const growthColor = data.sales_growth.is_positive ? 'text-green-500' : 'text-red-500';

                growthElement.html(`
                <span class="${growthColor} text-sm font-medium">
                    ${growthIcon} ${data.sales_growth.growth_percentage}%
                </span>
                <span class="ml-1 text-sm text-gray-500">vs previous period</span>
            `);
            }
        }

        // Update Sales Trend Chart
        function updateSalesTrendChart(salesByDay) {
            const ctx = document.getElementById('salesTrendChart').getContext('2d');

            const labels = salesByDay.map(day => formatDate(day.date));
            const data = salesByDay.map(day => day.total_sales);

            if (salesTrendChart) {
                salesTrendChart.destroy();
            }

            salesTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Sales',
                        data: data,
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return formatCurrency(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value).split(',')[0];
                                }
                            }
                        }
                    }
                }
            });
        }

        // Update Top Products Chart
        function updateTopProductsChart(products) {
            const ctx = document.getElementById('topProductsChart').getContext('2d');

            // Take top 5 products
            const topProducts = products.slice(0, 5);
            const labels = topProducts.map(product => product.name);
            const data = topProducts.map(product => product.total_quantity);

            if (topProductsChart) {
                topProductsChart.destroy();
            }

            // Generate colors for bars
            const colors = [
                'rgba(59, 130, 246, 0.8)', // Blue
                'rgba(16, 185, 129, 0.8)', // Green
                'rgba(139, 92, 246, 0.8)', // Purple
                'rgba(249, 115, 22, 0.8)', // Orange
                'rgba(236, 72, 153, 0.8)' // Pink
            ];

            topProductsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Units Sold',
                        data: data,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        }
                    }
                }
            });
        }

        // Update Product Revenue Chart
        function updateProductRevenueChart(products) {
            const ctx = document.getElementById('productRevenueChart').getContext('2d');

            // Take top 5 products by revenue
            const topProducts = [...products]
                .sort((a, b) => b.total_sales - a.total_sales)
                .slice(0, 5);

            const labels = topProducts.map(product => product.name);
            const data = topProducts.map(product => product.total_sales);

            if (productRevenueChart) {
                productRevenueChart.destroy();
            }

            productRevenueChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)', // Blue
                            'rgba(16, 185, 129, 0.8)', // Green
                            'rgba(139, 92, 246, 0.8)', // Purple
                            'rgba(249, 115, 22, 0.8)', // Orange
                            'rgba(236, 72, 153, 0.8)' // Pink
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${formatCurrency(context.raw)}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Update Inventory Status Chart
        function updateInventoryStatusChart(inventoryData) {
            const ctx = document.getElementById('inventoryStatusChart').getContext('2d');

            // Count items by stock level
            const stockLevels = {
                'Low': 0,
                'Medium': 0,
                'High': 0
            };

            inventoryData.forEach(item => {
                stockLevels[item.stock_level]++;
            });

            if (inventoryStatusChart) {
                inventoryStatusChart.destroy();
            }

            inventoryStatusChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Low Stock', 'Medium Stock', 'High Stock'],
                    datasets: [{
                        data: [stockLevels.Low, stockLevels.Medium, stockLevels.High],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)', // Red
                            'rgba(245, 158, 11, 0.8)', // Amber
                            'rgba(16, 185, 129, 0.8)' // Green
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12
                        }
                    }
                }
            });
        }

        // Create a mini line chart for product time series
        function createProductMiniChart(canvasId, timeSeriesData, isQuantity = true) {
            // Clean up if chart already exists
            if (productMiniCharts[canvasId]) {
                productMiniCharts[canvasId].destroy();
            }

            const ctx = document.getElementById(canvasId).getContext('2d');

            // Sort data by period to ensure chronological order
            const sortedData = [...timeSeriesData].sort((a, b) =>
                new Date(a.period) - new Date(b.period)
            );

            const labels = sortedData.map(item => formatDate(item.period));
            const data = sortedData.map(item => isQuantity ? item.sales : item.quantity);

            // Determine color based on trend
            let color = 'rgba(59, 130, 246, 1)'; // default blue
            if (data.length > 1) {
                // Calculate trend direction (positive or negative)
                const firstValue = data[0];
                const lastValue = data[data.length - 1];
                color = lastValue >= firstValue ? 'rgba(16, 185, 129, 1)' : 'rgba(239, 68, 68, 1)';
            }

            productMiniCharts[canvasId] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        borderColor: color,
                        backgroundColor: color.replace('1)', '0.1)'),
                        borderWidth: 2,
                        pointRadius: 1,
                        pointHoverRadius: 3,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(17, 24, 39, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 8,
                            displayColors: false,
                            callbacks: {
                                title: function(tooltipItems) {
                                    return labels[tooltipItems[0].dataIndex];
                                },
                                label: function(context) {
                                    const value = context.raw;
                                    return isQuantity ?
                                        `${formatNumber(value)} units` :
                                        formatCurrency(value);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true
                        }
                    }
                }
            });

            return productMiniCharts[canvasId];
        }

        // Update Best Selling Products Table
        function updateBestSellingTable(products) {
            const tableBody = $('#bestSellingTable');
            tableBody.empty();

            // Destroy existing mini charts
            Object.keys(productMiniCharts).forEach(key => {
                if (productMiniCharts[key]) {
                    productMiniCharts[key].destroy();
                    delete productMiniCharts[key];
                }
            });

            // Display top 5 products
            products.slice(0, 5).forEach((product, index) => {
                const chartId = `best-product-chart-${index}`;

                tableBody.append(`
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 font-medium text-gray-800">${product.name}</td>
                    <td class="py-3 text-center text-gray-600">${formatNumber(product.total_quantity)} ${product.unit}</td>
                    <td class="py-3 text-right font-medium text-gray-800">${formatCurrency(product.total_sales)}</td>
                    <td class="py-3">
                        <div class="flex items-center justify-center">
                            <div class="h-12 w-36">
                                <canvas id="${chartId}"></canvas>
                            </div>
                        </div>
                    </td>
                </tr>
                `);

                // Create mini chart after the canvas element is in the DOM
                setTimeout(() => {
                    if (product.time_series && product.time_series.length > 0) {
                        createProductMiniChart(chartId, product.time_series, true);
                    }
                }, 0);
            });
        }

        function updateLowSellingTable(products) {
            const tableBody = $('#lowSellingTable');
            tableBody.empty();

            // Destroy existing mini charts
            Object.keys(productMiniCharts).forEach(key => {
                if (productMiniCharts[key]) {
                    productMiniCharts[key].destroy();
                    delete productMiniCharts[key];
                }
            });

            // Display top 5 products
            products.slice(0, 5).forEach((product, index) => {
                const chartId = `low-product-chart-${index}`;

                tableBody.append(`
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 font-medium text-gray-800">${product.name}</td>
                    <td class="py-3 text-center text-gray-600">${formatNumber(product.total_quantity)} ${product.unit}</td>
                    <td class="py-3 text-right font-medium text-gray-800">${formatCurrency(product.total_sales)}</td>
                    <td class="py-3">
                        <div class="flex items-center justify-center">
                            <div class="h-12 w-36">
                                <canvas id="${chartId}"></canvas>
                            </div>
                        </div>
                    </td>
                </tr>
                `);

                // Create mini chart after the canvas element is in the DOM
                setTimeout(() => {
                    if (product.time_series && product.time_series.length > 0) {
                        createProductMiniChart(chartId, product.time_series, true);
                    }
                }, 0);
            });
        }

        // Update Inventory Table
        function updateInventoryTable(inventoryData) {
            const tableBody = $('#inventoryTable');
            tableBody.empty();

            // Sort by stock level (Low first)
            const sortedInventory = [...inventoryData].sort((a, b) => {
                const levels = {
                    'Low': 0,
                    'Medium': 1,
                    'High': 2
                };
                return levels[a.stock_level] - levels[b.stock_level];
            });

            // Display top 5 items
            sortedInventory.slice(0, 5).forEach(item => {
                let statusClass = 'text-green-600 bg-green-100';

                if (item.stock_level === 'Low') {
                    statusClass = 'text-red-600 bg-red-100';
                } else if (item.stock_level === 'Medium') {
                    statusClass = 'text-amber-600 bg-amber-100';
                }

                tableBody.append(`
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 font-medium text-gray-800">${item.product_name}</td>
                    <td class="py-3 text-center text-gray-600">${formatNumber(item.remaining_quantity)} ${item.unit}</td>
                    <td class="py-3 text-right">
                        <span class="${statusClass} rounded-full px-2 py-1 text-xs font-medium">
                            ${item.stock_level}
                        </span>
                    </td>
                </tr>
            `);
            });
        }
    </script>
@endpush
