<?php
include('config/db.php');



// Sales by Roast Type
$query_sales_by_roast_type = "
    SELECT orders.roast_type, COUNT(order_id) AS total_orders
    FROM orders
    INNER JOIN products ON orders.product_id = products.product_id
    GROUP BY orders.roast_type";
$result_sales_by_roast_type = $conn->query($query_sales_by_roast_type);

$roastTypeLabels = [];
$ordersData = [];

if ($result_sales_by_roast_type->num_rows > 0) {
    while ($row = $result_sales_by_roast_type->fetch_assoc()) {
        $roastTypeLabels[] = $row['roast_type'];
        $ordersData[] = $row['total_orders'];
    }
} else {
    echo "No data found for roast type.";
}



// Orders by Coffee Type
$query_orders_by_coffee_type = "SELECT orders.coffee_type_name, COUNT(order_id) AS total_orders 
                                FROM orders
                                INNER JOIN products ON orders.product_id = products.product_id
                                INNER JOIN customers ON orders.customer_id = customers.CustomerID
                                GROUP BY orders.coffee_type_name";
$result_orders_by_coffee_type = $conn->query($query_orders_by_coffee_type);

$coffeeTypeLabels = [];
$orderCounts = [];

if ($result_orders_by_coffee_type->num_rows > 0) {
    while ($row = $result_orders_by_coffee_type->fetch_assoc()) {
        $coffeeTypeLabels[] = $row['coffee_type_name'];
        $orderCounts[] = $row['total_orders'];
    }
} else {
    echo "No order data found for the selected period.";
}



// Sales by Country
$query_sales_by_country = "
    SELECT orders.country, SUM(sales) AS total_sales
    FROM orders
    INNER JOIN products ON orders.product_id = products.product_id
    INNER JOIN customers ON orders.customer_id = customers.CustomerID
    GROUP BY orders.country";
$result_sales_by_country = $conn->query($query_sales_by_country);

$countries = [];
$salesData = [];

if ($result_sales_by_country->num_rows > 0) {
    while ($row = $result_sales_by_country->fetch_assoc()) {
        $country = $row['country'];
        $total_sales = $row['total_sales'];
        
        $countries[] = $country;   
        $salesData[] = $total_sales;  
    }
} else {
    echo "No data found for sales by country.";
}

$labels = json_encode($countries);      
$salesDataJson = json_encode($salesData); 



// Unit price by country
$query_price_unit_by_country = "
    SELECT orders.country, SUM(orders.unit_price) AS unit_price
    FROM orders
    INNER JOIN products ON orders.product_id = products.product_id
    INNER JOIN customers ON orders.customer_id = customers.CustomerID
    GROUP BY orders.country";

$result_unit_price_by_country = $conn->query($query_price_unit_by_country);

$unitPrices = [];

if ($result_unit_price_by_country->num_rows > 0) {
    while ($row = $result_unit_price_by_country->fetch_assoc()) {
        $country = $row['country'];
        $unit_price = $row['unit_price'];
        
        $countries[] = $country;   
        $unitPrices[] = $unit_price;  
    }
} else {
    echo "No data found for unit price by country.";
}

$unitPricesJson = json_encode($unitPrices);
?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="assets/css/style.css">

<body>
<div class="sidebar">
    <ul>
        <li><a href="#">Orders</a></li>
        <li><a href="#">Products</a></li>
        <li><a href="#">Customers</a></li>
    </ul>
</div>

<div class="content">
    <h1>Order Dashboard</h1>

    <div class="chart-container">
        <div class="chart">
            <h3>Orders by Roast Type</h3> 
            <canvas id="salesByRoastTypeChart"></canvas>
        </div>
        <div class="chart">
            <h3>Total Orders by Coffee Type</h3> 
            <canvas id="ordersByCoffeeTypeChart"></canvas>
        </div>
    </div>

    <div class="chart-container">
        <div class="chart">
            <h3>Total Sales by Country</h3> 
            <canvas id="ordersByCountryChart"></canvas>
        </div>
        <div class="chart">
            <h3>Total Unit Price By Country</h3> 
            <canvas id="unitPriceByCountryChart"></canvas>
        </div>
    </div>
</div>

<script>
    
    // Chart 1
    var ctx1 = document.getElementById('salesByRoastTypeChart').getContext('2d');
    var salesByRoastTypeChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($roastTypeLabels); ?>,
            datasets: [{
                label: 'Orders by Roast Type',
                data: <?php echo json_encode($ordersData); ?>,
                backgroundColor: ['#FF6347', '#FF4500', '#32CD32', '#1E90FF', '#FF1493', '#00CED1'],
            }]
        }
    });

     // Chart 2
    var ctx2 = document.getElementById('ordersByCoffeeTypeChart').getContext('2d');
    var ordersByCoffeeTypeChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($coffeeTypeLabels); ?>,
            datasets: [{
                label: 'Total Orders by Coffee Type',
                data: <?php echo json_encode($orderCounts); ?>,
                backgroundColor: '#3498db',
                borderColor: '#2980b9',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

     // Chart 3
    var ctx3 = document.getElementById('ordersByCountryChart').getContext('2d');
    var ordersByCountryChart = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: <?php echo $labels; ?>,
            datasets: [{
                label: 'Total Sales by Country',
                data: <?php echo $salesDataJson; ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.2)',
                fill: true,
                tension: 0.1,
                pointRadius: 5,
                pointBackgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Country'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total Sales'
                    },
                    beginAtZero: true
                }
            }
        }
    });

      // Chart 4
        var ctx4 = document.getElementById('unitPriceByCountryChart').getContext('2d');
        var unitPriceByCountryChart = new Chart(ctx4, {
            type: 'doughnut',  
            data: {
                labels: <?php echo $labels; ?>,
                datasets: [{
                    label: 'Unit Price by Country',
                    data: <?php echo $unitPricesJson; ?>,
                    backgroundColor: [
                        '#FF6347', '#FF4500', '#32CD32', '#1E90FF', '#FF1493', '#00CED1'
                    ],  
                    borderColor: '#fff', 
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',  
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });


</script>
</body>
