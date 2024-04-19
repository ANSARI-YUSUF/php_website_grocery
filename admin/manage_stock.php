<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `inventory_list` where inventory_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="stock-form">
        <input type="hidden" name="id" value="<?php echo isset($inventory_id) ? $inventory_id : '' ?>">
        <div class="form-group">
            <label for="product_id" class="control-label">Product</label>
            <select name="product_id" id="product_id" class="form-select form-select-sm select2" required>
                <option value="" disabled <?php echo !isset($product_id) ? "selected" : '' ?>></option>
                <?php 
                $product = $conn->query("SELECT * FROM `product_list` where status = 1 ".(isset($product_id) ? " OR product_id = '{$product_id}'" : '')." order by `name` asc ");
                while($row = $product->fetchArray()):
                ?>
                <option value="<?php echo $row['product_id'] ?>" <?php echo isset($product_id) && $product_id == $row['product_id'] ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity" class="control-label">Quantity</label>
            <input type="text" pattern="[0-9]+" name="quantity" autofocus id="quantity" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($quantity) ? $quantity : '' ?>">
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#stock-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'../Actions.php?a=save_stock',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($stock_id) ?>" != 1)
                        _this.get(0).reset();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>