document.addEventListener('DOMContentLoaded', function() {
  
    let currentPeriod = document.getElementById('periode-selector').value;
    
    // Initialize charts as global variables so we can update them
    let ioChart = null;
    let trendChart = null;
    
    // Load initial data
    loadAnalyticsData(currentPeriod);
    loadMostSellingData(currentPeriod);
    loadSalesTrendData(currentPeriod);
    
    // Event listeners
    document.getElementById('periode-selector').addEventListener('change', function() {
        currentPeriod = this.value;
        loadAnalyticsData(currentPeriod);
        loadMostSellingData(currentPeriod);
        loadSalesTrendData(currentPeriod);
    });
    
    document.getElementById('refresh-data').addEventListener('click', function() {
        loadAnalyticsData(currentPeriod);
        loadMostSellingData(currentPeriod);
        loadSalesTrendData(currentPeriod);
    });
    
    // Functions to load data from the API
    function loadAnalyticsData(period) {
        showLoader('dashboard-loader');
        fetch(`/api/analytics/io/${period}`)
            .then(response => response.json())
            .then(data => {
                hideLoader('dashboard-loader');
                if (data.success) {
                    updateDashboardData(data.data);
                }
            })
            .catch(error => {
                hideLoader('dashboard-loader');
                console.error('Error loading analytics data:', error);
                showError('dashboard-error', 'Failed to load analytics data');
            });
    }
    
    function loadMostSellingData(period) {
        showLoader('most-selling-loader');
        fetch(`/api/analytics/most-selling/${period}`)
            .then(response => response.json())
            .then(data => {
                hideLoader('most-selling-loader');
                if (data.success) {
                    updateMostSellingTable(data.data);
                }
            })
            .catch(error => {
                hideLoader('most-selling-loader');
                console.error('Error loading most selling data:', error);
                showError('most-selling-error', 'Failed to load most selling data');
            });
    }
    
    function loadSalesTrendData(period) {
        showLoader('sales-trend-loader');
        fetch(`/api/analytics/sales-trend/${period}`)
            .then(response => response.json())
            .then(data => {
                hideLoader('sales-trend-loader');
                if (data.success) {
                    updateSalesTrendTable(data.data);
                }
            })
            .catch(error => {
                hideLoader('sales-trend-loader');
                console.error('Error loading sales trend data:', error);
                showError('sales-trend-error', 'Failed to load sales trend data');
            });
    }
    
    // Update dashboard data
    function updateDashboardData(data) {
        if (!data) return;
        
        // Update overview stats
        document.getElementById('total-in').textContent = formatNumber(data.total_in);
        document.getElementById('total-out').textContent = formatNumber(data.total_out);
        document.getElementById('ratio').textContent = data.ratio;
        document.getElementById('total-transactions').textContent = formatNumber(data.total_transactions);
        
        // Update percentage indicators
        updatePercentageIndicator('in-percentage', data.in_percentage);
        updatePercentageIndicator('out-percentage', data.out_percentage);
        updatePercentageIndicator('transaction-percentage', data.transaction_percentage);
        
        // Update charts
        updateIOChart(data.io_chart);
        updateTrendChart(data.trend);
    }
    
    // Update the IO Chart using ApexCharts
    function updateIOChart(chartData) {
        if (!chartData) return;
        
        const options = {
            series: [
                {
                    name: 'Barang Masuk',
                    data: chartData.in
                },
                {
                    name: 'Barang Keluar',
                    data: chartData.out
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: chartData.labels,
                labels: {
                    show: false
                }
            },
            yaxis: {
                title: {
                    text: ''
                },
                labels: {
                    show: false
                }
            },
            fill: {
                opacity: 1,
                colors: ['#3B82F6', '#EF4444']
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return formatNumber(val);
                    }
                }
            },
            legend: {
                position: 'top'
            }
        };
        
        if (ioChart) {
            ioChart.destroy();
        }
        
        ioChart = new ApexCharts(document.querySelector("#io-chart"), options);
        ioChart.render();
    }
    
    // Update the Trend Chart using ApexCharts
    function updateTrendChart(trendData) {
        if (!trendData) return;
        
        const options = {
            series: [
                {
                    name: 'Barang Masuk',
                    type: 'area',
                    data: trendData.in
                },
                {
                    name: 'Barang Keluar',
                    type: 'area',
                    data: trendData.out
                },
                {
                    name: 'Transaksi',
                    type: 'line',
                    data: trendData.transactions
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                stacked: false,
                toolbar: {
                    show: false
                }
            },
            stroke: {
                width: [2, 2, 4],
                curve: 'smooth'
            },
            fill: {
                type: ['gradient', 'gradient', 'solid'],
                opacity: [0.3, 0.3, 1]
            },
            colors: ['#3B82F6', '#EF4444', '#10B981'],
            xaxis: {
                categories: trendData.labels,
                labels: {
                    show: false
                }
            },
            yaxis: [
                {
                    seriesName: 'Barang Masuk',
                    show: false
                },
                {
                    seriesName: 'Barang Keluar',
                    show: false
                },
                {
                    seriesName: 'Transaksi',
                    opposite: true,
                    show: false
                }
            ],
            legend: {
                position: 'top'
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return formatNumber(val);
                    }
                }
            }
        };
        
        if (trendChart) {
            trendChart.destroy();
        }
        
        trendChart = new ApexCharts(document.querySelector("#trend-chart"), options);
        trendChart.render();
    }
    
    // Update Most Selling Products table
    function updateMostSellingTable(data) {
        if (!data || !Array.isArray(data)) return;
        
        const table = document.getElementById('most-selling-table');
        if (!table) return;
        
        table.innerHTML = '';
        
        if (data.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No data available</td>
            `;
            table.appendChild(emptyRow);
            return;
        }
        
        data.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">${index + 1}</td>
                <td class="px-6 py-4 whitespace-nowrap">${item.brand_name || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap">${item.barang_name || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right">${formatNumber(item.total_sold)}</td>
                <td class="px-6 py-4 whitespace-nowrap">${item.satuan || 'Unit'}</td>
            `;
            table.appendChild(row);
        });
    }
    
    // Update Sales Trend table
    function updateSalesTrendTable(data) {
        if (!data) return;
        
        const table = document.getElementById('sales-trend-table');
        if (!table) return;
        
        table.innerHTML = '';
        
        const periods = Object.keys(data);
        
        if (periods.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="2" class="px-6 py-4 text-center text-gray-500">No data available</td>
            `;
            table.appendChild(emptyRow);
            return;
        }
        
        periods.forEach(period => {
            const row = document.createElement('tr');
            const products = data[period];
            
            if (!Array.isArray(products) || products.length === 0) {
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap font-medium">${period}</td>
                    <td class="px-6 py-4 text-gray-500">No products</td>
                `;
                table.appendChild(row);
                return;
            }
            
            let productCells = '';
            products.forEach(product => {
                productCells += `
                    <div class="mb-2 p-2 border rounded-md">
                        <div class="font-medium">${product.brand_name || 'N/A'} - ${product.barang_name || 'N/A'}</div>
                        <div class="flex justify-between text-sm">
                            <span>Terjual:</span>
                            <span class="font-medium">${formatNumber(product.total_sold)} ${product.satuan || 'Unit'}</span>
                        </div>
                    </div>
                `;
            });
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap font-medium">${period}</td>
                <td class="px-6 py-4">
                    <div class="space-y-1">
                        ${productCells}
                    </div>
                </td>
            `;
            table.appendChild(row);
        });
    }
    
    // Update percentage indicators with proper colors and icons
    function updatePercentageIndicator(elementId, percentage) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        if (percentage === null || percentage === undefined) {
            element.innerHTML = `<span class="text-gray-500">Data tidak tersedia</span>`;
            return;
        }
        
        const isPositive = percentage > 0;
        const absPercentage = Math.abs(percentage).toFixed(1);
        
        element.innerHTML = `
            <span class="${isPositive ? 'text-green-600' : 'text-red-600'} mr-1">
                ${isPositive ? '↑' : '↓'} ${absPercentage}%
            </span>
        `;
    }
    
    // Helper function to format numbers with commas
    function formatNumber(num) {
        if (num === null || num === undefined) return '0';
        return new Intl.NumberFormat('id-ID').format(num);
    }
    
    // UI helpers for loading states
    function showLoader(id) {
        const loader = document.getElementById(id);
        if (loader) {
            loader.classList.remove('hidden');
        }
    }
    
    function hideLoader(id) {
        const loader = document.getElementById(id);
        if (loader) {
            loader.classList.add('hidden');
        }
    }
    
    function showError(id, message) {
        const errorElement = document.getElementById(id);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                errorElement.classList.add('hidden');
            }, 5000);
        }
    }
    
    // Add function to manually trigger a data refresh through the backend job
    function triggerDataRefresh() {
        const refreshButton = document.getElementById('refresh-data');
        
        if (refreshButton) {
            refreshButton.disabled = true;
            refreshButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Refreshing...
            `;
            
            // Call the endpoint to trigger the refresh job
            fetch('/api/analytics/refresh', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ periode: currentPeriod })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Data refresh job has been queued. Updates will appear shortly.');
                } else {
                    showError('dashboard-error', 'Failed to refresh data: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error triggering refresh:', error);
                showError('dashboard-error', 'Failed to trigger data refresh');
            })
            .finally(() => {
                // Re-enable the button after a short delay
                setTimeout(() => {
                    refreshButton.disabled = false;
                    refreshButton.innerHTML = `
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Data
                    `;
                }, 2000);
            });
        }
    }
    
    // Show toast notification
    function showNotification(message, type = 'success') {
        // Check if notification container exists, if not create it
        let container = document.getElementById('notification-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed bottom-4 right-4 z-50 flex flex-col gap-2';
            document.body.appendChild(container);
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `p-4 rounded-md shadow-lg transform transition-all duration-300 ease-in-out translate-y-0 opacity-100 flex items-center ${
            type === 'success' ? 'bg-green-100 border-l-4 border-green-500' : 
            type === 'error' ? 'bg-red-100 border-l-4 border-red-500' : 
            'bg-blue-100 border-l-4 border-blue-500'
        }`;
        
        // Add icon based on type
        let icon = '';
        if (type === 'success') {
            icon = `<svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>`;
        } else if (type === 'error') {
            icon = `<svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>`;
        } else {
            icon = `<svg class="h-5 w-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`;
        }
        
        notification.innerHTML = `
            ${icon}
            <span class="flex-1">${message}</span>
            <button class="ml-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        // Add to container
        container.appendChild(notification);
        
        // Add click event to close button
        notification.querySelector('button').addEventListener('click', () => {
            removeNotification(notification);
        });
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            removeNotification(notification);
        }, 5000);
    }
    
    // Remove notification with animation
    function removeNotification(notification) {
        notification.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
    
    // Add event listener for refresh button
    const manualRefreshBtn = document.getElementById('trigger-refresh');
    if (manualRefreshBtn) {
        manualRefreshBtn.addEventListener('click', triggerDataRefresh);
    }
});