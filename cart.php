<h3>Cart</h3>
<hr>
<div class="col-lg-12">
    <div class="w-100">
        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-hover table-stripped">
                    <colgroup>
                        <col width="5%">
                        <col width="75%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Item Details</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $qry = $conn->query("SELECT c.*,p.name as pname, p.description,p.price,cc.name as cname FROM `cart_list` c inner join `product_list` p on c.product_id = p.product_id inner join `category_list` cc on p.category_id = cc.category_id where c.`customer_id` = '{$_SESSION['customer_id']}'");
                        $total = 0;
                        while($row=$qry->fetchArray()):
                        $total += $row['quantity'] * $row['price'];
                        ?>
                        <tr class="item" data-id="<?php echo $row['product_id'] ?>">
                            <td class="text-center align-middle">
                                <button class="btn btn-sm btn-danger rounded-0 del_item" button="button" data-id="<?php echo  $row['product_id'] ?>"><span class="fa fa-trash"></span></button>
                            </td>
                            <td>
                                <div class="w-100 d-flex">
                                    <div class="col-auto me-2">
                                        <img src="<?php echo './uploads/thumbnails/'.$row['product_id'].'.png' ?>" alt="" class="img-fluid border border-dark" height="75px" width="75px">
                                    </div>
                                    <div class="col-auto flex-grow-1">
                                        <div class="fs-5"><b><?php echo $row['pname'] ?></b></div>
                                        <small><i><?php echo $row['cname'] ?></i></small>
                                        <div class="d-flex w-100">
                                            <div class="col-auto">
                                                <input type="number" value="<?php echo number_format($row['quantity'],0,'.','') ?>" class="form-control form-control-sm rounded-0 py-0 qty text-center col-1" style="width:100px" required>
                                                <input type="hidden" value="<?php echo $row['price'] ?>" class="price">
                                                <input type="hidden" value="<?php echo $row['product_id'] ?>" class="product_id">
                                            </div>
                                            <div class="col-auto ms-1">
                                                <b><i> X <?php echo number_format($row['price'],2) ?></i></b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end align-middle">
                                <span class="fs-5"><b class="price_display"><?php echo number_format($row['quantity'] * $row['price'],2) ?></b></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if(!$qry->fetchArray()): ?>
                            <tr>
                                <th class="text-center" colspan='3'>Cart is empty. <a href="./">Explore Products</a></th>
                            </tr>
                        <?php endif; ?>
                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan='2' class="text-center">Total <input type="hidden" name="total" value="<?php echo $total ?>"></th>
                            <th class="text-end fs-5" id="gTotal"><?php echo number_format($total,2) ?></th>
                        </tr>
                    </tfoot>
                </table>
                <center><button class="btn btn-dark btn-sm rounded-0" style="display:none" type="button" id="checkout">Checkout</button></center>

            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        if($('.item').length < 0){
            $('#checkout').hide();
        }else{
            $('#checkout').show();
        }
        $('#checkout').click(function(){
            uni_modal("Checkout","place_order.php?total="+$('[name="total"]').val(),'mid-large')
        })
        $('.qty').change(function(){
            var _qty = $(this).val()
            var _this = $(this)
                $(this).removeClass('boreder-danger')
            if($.isNumeric(_qty) === false){
                $(this).addClass('boreder-danger')
            }else{
                var _price = $(this).siblings('.price').val()
                var product_id = $(this).siblings('.product_id').val()
                console.log(product_id)
                var total = parseFloat(_price) * parseFloat(_qty)
                $(this).closest('tr').find('.price_display').text(parseFloat(total).toLocaleString('en-US',{ style:'decimal',minimumFractionDigits:2,maximumFractionDigits:2}))
                $.ajax({
                    url:"Actions.php?a=update_cart",
                    method:"POST",
                    data:{customer_id:"<?php echo $_SESSION['customer_id'] ?>",product_id:product_id,quantity:_qty},
                    dataType:'JSON',
                    error:err=>{
                        console.log(err)
                        alert("An error occured")
                    },
                    success:function(resp){
                        if(resp.status == 'success'){
                            $('#cart_count').text(resp.cart_count)
                            calc()
                        }else if(!!resp.msg && !!resp.available){
                            alert(resp.msg)
                            _this.val(resp.available).trigger('change')
                        }else{
                            alert("An error occured")
                        }
                    }
                })
            }
        })
        $('.del_item').click(function(){
            _conf("Are you sure to remove this Item from cart list?",'delete_data',[$(this).attr('data-id')])
        })
    })
    function calc(){
        var total = 0;
        $('.price_display').each(function(){
            var _price = $(this).text().replace(/,/gi,'')
            total += parseFloat(_price)
        })
        $('#gTotal').text(parseFloat(total).toLocaleString('en-US',{ style:'decimal',minimumFractionDigits:2,maximumFractionDigits:2}))
    }
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'Actions.php?a=delete_from_cart',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    $('#cart_count').text(resp.cart_count)
                    calc()
                    $('table tr.item[data-id="'+$id+'"]').remove()
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>