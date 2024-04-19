
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Stock List</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="20%">
                <col width="50%">
                <col width="15%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Date Added</th>
                    <th class="text-center p-0">Product</th>
                    <th class="text-center p-0">Quantity</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT i.*,p.name FROM `inventory_list` i inner join product_list p on i.product_id = p.product_id where i.type = 1 order by strftime('%s',i.date_created) desc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                        $thumbnail = '../uploads/thumbnails/'.$row['product_id'].'.png';
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                    <td class="py-0 px-1">
                        <div class="w-100 d-flex align-items-center">
                            <div class="col-auto">
                                <img src="<?php echo $thumbnail ?>" alt="img" class="thumbnail-img border rounded broder-light">
                            </div>
                            <div class="col-auto flex-grow-1">
                                <div class="lh-1">
                                    <span class="text-muted">Name: </span><span><?php echo $row['name'] ?></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['quantity']) ?></td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item view_data" data-id = '<?php echo $row['inventory_id'] ?>' href="javascript:void(0)">View</a></li>
                            <li><a class="dropdown-item edit_data" data-id = '<?php echo $row['inventory_id'] ?>' href="javascript:void(0)">Edit</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['inventory_id'] ?>' data-name = '<?php echo $row['name'] ?>' href="javascript:void(0)">Delete</a></li>
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
            uni_modal('Add New Stock',"manage_stock.php",'mid-large')
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Stock Details',"manage_stock.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.view_data').click(function(){
            uni_modal('Stock Details',"view_stock.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })
        $('table td,table th').addClass('align-middle')
        $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:4 }
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