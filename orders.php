
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Order List</h3>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="20%">
                <col width="10%">
                <col width="20%">
                <col width="5%">
                <col width="20%">
                <col width="10%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">DateTime Created</th>
                    <th class="text-center p-0">Transaction Code</th>
                    <th class="text-center p-0">Customer Name</th>
                    <th class="text-center p-0">Items</th>
                    <th class="text-center p-0">Total Amount</th>
                    <th class="text-center p-0">Status</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT o.*,c.fullname FROM `order_list` o inner join `customer_list` c on o.customer_id = c.customer_id order by o.`status` asc,  strftime('%s',o.`date_created`) asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                        $items = $conn->query("SELECT SUM(quantity) as `items` from `order_items` where `order_id` = '{$row['order_id']}'")->fetchArray()['items'];
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                    <td class="py-0 px-1 text-center"><?php echo $row['transaction_code'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['fullname'] ?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($items) ?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['total_amount'],2) ?></td>
                    <td class="py-0 px-1 text-center">
                    <?php if($row['status'] == 1): ?>
                        <span class="badge bg-primary"><small>Confirmed</small></span>
                    <?php elseif($row['status'] == 2): ?>
                        <span class="badge bg-success"><small>Delivered</small></span>
                    <?php elseif($row['status'] == 3): ?>
                        <span class="badge bg-danger"><small>Cancelled</small></span>
                    <?php else: ?>
                        <span class="badge bg-dark text-light"><small>Pending</small></span>
                    <?php endif; ?>
                    </td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item view_data" data-id = '<?php echo $row['order_id'] ?>' href="javascript:void(0)">View</a></li>
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
            uni_modal('Order Details',"view_order.php?id="+$(this).attr('data-id'),'large')
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