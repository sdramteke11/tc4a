<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .category-grid {
            overflow-x: auto;
            white-space: nowrap;
        }
        .category-item {
            flex-shrink: 0;
            width: 120px;
            height: 140px;
            background-color: #f0f0f0;
            border-radius: 8px;
            scroll-snap-align: center;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 80px;
            margin: 20px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .list {
            display: flex;
            gap: 8px;
            padding: 16px;
            list-style: none;
            overflow-x: scroll;
            scroll-snap-type: x mandatory;
            /* Hide scrollbar in Firefox */
            scrollbar-width: none;
            /* Hide scrollbar in IE and Edge */
            -ms-overflow-style: none;
        }
        
        .category-item:hover {
            transform: scale(1.05);
        }
        .category-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
        }
        .category-name {
            font-weight: bold;
        }
        .chart-container {
            float: left;
            width: 40%;
            margin-right: 20px;
        }
        .products-container {
            float: left;
            width: 60%;
        }
        .product-card {
            margin-bottom: 20px;
        }
        .product-card img {
            height: 150px;
            object-fit: cover;
        }
        .product-card .card-title {
            font-weight: bold;
        }
        .product-card .card-text {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center mb-5">Product Categories</h1>
                <div class="category-grid">
                    <ul class="list" id="categories">
                        @foreach($categories as $category)
                            <div class="category-item" data-id="{{ $category['id'] }}">
                                <img src="{{ $category['image'] }}" class="category-img" alt="{{ $category['name'] }}">
                                <span class="category-name">{{ $category['name'] }}</span>
                            </div>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 chart-container">
                <h4 style="text-align:center; margin-bottom: 20px;">No of Products per Category</h4>   
                <canvas id="categoryChart" width="400" height="200"></canvas>
            </div>
            <div class="col-md-6 products-containers">
            <h4 style="text-align:center; margin-bottom: 20px;">Products</h4>
                <div class="row" id="products-container">
                    <!-- Products will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initially load products for all categories
            loadProducts('all');
            startAutoScroll();
            // Load products for the selected category
            $('.category-item').on('click', function() {
                var categoryId = $(this).data('id');
                loadProducts(categoryId);
            });

            // Function to load products via AJAX
            function loadProducts(categoryId) {
                $.ajax({
                    url: '/category/' + categoryId,
                    method: 'GET',
                    success: function(products) {
                        $('#products-container').empty();

                        products.slice(0, 10).forEach(function(product) {
                            var productHTML = `
                                <div class="col-md-6 product-card">
                                    <div class="card">
                                        <img src="${product.images[0]}" class="card-img-top" alt="${product.title}">
                                        <div class="card-body">
                                            <h5 class="card-title">${product.title}</h5>
                                            <p class="card-text">$${product.price}</p>
                                            <p class="card-text">${product.description}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#products-container').append(productHTML);
                        });
                    }
                });
            }

            // Generate category chart
            var ctx = document.getElementById('categoryChart').getContext('2d');
            var categoryData = {
                labels: @json($categories->pluck('name')),
                datasets: [{
                    label: 'Number of Products',
                    data: @json($categoryCounts->values()),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            };

            new Chart(ctx, {
                type: 'pie',
                data: categoryData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Number of Products per Category'
                        }
                    }
                }
            });

            function startAutoScroll() {
                const scrollInterval = 6000; // Adjust the interval as needed (in milliseconds)
                const scrollStep = 1; // Adjust the scroll step as needed
                const scrollContainer = $('.list');
                const listWidth = scrollContainer.get(0).scrollWidth - scrollContainer.width();

                setInterval(function() {
                    scrollContainer.animate({ scrollLeft: listWidth }, scrollInterval, 'linear', function() {
                        scrollContainer.animate({ scrollLeft: 0 }, 0); // Reset scroll position
                    });
                }, scrollInterval);
            }
        });
    </script>
</body>
</html>
