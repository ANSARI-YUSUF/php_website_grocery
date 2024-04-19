<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `customer_list` where customer_id = '{$_GET['id']}'");
        foreach($qry->fetchArray() as $k => $v){
            $$k = $v;
        }
    }
?>
<div class="container-fluid">
<form action="" id="register-form">
    <input type="hidden" name="id" value="<?php echo isset($customer_id)? $customer_id : '' ?>">
    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fullname" class="control-label">Full Name</label>
                    <input type="text" name="fullname" id="fullname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($fullname)? $fullname : '' ?>">
                </div>
                <div class="form-group">
                    <label for="email" class="control-label">Email</label>
                    <input type="email" name="email" id="email" required class="form-control form-control-sm rounded-0" value="<?php echo isset($email)? $email : '' ?>">
                </div>
                <div class="form-group">
                    <label for="contact" class="control-label">Contact</label>
                    <input type="text" name="contact" id="contact" required class="form-control form-control-sm rounded-0" value="<?php echo isset($contact)? $contact : '' ?>">
                </div>
                <div class="form-group">
                    <label for="address" class="control-label">Address</label>
                    <textarea rows="2" name="address" id="address" required class="form-control form-control-sm rounded-0"><?php echo isset($address)? $address : '' ?></textarea>
                </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="username" name="username" id="username" required class="form-control form-control-sm rounded-0" value="<?php echo isset($username)? $username : '' ?>">
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" name="password" id="password" required class="form-control form-control-sm rounded-0" value="">
                    <?php if(isset($password)): ?>
                        <small class="text-info"><i>Leave this blank if you don't wish to update the password.</i></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>
</div>

<script>
    $(function(){
        $('#register-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'../Actions.php?a=save_customer',
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
                        if("<?php echo isset($customer_id) ?>" != 1)
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