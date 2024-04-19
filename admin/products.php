
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Product List</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="15%">
                <col width="25%">
                <col width="25%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Date Updated</th>
                    <th class="text-center p-0">Info</th>
                    <th class="text-center p-0">Description</th>
                    <th class="text-center p-0">Stock Available</th>
                    <th class="text-center p-0">Status</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT p.*,c.name as category FROM `product_list` p inner join category_list c on p.category_id = c.category_id order by p.`name` asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                        $in = $conn->query("SELECT SUM(quantity) as `in` from inventory_list where product_id = '{$row['product_id']}' and `type` = 1")->fetchArray()['in'];
                        $out = $conn->query("SELECT SUM(quantity) as `out` from inventory_list where product_id = '{$row['product_id']}' and `type` = 2")->fetchArray()['out'];
                        $row['available'] = $in - $out;
                        $thumbnail = '../uploads/thumbnails/'.$row['product_id'].'.png';
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo date("Y-m-d H:i",strtotime($row['date_updated'])) ?></td>
                    <td class="py-0 px-1">
                        <div class="w-100 d-flex align-items-center">
                            <div class="col-auto">
                                <img src="<?php echo $thumbnail ?>" alt="img" class="thumbnail-img border rounded broder-light">
                            </div>
                            <div class="col-auto flex-grow-1">
                                <div class="lh-1 w-100 text-break">
                                    <span class=""><?php echo $row['name'] ?></span><br>
                                    <span class=""><?php echo $row['category'] ?></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="py-0 px-1"  title="<?php echo $row['description'] ?>"><p class="m-0 truncate-1"><small><i><?php echo $row['description'] ?></i></small></p></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['available']) ?></td>
                    <td class="py-0 px-1 text-center">
                    <?php if($row['status'] == 1): ?>
                        <span class="badge bg-success"><small>Active</small></span>
                    <?php else: ?>
                        <span class="badge bg-danger"><small>Inactive</small></span>
                    <?php endif; ?>
                    </td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item view_data" data-id = '<?php echo $row['product_id'] ?>' href="javascript:void(0)">View</a></li>
                            <li><a class="dropdown-item edit_data" data-id = '<?php echo $row['product_id'] ?>' href="javascript:void(0)">Edit</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['product_id'] ?>' data-name = '<?php echo $row['employee_code']." - ".$row['name'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </th>
                </tr>
                <?php endwhile; ?>
               
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#create_new').click(function(){
            uni_modal('Add New Product',"manage_product.php",'mid-large')
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Product Details',"manage_product.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.view_data').click(function(){
            uni_modal('Product Details',"view_product.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })
        $('table td,table th').addClass('align-middle')
        $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:6 }
            ]
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'../Actions.php?a=delete_transaction',
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