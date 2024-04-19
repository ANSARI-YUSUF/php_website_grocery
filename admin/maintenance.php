
<style>
    .card-panel{
        overflow:auto !important;
        height:68vh !important;
    }
</style>
<div class="card h-100 d-flex flex-column">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Maintenance</h3>
        <div class="card-tools align-middle">
            <!-- <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button> -->
        </div>
    </div>
    <div class="card-body flex-grow-1">
        <div class="col-12 h-100">
            <div class="row h-100">
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>Category List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_category" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add category"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark card-panel">
                        <ul class="list-group">
                            <?php 
                            $dept_qry = $conn->query("SELECT * FROM `category_list` order by `name` asc");
                            while($row = $dept_qry->fetchArray()):
                            ?>
                            <li class="list-group-item d-flex">
                                <div class="col-auto flex-grow-1">
                                    <?php echo $row['name'] ?>
                                </div>
                                <div class="col-auto">
                                    <?php if($row['status'] == 1): ?>
                                        <a href="javascript:void(0)" class="update_stat_cat badge bg-success bg-gradiend rounded-pill text-decoration-none me-1" title="Update Status" data-toStat = "0" data-id="<?php echo $row['category_id'] ?>" data-name="<?php echo $row['name'] ?>"><small>Active</small></a>
                                        <?php else: ?>
                                        <a href="javascript:void(0)" class="update_stat_cat badge bg-secondary bg-gradiend rounded-pill text-decoration-none me-1" title="Update Status" data-toStat = "1" data-id="<?php echo $row['category_id'] ?>"  data-name="<?php echo $row['name'] ?>"><small>Inactive</small></a>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_category btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit category Details" data-id="<?php echo $row['category_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-edit"></span></a>

                                    <a href="javascript:void(0)" class="delete_category btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete category" data-id="<?php echo $row['category_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if(!$dept_qry->fetchArray()): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>Delivery Fee List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_fee" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add fee"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark card-panel">
                        <ul class="list-group">
                            <?php 
                            $dept_qry = $conn->query("SELECT * FROM `fees_list` order by `location` asc");
                            while($row = $dept_qry->fetchArray()):
                            ?>
                            <li class="list-group-item d-flex">
                                <div class="col-auto flex-grow-1">
                                    <div class="w-100 d-flex">
                                    <span class="col-auto flex-grow-1">
                                        <?php echo $row['location'] ?>
                                    </span>
                                    <span class="col-auto col-auto px-2 text-muted"><?php echo number_format($row['amount'],2) ?></span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <?php if($row['status'] == 1): ?>
                                        <a href="javascript:void(0)" class="update_stat_fee badge bg-success bg-gradiend rounded-pill text-decoration-none me-1" title="Update Status" data-toStat = "0" data-id="<?php echo $row['fee_id'] ?>" data-name="<?php echo $row['location'] ?>"><small>Active</small></a>
                                        <?php else: ?>
                                        <a href="javascript:void(0)" class="update_stat_fee badge bg-secondary bg-gradiend rounded-pill text-decoration-none me-1" title="Update Status" data-toStat = "1" data-id="<?php echo $row['fee_id'] ?>"  data-name="<?php echo $row['location'] ?>"><small>Inactive</small></a>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_fee btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit fee Details" data-id="<?php echo $row['fee_id'] ?>"  data-name="<?php echo $row['location'] ?>"><span class="fa fa-edit"></span></a>

                                    <a href="javascript:void(0)" class="delete_fee btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete fee" data-id="<?php echo $row['fee_id'] ?>"  data-name="<?php echo $row['location'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if(!$dept_qry->fetchArray()): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#new_category').click(function(){
            uni_modal('Add New Category',"manage_category.php")
        })
        $('.edit_category').click(function(){
            uni_modal('Edit Category Details',"manage_category.php?id="+$(this).attr('data-id'))
        })
        $('.update_stat_cat').click(function(){
            var changeTo = $(this).attr('data-toStat') == 1 ? "Active" : "Inactive";
            _conf("Are you sure to change status of Category <b>"+$(this).attr('data-name')+"</b> to "+changeTo+"?",'update_stat_cat',[$(this).attr('data-id'),$(this).attr('data-toStat')])
        })
        $('.delete_category').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Category List?",'delete_category',[$(this).attr('data-id')])
        })

        $('#new_fee').click(function(){
            uni_modal('Add New Fee',"manage_fee.php")
        })
        $('.edit_fee').click(function(){
            uni_modal('Edit Fee Details',"manage_fee.php?id="+$(this).attr('data-id'))
        })
        $('.update_stat_fee').click(function(){
            var changeTo = $(this).attr('data-toStat') == 1 ? "Active" : "Inactive";
            _conf("Are you sure to change status the Delivery Fee of <b>"+$(this).attr('data-name')+"</b> to "+changeTo+"?",'update_stat_fee',[$(this).attr('data-id'),$(this).attr('data-toStat')])
        })
        $('.delete_fee').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Fee List?",'delete_fee',[$(this).attr('data-id')])
        })
       
        $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:6 }
            ]
        })
    })
    function update_stat_cat($id,$status){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'../Actions.php?a=update_stat_cat',
            method:'POST',
            data:{id:$id,status:$status},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function update_stat_fee($id,$status){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'../Actions.php?a=update_stat_fee',
            method:'POST',
            data:{id:$id,status:$status},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function delete_category($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'../Actions.php?a=delete_category',
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
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function delete_fee($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'../Actions.php?a=delete_fee',
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
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>