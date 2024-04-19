<style>
    #uni_modal .modal-footer{
        display:none;
    }
</style>
<?php
session_start();
require_once("DBConnection.php");
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
        <form action="" id="place-order">
            <input type="hidden" name="total_amount" value="<?php echo $_GET['total'] ?>">
            <div class="form-group">
                <label for="fee_id" class="control-label">Delivery Location</label>
                <select name="fee_id" id="fee_id" class="form-select form-select-sm select2" required>
                    <option value="" disabled <?php echo !isset($fee_id) ? "selected" : '' ?>></option>
                    <?php 
                    $fees = $conn->query("SELECT * FROM `fees_list` where status = 1 ".(isset($fee_id) ? " OR fee_id = '{$fee_id}'" : '')." order by `location` asc ");
                    while($row = $fees->fetchArray()):
                    ?>
                    <option value="<?php echo $row['fee_id'] ?>" data-amount = "<?php echo $row['amount'] ?>" <?php echo isset($fee_id) && $fee_id == $row['fee_id'] ? "selected" : "" ?>><?php echo $row['location'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="" class="control-label">Delivery Address Other Information</label>
                <textarea name="delivery_address" id="delivery_address" cols="30" rows="3" class="form-control rounded-0" placeholder="ie. Lot 23 Block 6, There Ville"></textarea>
            </div>
        
        </form>
        </div>
        <div class="col-md-6">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th>Sub-Total</th>
                        <th class="text-end" id="csub-total"><?php echo $_GET['total'] ?></th>
                    </tr>
                    <tr>
                        <th>Delivery Fee</th>
                        <th class="text-end" id="cfee">0</th>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <th class="text-end" id="ctotal"><?php echo $_GET['total'] ?></th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-12 mt-3 text-center ">
                <button class="btn btn-sm btn-primary rounded-0 my-1" form="place-order">Place Order</button>
                <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Cancel</button>
        </div>
    </div>
    
</div>
<script>
    
    $(function(){
        $('#fee_id').change(function(){
            var fee_id = $(this).val()
            var amount = $('#fee_id option[value="'+fee_id+'"]').attr('data-amount')
            var sub = $('#csub-total').text().replace(/\,/gi,'')
            var total = parseFloat(sub) + parseFloat(amount);
            $('#cfee').text(parseFloat(amount).toLocaleString('en-US'))
            $('#ctotal').text(parseFloat(total).toLocaleString('en-US'))
        })
        $('#place-order').submit(function(e){
            e.preventDefault();
            if($('#fee_id').val() <= 0){
                alert("Please select location address first.");
                $('#fee_id').focus()
                return false;
            }
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            $.ajax({
                url:'Actions.php?a=place_order',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.replace("./")
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>