<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `product_list` where product_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
$thumbnail = '../uploads/thumbnails/'.$product_id.'.png';
$scan = scandir('../uploads/images/'.$product_id.'/');
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
                        <img src="<?php echo $thumbnail ?>" id="selected-image" alt="Img" class="display-image image-fluid border-dark">
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
                    <div class="col-auto m-1 position-relative img-item">
                        <a href="javascript:void(0)" class="select-img border border-dark d-block">
                            <img src="<?php echo '../uploads/images/'.$product_id.'/'.$img ?>" alt="Img" class="display-select-image img-fluid" />
                        </a>
                        <span class="position-absolute img-del-btn"><button class="btn btn-sm btn-danger rounded-0 p-0"  data-path="<?php echo '/uploads/images/'.$product_id.'/'.$img ?>"><i class="fa fa-times" type="button"></i></button></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="fs-4 pb-3"><?php echo $name ?></div>
                <hr>
                <div>Price: <?php echo number_format($price,2) ?> <i class="fa fa-tag text-success"></i></div>
                <p class="py-3"><?php echo str_replace("\n\r","<br/>",$description) ?></p>
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
        $('.select-img').click(function(){
            var imgPath = $(this).find('img').attr('src')
            $('#selected-image').attr('src',imgPath)
        })
        $('.img-del-btn>.btn').click(function(e){
            e.preventDefault()
            _conf("Are you sure to delete this product image?","delete_img",["'"+$(this).attr('data-path')+"'"])
        })
        if('<?php echo isset($_GET['order_id']) ?>' == 1){
            $('#uni_modal').on('hidden.bs.modal',function(){
                if($('#uni_modal #product-details').length > 0)
                uni_modal('Order Details',"view_order.php?id=<?php echo isset($_GET['order_id']) ? $_GET['order_id'] : '' ?>",'large')
            })
        }
    })
    function delete_img($path){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:"../Actions.php?a=delete_img",
            method:"POST",
            data:{path:$path},
            dataType:'json',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
            },
            success:function(resp){
                if(resp.status == 'success'){
                    $('.img-del-btn>.btn[data-path="'+$path+'"]').closest('.img-item').remove()
                    $('#confirm_modal').modal('hide')
                }else{
                    console.log(resp)
                    alert("An error occurred.")
                }
            $('#confirm_modal button').attr('disabled',false)
            }
        })
    }
</script>