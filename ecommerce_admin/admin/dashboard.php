<?php
// No login requirement
require_once '../config/database.php';

// Get basic statistics
try {
    // Count total products
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $product_count = $stmt->fetch()['total'];
    
    // Count total orders
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $order_count = $stmt->fetch()['total'];
    
    // Count total pending orders
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $pending_order_count = $stmt->fetch()['total'];
    
    // Count total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $user_count = $stmt->fetch()['total'];
    
    // Get 5 most recent orders
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll();
    
    // Get data for pie chart - Orders by status
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
    $order_status_data = $stmt->fetchAll();
    
    // Get data for bar chart - Monthly revenue for current year
    $current_year = date('Y');
    $stmt = $pdo->query("
        SELECT 
            MONTH(order_date) as month, 
            SUM(total) as revenue 
        FROM orders 
        WHERE YEAR(order_date) = '$current_year' AND status != 'cancelled'
        GROUP BY MONTH(order_date) 
        ORDER BY month
    ");
    $monthly_revenue = $stmt->fetchAll();
    
    // Prepare data for JavaScript charts
    $status_labels = [];
    $status_data = [];
    $status_colors = [
        'pending' => '#FFC107',
        'processing' => '#2196F3',
        'shipped' => '#9C27B0',
        'delivered' => '#4CAF50',
        'cancelled' => '#F44336'
    ];
    $chart_colors = [];
    
    foreach ($order_status_data as $status) {
        $status_name = '';
        switch($status['status']) {
            case 'pending': $status_name = 'Pending'; break;
            case 'processing': $status_name = 'Processing'; break;
            case 'shipped': $status_name = 'Shipped'; break;
            case 'delivered': $status_name = 'Delivered'; break;
            case 'cancelled': $status_name = 'Cancelled'; break;
            default: $status_name = $status['status'];
        }
        $status_labels[] = $status_name;
        $status_data[] = $status['count'];
        $chart_colors[] = $status_colors[$status['status']] ?? '#999999';
    }
    
    // Prepare data for bar chart
    $months = [];
    $revenue_data = array_fill(0, 12, 0); // Initialize array for 12 months with value 0
    
    foreach ($monthly_revenue as $data) {
        $revenue_data[$data['month']-1] = $data['revenue'];
    }
    
    $month_names = ['January', 'February', 'March', 'April', 'May', 'June', 
                    'July', 'August', 'September', 'October', 'November', 'December'];
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Set default value for admin username
$admin_username = "Administrator";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin System</title>
    <link rel="stylesheet" href="../styles/style.css">
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        
        .chart-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1;
            min-width: 300px;
        }
        
        .chart-title {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
        }
        
        canvas {
            width: 100% !important;
            height: 300px !important;
        }
        
        @media (max-width: 768px) {
            .charts-container {
                flex-direction: column;
            }
            
            .chart-card {
                width: 100%;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .chart-card {
                background: #2a2a2a;
            }
            
            .chart-title {
                color: #e0e0e0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <header class="admin-header">
                <h1>Control Panel</h1>
                <div class="user-info">
                    <span>Hello, <?php echo $admin_username; ?></span>
                    <a href="index.php" class="btn btn-sm btn-primary">Home</a>
                </div>
            </header>
            
            <div class="dashboard">
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $product_count; ?></div>
                        <div class="stat-label">Products</div>
                        <a href="./products/index.php" class="stat-link">View details</a>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $order_count; ?></div>
                        <div class="stat-label">Orders</div>
                        <a href="../admin/orders/index.php" class="stat-link">View details</a>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $pending_order_count; ?></div>
                        <div class="stat-label">Pending Orders</div>
                        <a href="orders/index.php?status=pending" class="stat-link">View details</a>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $user_count; ?></div>
                        <div class="stat-label">Users</div>
                        <a href="./users/index.php" class="stat-link">View details</a>
                    </div>
                </div>
                
                <!-- Add charts section -->
                <div class="charts-container">
                    <div class="chart-card">
                        <h3 class="chart-title">Orders by Status</h3>
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    
                    <div class="chart-card">
                        <h3 class="chart-title">Monthly Revenue for <?php echo $current_year; ?></h3>
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
                
                <div class="recent-orders">
                    <h2>Recent Orders</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Order Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><?php echo $order['order_id']; ?></td>
                                <td><?php echo $order['full_name']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                <td><?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php 
                                        $status = '';
                                        switch($order['status']) {
                                            case 'pending': $status = 'Pending'; break;
                                            case 'processing': $status = 'Processing'; break;
                                            case 'shipped': $status = 'Shipped'; break;
                                            case 'delivered': $status = 'Delivered'; break;
                                            case 'cancelled': $status = 'Cancelled'; break;
                                            default: $status = $order['status'];
                                        }
                                        echo $status;
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="orders/edit.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="../admin/orders/index.php" class="btn btn-outline">View all orders</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize pie chart - Orders by status
        const statusLabels = <?php echo json_encode($status_labels); ?>;
        const statusData = <?php echo json_encode($status_data); ?>;
        const chartColors = <?php echo json_encode($chart_colors); ?>;
        
        const statusChartCtx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(statusChartCtx, {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: chartColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e0e0e0' : '#666'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Initialize bar chart - Monthly revenue
        const monthNames = <?php echo json_encode($month_names); ?>;
        const revenueData = <?php echo json_encode($revenue_data); ?>;
        
        const revenueChartCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(revenueChartCtx, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData,
                    backgroundColor: '#5D5CDE',
                    borderColor: '#4a49b3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('en-US').format(value);
                            },
                            color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e0e0e0' : '#666'
                        },
                        grid: {
                            color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e0e0e0' : '#666'
                        },
                        grid: {
                            color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e0e0e0' : '#666'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ' + new Intl.NumberFormat('en-US').format(context.raw);
                            }
                        }
                    }
                }
            }
        });
        // Update chart colors when color scheme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            window.location.reload(); // Reload page to update chart colors
        });
    </script>
</body>
</html>