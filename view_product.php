<?php
session_start();
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `product_list` where product_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
    $in = $conn->query("SELECT SUM(quantity) as `in` from inventory_list where product_id = '{$product_id}' and `type` = 1")->fetchArray()['in'];
    $out = $conn->query("SELECT SUM(quantity) as `out` from inventory_list where product_id = '{$product_id}' and `type` = 2")->fetchArray()['out'];
    $available = $in - $out;
}
$thumbnail = 'uploads/thumbnails/'.$product_id.'.png';
$scan = scandir('uploads/images/'.$product_id.'/');
unset($scan[0]);
unset($scan[1]);
?>
<style>
    #uni_modal .modal-footer{
        display:none;
    }
</style>

<div class="container-fluid" id="product-details">
    <div class="col-12">
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-12">
                        <img src="<?php echo $thumbnail ?>" id="selected-image" alt="Img" class="display-image image-fluid border-dark border">
                    </div>
                </div>
                <div class="d-flex flex-nowrap w-100 overflow-auto my-2">
                    <div class="col-auto m-1">
                        <a href="javascript:void(0)" class="select-img border border-dark d-block">
                            <img src="<?php echo $thumbnail ?>" alt="Img" class="display-select-image img-fluid" />
                        </a>
                    </div>
                    <?php 
                        foreach($scan as $img):
                    ?>
                    <div class="col-auto m-1 img-item">
                        <a href="javascript:void(0)" class="select-img border border-dark d-block">
                            <img src="<?php echo 'uploads/images/'.$product_id.'/'.$img ?>" alt="Img" class="display-select-image img-fluid" />
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="fs-4 pb-3"><?php echo $name ?></div>
                <hr>
                <div>Price: <?php echo number_format($price,2) ?> <i class="fa fa-tag text-success"></i></div>
                <div>Available: <?php echo number_format($available) ?></div>
                <p class="py-3"><?php echo str_replace("\n\r","<br/>",$description) ?></p>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="row justify-content-end">
        <?php if(!isset($_GET['order_id'])): ?>
            <div class="col-auto mx-1">
                <div class="btn btn btn-primary btn-sm rounded-0" id="add_to_cart" type="button">Add to Cart</div>
            </div>
            <?php endif; ?>
            <div class="col-1">
                <div class="btn btn btn-dark btn-sm rounded-0" type="button" data-bs-dismiss="modal">Close</div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.select-img').click(function(){
            var imgPath = $(this).find('img').attr('src')
            $('#selected-image').attr('src',imgPath)
        })
        if('<?php echo isset($_GET['order_id']) ?>' == 1){
            $('#uni_modal').on('hidden.bs.modal',function(){
                if($('#uni_modal #product-details').length > 0)
                uni_modal('Order Details',"view_order.php?id=<?php echo isset($_GET['order_id'])? $_GET['order_id'] : '' ?>",'large')
            })
        }
        $('#add_to_cart').click(function(){
            $('#uni_modal button').attr('disabled',true)
            if('<?php echo isset($_SESSION['customer_id']) && $_SESSION['customer_id'] > 0 ?>' == 1){
                $.ajax({
                    url:"Actions.php?a=add_to_cart",
                    method:"POST",
                    data:{product_id:'<?php echo $product_id ?>',quantity:1},
                    dataType:'JSON',
                    error:err=>{
                        alert("An error occurred")
                        $('#uni_modal button').attr('disabled',false)
                    },
                    success:function(resp){
                        if(resp.status == "success"){
                            $('#cart_count').text(resp.cart_count)
                            alert("Product Added to cart");
                        }else{
                        alert("An error occurred")
                        }
                        $('#uni_modal button').attr('disabled',false)
                    }
                })
            }else{
                uni_modal('Please Enter your Login Credentials',"login.php")
            }
        })
    })
   
</script>