<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Store Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e63946',
                        secondary: '#f1faee',
                        accent: '#a8dadc',
                        dark: '#1d3557',
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #e63946 0%, #ff6b6b 100%);
        }

        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid white;
        }

        .status-badge {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .product-row:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="gradient-bg flex w-64 flex-col text-white">
                <div class="flex h-16 items-center justify-center px-4">
                    <div class="flex items-center">
                        <i class="fas fa-store-alt mr-2 text-2xl"></i>
                        <span class="text-xl font-bold">RedMart Pro</span>
                    </div>
                </div>
                <div class="flex flex-grow flex-col overflow-y-auto px-4 py-4">
                    <div class="space-y-1">
                        <a href="#" class="sidebar-item flex items-center rounded-md bg-red-800 px-4 py-3 text-sm font-medium text-white">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                        <a href="#" class="sidebar-item flex items-center rounded-md px-4 py-3 text-sm font-medium text-white hover:bg-red-700">
                            <i class="fas fa-box-open mr-3"></i>
                            Products
                        </a>
                        <a href="#" class="sidebar-item flex items-center rounded-md px-4 py-3 text-sm font-medium text-white hover:bg-red-700">
                            <i class="fas fa-shopping-cart mr-3"></i>
                            Sales
                        </a>
                        <a href="#" class="sidebar-item flex items-center rounded-md px-4 py-3 text-sm font-medium text-white hover:bg-red-700">
                            <i class="fas fa-truck mr-3"></i>
                            Purchases
                        </a>
                        <a href="#" class="sidebar-item flex items-center rounded-md px-4 py-3 text-sm font-medium text-white hover:bg-red-700">
                            <i class="fas fa-users mr-3"></i>
                            Employees
                        </a>
                        <a href="#" class="sidebar-item flex items-center rounded-md px-4 py-3 text-sm font-medium text-white hover:bg-red-700">
                            <i class="fas fa-chart-line mr-3"></i>
                            Reports
                        </a>
                        <a href="#" class="sidebar-item flex items-center rounded-md px-4 py-3 text-sm font-medium text-white hover:bg-red-700">
                            <i class="fas fa-coins mr-3"></i>
                            Tokens & Points
                        </a>
                    </div>

                    <div class="mb-4 mt-auto">
                        <div class="rounded-lg bg-red-800 p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">
                                        Store Status: <span class="font-bold">OPEN</span>
                                    </p>
                                    <p class="text-xs">Hours: 8:00 AM - 10:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-1 flex-col overflow-hidden">
            <!-- Top Navigation -->
            <div class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-6">
                <div class="flex items-center">
                    <button class="text-gray-500 focus:outline-none md:hidden">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="relative mx-4">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input class="w-full rounded-md bg-gray-100 py-2 pl-10 pr-4 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500" type="text" placeholder="Search...">
                    </div>
                </div>
                <div class="flex items-center">
                    <button class="rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="relative ml-3">
                        <div class="flex items-center">
                            <img class="h-8 w-8 rounded-full" src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                            <span class="ml-2 text-sm font-medium">Admin</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 overflow-auto p-6">
                <!-- Store Banner -->
                <div class="relative mb-6 h-48 overflow-hidden rounded-lg">
                    <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Store Banner">
                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40">
                        <div class="text-center text-white">
                            <h1 class="mb-2 text-3xl font-bold">RedMart Superstore</h1>
                            <div class="flex items-center justify-center">
                                <span class="status-badge flex items-center rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white">
                                    <i class="fas fa-check-circle mr-1"></i> OPEN NOW
                                </span>
                                <span class="ml-2 text-sm">8:00 AM - 10:00 PM</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center">
                            <div class="rounded-full bg-red-100 p-3 text-red-600">
                                <i class="fas fa-coins text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Store Points</p>
                                <p class="text-2xl font-bold">12,450</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="h-2 w-full rounded-full bg-gray-200">
                                <div class="h-2 rounded-full bg-red-600" style="width: 75%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Next reward at 15,000 points</p>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-3 text-blue-600">
                                <i class="fas fa-token text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Available Tokens</p>
                                <p class="text-2xl font-bold">1,250</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="rounded-full bg-red-600 px-3 py-1 text-xs font-medium text-white hover:bg-red-700">
                                Redeem Tokens
                            </button>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center">
                            <div class="rounded-full bg-green-100 p-3 text-green-600">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Today's Sales</p>
                                <p class="text-2xl font-bold">$3,245</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                <i class="fas fa-arrow-up mr-1"></i> 12% from yesterday
                            </span>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center">
                            <div class="rounded-full bg-purple-100 p-3 text-purple-600">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Employees</p>
                                <p class="text-2xl font-bold">8</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-gray-500">2 on duty now</p>
                        </div>
                    </div>
                </div>

                <!-- Products Section - List View -->
                <div class="mb-6 rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold">Products Inventory</h2>
                            <div class="flex space-x-2">
                                <button class="rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-200">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                <button class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                    <i class="fas fa-plus mr-1"></i> Add Product
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Category</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Price</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Stock</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <!-- Product Row 1 -->
                                    <tr class="product-row transition duration-150 ease-in-out">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1586495777744-4413f21062fa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">Premium Coffee Beans</div>
                                                    <div class="text-xs text-gray-500">SKU: CB-1001</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">Beverages</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center">
                                                <span class="font-bold text-red-600">$12.99</span>
                                                <span class="ml-2 text-xs text-gray-500 line-through">$15.30</span>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">45</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                                In Stock
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <button class="mr-3 text-red-600 hover:text-red-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Product Row 2 -->
                                    <tr class="product-row transition duration-150 ease-in-out">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1519735777090-ec97162dc266?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1115&q=80" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">Organic Apples</div>
                                                    <div class="text-xs text-gray-500">SKU: FR-2005</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">Fruits</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">$4.99/kg</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">120</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                                In Stock
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <button class="mr-3 text-red-600 hover:text-red-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Product Row 3 -->
                                    <tr class="product-row transition duration-150 ease-in-out">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1550583724-b2692b85b150?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">Sparkling Water</div>
                                                    <div class="text-xs text-gray-500">SKU: BW-3002</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">Beverages</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">$1.99</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">89</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                                In Stock
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <button class="mr-3 text-red-600 hover:text-red-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Product Row 4 -->
                                    <tr class="product-row transition duration-150 ease-in-out">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1606787366850-de6330128bfc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">Fresh Bread</div>
                                                    <div class="text-xs text-gray-500">SKU: BK-4007</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">Bakery</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">$3.49</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">15</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                                                Low Stock
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <button class="mr-3 text-red-600 hover:text-red-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">24</span> products
                            </div>
                            <div class="flex space-x-1">
                                <button class="rounded-md bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800">
                                    Previous
                                </button>
                                <button class="rounded-md bg-red-600 px-3 py-1 text-sm font-medium text-white">
                                    1
                                </button>
                                <button class="rounded-md bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800">
                                    2
                                </button>
                                <button class="rounded-md bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Sales & Employees -->
                <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Recent Sales -->
                    <div class="rounded-lg bg-white shadow">
                        <div class="border-b border-gray-200 p-6">
                            <h2 class="text-lg font-semibold">Recent Sales</h2>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Order ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">#ORD-78945</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">John Smith</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">$45.99</td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Completed</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">#ORD-78944</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">Sarah Johnson</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">$120.50</td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Completed</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">#ORD-78943</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">Michael Brown</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">$32.75</td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Processing</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">#ORD-78942</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">Emily Davis</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">$89.30</td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">Cancelled</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Employees -->
                    <div class="rounded-lg bg-white shadow">
                        <div class="border-b border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold">Store Employees</h2>
                                <button class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                    <i class="fas fa-plus mr-1"></i> Add Employee
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Employee 1 -->
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Employee">
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Jessica Wilson</p>
                                        <p class="text-xs text-gray-500">Cashier</p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                            <i class="fas fa-circle mr-1 text-xs"></i> On Duty
                                        </span>
                                    </div>
                                </div>

                                <!-- Employee 2 -->
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Employee">
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Robert Garcia</p>
                                        <p class="text-xs text-gray-500">Store Manager</p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                            <i class="fas fa-circle mr-1 text-xs"></i> On Duty
                                        </span>
                                    </div>
                                </div>

                                <!-- Employee 3 -->
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Employee">
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Amanda Lee</p>
                                        <p class="text-xs text-gray-500">Stock Clerk</p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                                            <i class="fas fa-circle mr-1 text-xs"></i> Off Duty
                                        </span>
                                    </div>
                                </div>

                                <!-- Employee 4 -->
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/men/75.jpg" alt="Employee">
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">David Kim</p>
                                        <p class="text-xs text-gray-500">Cashier</p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                                            <i class="fas fa-circle mr-1 text-xs"></i> Off Duty
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tokens & Points Section -->
                <div class="mb-6 rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-lg font-semibold">Tokens & Points System</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Points Card -->
                            <div class="rounded-lg bg-gradient-to-r from-red-500 to-red-600 p-6 text-white">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="text-lg font-bold">Loyalty Points</h3>
                                    <i class="fas fa-coins text-2xl"></i>
                                </div>
                                <div class="mb-4">
                                    <p class="mb-2 text-3xl font-bold">12,450 pts</p>
                                    <p class="text-sm opacity-80">Earned from purchases</p>
                                </div>
                                <div class="mb-2 h-2 w-full rounded-full bg-white bg-opacity-20">
                                    <div class="h-2 rounded-full bg-white" style="width: 65%"></div>
                                </div>
                                <p class="text-xs">Next reward at 15,000 points</p>
                            </div>

                            <!-- Tokens Card -->
                            <div class="rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="text-lg font-bold">Store Tokens</h3>
                                    <i class="fas fa-token text-2xl"></i>
                                </div>
                                <div class="mb-4">
                                    <p class="mb-2 text-3xl font-bold">1,250 tokens</p>
                                    <p class="text-sm opacity-80">Available for redemption</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="rounded-full bg-white px-3 py-1 text-xs font-bold text-blue-600">
                                        Redeem Now
                                    </button>
                                    <button class="rounded-full bg-white bg-opacity-20 px-3 py-1 text-xs font-bold text-white">
                                        How It Works
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
