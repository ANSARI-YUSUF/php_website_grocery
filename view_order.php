<?php 
require_once('./DBConnection.php');
$sql = "SELECT o.*,c.fullname,f.location, f.amount FROM `order_list` o inner join `customer_list` c on o.customer_id = c.customer_id inner join fees_list f on o.fee_id = f.fee_id where `order_id` = '{$_GET['id']}'";
$qry = $conn->query($sql);
foreach($qry->fetchArray() as $k => $v){
    $$k = $v;
}
?>
<style>
    #uni_modal .modal-footer{
        display:none;
    }
</style>
<div class="container-fluid">
    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-2 d-flex">
                    <label for="" class="col-auto pe-1">Transaction Code: </label>
                    <div class="col-auto flex-grow-1"><?php echo $transaction_code ?></div>
                </div>
                <div class="mb-2 d-flex">
                    <label for="" class="col-auto pe-1">Status: </label>
                    <div class="col-auto flex-grow-1">
                        <span id="status">
                        <?php if($status == 1): ?>
                            <span class="badge bg-primary"><small>Confirmed</small></span>
                        <?php elseif($status == 2): ?>
                            <span class="badge bg-success"><small>Delivered</small></span>
                        <?php elseif($status == 3): ?>
                            <span class="badge bg-danger"><small>Cancelled</small></span>
                        <?php else: ?>
                            <span class="badge bg-dark text-light"><small>Pending</small></span>
                        <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-2 d-flex">
                    <label for="" class="col-auto pe-1">Date Created: </label>
                    <div class="col-auto flex-grow-1"><?php echo date("Y-m-d h:i A",strtotime($date_created)) ?></div>
                </div>
                <div class="mb-2 d-flex">
                        <label for="" class="col-auto pe-1">Delivery Address: </label>
                        <div class="col-auto flex-grow-1"><?php echo $delivery_address.', ',$location ?></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-hover">
                    <colgroup>
                            <col width="10%">
                            <col width="75%">
                            <col width="15%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="py-0 px-1 text-center">QTY</th>
                            <th class="py-0 px-1 text-center">Item Details</th>
                            <th class="py-0 px-1 text-center">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $qry = $conn->query("SELECT o.*,p.name as pname, p.description,p.price,cc.name as cname FROM `order_items` o inner join `product_list` p on o.product_id = p.product_id inner join `category_list` cc on p.category_id = cc.category_id where o.`order_id` = '{$order_id}'");
                        $total = 0;
                        while($row=$qry->fetchArray()):
                        $total += $row['quantity'] * $row['price'];
                        ?>
                        <tr>
                            <td class="px-1 py-0 text-center"><?php echo number_format($row['quantity']) ?></td>
                            <td>
                                <div class="w-100 d-flex">
                                    <div class="col-auto me-2">
                                        <img src="<?php echo './uploads/thumbnails/'.$row['product_id'].'.png' ?>" alt="" class="img-fluid border border-dark" height="75px" width="75px">
                                    </div>
                                    <div class="col-auto flex-grow-1">
                                        <div><a class="fs-5 text-decoration-none view_product" href="javascript:void(0)" data-id="<?php echo $row['product_id'] ?>"><b><?php echo $row['pname'] ?></b></a>
                                        </div>
                                        <small><i><?php echo $row['cname'] ?></i></small>
                                        <div class="w-100">
                                        Price:&nbsp;<b><i> X <?php echo number_format($row['price'],2) ?></i></b>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end align-middle">
                                    <span class="fs-5"><b class="price_display"><?php echo number_format($row['quantity'] * $row['price'],2) ?></b></span>
                                </td>
                            <?php endwhile; ?>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan='2' class="text-end">Sub Total</th>
                            <th class="text-end fs-5" id="gTotal"><?php echo number_format($total,2) ?></th>
                        </tr>
                        <tr>
                            <th colspan='2' class="text-end">Delivery Fee</th>
                            <th class="text-end fs-5" id="gTotal"><?php echo number_format($amount,2) ?></th>
                        </tr>
                        <tr>
                            <th colspan='2' class="text-end">Total <input type="hidden" name="total" value="<?php echo $total + $row['amount'] ?>"></th>
                            <th class="text-end fs-5" id="gTotal"><?php echo number_format($total + $amount,2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="row justify-content-end">
            <div class="col-1">
                <div class="btn btn btn-dark btn-sm rounded-0" type="button" data-bs-dismiss="modal">Close</div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.view_product').click(function(){
            uni_modal('Product Detailes',"view_product.php?order_id=<?php echo $order_id ?>&id="+$(this).attr('data-id'),"mid-large")
        })
    })
</script>