<h3>Welcome to Simple Online Groceries Ordering System</h3>
<hr>
<div class="col-12">
    <div class="row gx-3 row-cols-4">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-boxes fs-3 text-primary"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Total Products</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $products = $conn->query("SELECT count(product_id) as `count` FROM `product_list` where `status` = 1 ")->fetchArray()['count'];
                                echo $products > 0 ? number_format($products) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-th-list fs-3 text-dark"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Pending Orders</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $orders = $conn->query("SELECT count(order_id) as `count` FROM `order_list` where `status` = 0 ")->fetchArray()['count'];
                                echo $orders > 0 ? number_format($orders) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-th-list fs-3 text-success"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Delivered Orders</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $orders = $conn->query("SELECT count(order_id) as `count` FROM `order_list` where `status` = 2 ")->fetchArray()['count'];
                                echo $orders > 0 ? number_format($orders) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-th-list fs-3 text-danger"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Cancelled Orders</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $orders = $conn->query("SELECT count(order_id) as `count` FROM `order_list` where `status` = 3 ")->fetchArray()['count'];
                                echo $orders > 0 ? number_format($orders) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>