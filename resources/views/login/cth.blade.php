<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981',
                        danger: '#EF4444',
                        warning: '#F59E0B',
                        info: '#3B82F6',
                        dark: '#1F2937',
                        light: '#F3F4F6',
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar {
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .sidebar-text {
            display: none;
        }

        .sidebar.collapsed .logo-text {
            display: none;
        }

        .sidebar.collapsed .sidebar-item {
            justify-content: center;
        }

        .main-content {
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 80px;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar flex w-64 flex-col bg-white shadow-lg">
            <div class="flex items-center border-b border-gray-200 p-4">
                <div class="bg-primary flex h-10 w-10 items-center justify-center rounded-full font-bold text-white">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <span class="logo-text ml-3 text-xl font-bold text-gray-800">AnalyticsPro</span>
            </div>

            <div class="flex-1 overflow-y-auto py-4">
                <div class="mb-8 px-4">
                    <p class="sidebar-text mb-2 text-xs font-semibold uppercase text-gray-500">Main</p>
                    <a href="#" class="sidebar-item bg-primary mb-1 flex items-center rounded-lg px-3 py-2 text-sm text-white">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="#" class="sidebar-item mb-1 flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span class="sidebar-text">Analytics</span>
                    </a>
                    <a href="#" class="sidebar-item mb-1 flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-users mr-3"></i>
                        <span class="sidebar-text">Customers</span>
                    </a>
                    <a href="#" class="sidebar-item mb-1 flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        <span class="sidebar-text">Products</span>
                    </a>
                </div>

                <div class="mb-8 px-4">
                    <p class="sidebar-text mb-2 text-xs font-semibold uppercase text-gray-500">Reports</p>
                    <a href="#" class="sidebar-item mb-1 flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-file-alt mr-3"></i>
                        <span class="sidebar-text">Sales</span>
                    </a>
                    <a href="#" class="sidebar-item mb-1 flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-file-invoice mr-3"></i>
                        <span class="sidebar-text">Revenue</span>
                    </a>
                    <a href="#" class="sidebar-item mb-1 flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-chart-pie mr-3"></i>
                        <span class="sidebar-text">Performance</span>
                    </a>
                </div>

                <div class="px-4">
                    <p class="sidebar-text mb-2 text-xs font-semibold uppercase text-gray-500">Settings</p>
                    <a href="#" class="sidebar-item mb-1 flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-3"></i>
                        <span class="sidebar-text">Settings</span>
                    </a>
                    <a href="#" class="sidebar-item mb-1 flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span class="sidebar-text">Logout</span>
                    </a>
                </div>
            </div>

            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" class="h-10 w-10 rounded-full" alt="User">
                    <div class="sidebar-text ml-3">
                        <p class="text-sm font-medium text-gray-800">Sarah Johnson</p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="main-content" class="main-content flex flex-1 flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="flex items-center justify-between bg-white px-6 py-4 shadow-sm">
                <div class="flex items-center">
                    <button id="toggle-sidebar" class="mr-4 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="bg-danger absolute right-0 top-0 h-2 w-2 rounded-full"></span>
                        </button>
                    </div>
                    <div class="relative">
                        <button class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-envelope text-xl"></i>
                            <span class="bg-danger absolute right-0 top-0 h-2 w-2 rounded-full"></span>
                        </button>
                    </div>
                    <div class="relative">
                        <button class="flex items-center text-gray-600 hover:text-gray-900">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="h-8 w-8 rounded-full" alt="User">
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <!-- Stats Cards -->
                <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                                <h3 class="mt-1 text-2xl font-bold text-gray-800">$24,780</h3>
                                <p class="text-success mt-2 flex items-center text-sm">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    <span>12.5% from last month</span>
                                </p>
                            </div>
                            <div class="bg-primary flex h-12 w-12 items-center justify-center rounded-full bg-opacity-10">
                                <i class="fas fa-dollar-sign text-primary text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                                <h3 class="mt-1 text-2xl font-bold text-gray-800">1,245</h3>
                                <p class="text-success mt-2 flex items-center text-sm">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    <span>8.3% from last month</span>
                                </p>
                            </div>
                            <div class="bg-secondary flex h-12 w-12 items-center justify-center rounded-full bg-opacity-10">
                                <i class="fas fa-shopping-cart text-secondary text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Active Users</p>
                                <h3 class="mt-1 text-2xl font-bold text-gray-800">3,456</h3>
                                <p class="text-danger mt-2 flex items-center text-sm">
                                    <i class="fas fa-arrow-down mr-1"></i>
                                    <span>2.1% from last month</span>
                                </p>
                            </div>
                            <div class="bg-info flex h-12 w-12 items-center justify-center rounded-full bg-opacity-10">
                                <i class="fas fa-users text-info text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Conversion Rate</p>
                                <h3 class="mt-1 text-2xl font-bold text-gray-800">3.6%</h3>
                                <p class="text-success mt-2 flex items-center text-sm">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    <span>1.2% from last month</span>
                                </p>
                            </div>
                            <div class="bg-warning flex h-12 w-12 items-center justify-center rounded-full bg-opacity-10">
                                <i class="fas fa-percentage text-warning text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Main Chart -->
                    <div class="rounded-lg bg-white p-6 shadow lg:col-span-2">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-800">Revenue Overview</h2>
                            <div class="flex items-center space-x-2">
                                <button class="bg-primary rounded-md px-3 py-1 text-xs text-white">This Year</button>
                                <button class="rounded-md bg-gray-100 px-3 py-1 text-xs text-gray-700 hover:bg-gray-200">Last Year</button>
                            </div>
                        </div>
                        <div class="h-80">
                            <canvas id="mainChart"></canvas>
                        </div>
                    </div>

                    <!-- Pie Chart -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-lg font-semibold text-gray-800">Traffic Sources</h2>
                        <div class="h-80">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Recent Orders -->
                    <div class="rounded-lg bg-white p-6 shadow lg:col-span-2">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-800">Recent Orders</h2>
                            <button class="text-primary text-sm font-medium">View All</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Order ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">#ORD-0001</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">John Smith</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">12 Jun 2023</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">$245.00</td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            <span class="bg-success text-success rounded-full bg-opacity-10 px-2 py-1 text-xs">Completed</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">#ORD-0002</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Sarah Johnson</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">11 Jun 2023</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">$189.50</td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            <span class="bg-warning text-warning rounded-full bg-opacity-10 px-2 py-1 text-xs">Processing</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">#ORD-0003</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Michael Brown</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">10 Jun 2023</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">$320.75</td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            <span class="bg-success text-success rounded-full bg-opacity-10 px-2 py-1 text-xs">Completed</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">#ORD-0004</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Emily Davis</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">9 Jun 2023</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">$145.00</td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            <span class="bg-danger text-danger rounded-full bg-opacity-10 px-2 py-1 text-xs">Cancelled</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">#ORD-0005</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Robert Wilson</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">8 Jun 2023</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">$275.50</td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            <span class="bg-success text-success rounded-full bg-opacity-10 px-2 py-1 text-xs">Completed</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-lg font-semibold text-gray-800">Recent Activity</h2>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="bg-primary mr-3 mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-opacity-10">
                                    <i class="fas fa-shopping-cart text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">New order received</p>
                                    <p class="text-xs text-gray-500">Order #ORD-0006 from Jessica Parker</p>
                                    <p class="mt-1 text-xs text-gray-400">2 hours ago</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-success mr-3 mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-opacity-10">
                                    <i class="fas fa-user-plus text-success"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">New customer registered</p>
                                    <p class="text-xs text-gray-500">David Miller joined the platform</p>
                                    <p class="mt-1 text-xs text-gray-400">5 hours ago</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-warning mr-3 mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-opacity-10">
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Payment failed</p>
                                    <p class="text-xs text-gray-500">Payment for order #ORD-0004 failed</p>
                                    <p class="mt-1 text-xs text-gray-400">1 day ago</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-info mr-3 mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-opacity-10">
                                    <i class="fas fa-chart-line text-info"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Monthly report generated</p>
                                    <p class="text-xs text-gray-500">May 2023 sales report is ready</p>
                                    <p class="mt-1 text-xs text-gray-400">2 days ago</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-secondary mr-3 mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-opacity-10">
                                    <i class="fas fa-truck text-secondary"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Order shipped</p>
                                    <p class="text-xs text-gray-500">Order #ORD-0003 has been shipped</p>
                                    <p class="mt-1 text-xs text-gray-400">3 days ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle sidebar
        const toggleSidebar = document.getElementById('toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Main Chart
        const mainCtx = document.getElementById('mainChart').getContext('2d');
        const mainChart = new Chart(mainCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                        label: 'Revenue',
                        data: [5000, 8000, 12000, 15000, 18000, 21000, 24000, 22000, 19000, 16000, 13000, 10000],
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Last Year',
                        data: [4000, 7000, 10000, 13000, 16000, 19000, 22000, 20000, 17000, 14000, 11000, 8000],
                        borderColor: '#9CA3AF',
                        backgroundColor: 'rgba(156, 163, 175, 0.05)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Direct', 'Organic Search', 'Social Media', 'Email', 'Referral'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: [
                        '#4F46E5',
                        '#10B981',
                        '#3B82F6',
                        '#F59E0B',
                        '#EF4444'
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
                    },
                },
                cutout: '70%'
            }
        });
    </script>
</body>

</html>
