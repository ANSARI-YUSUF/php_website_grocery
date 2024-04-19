<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Sales Report</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-success btn-sm py-1 rounded-0" type="button" id="print">Print</button>
        </div>
    </div>
    <div class="card-body">
        <div id="outprint">
        <table class="table table-hover table-striped table-bordered">
            <!-- <colgroup>
                <col width="5%">
                <col width="15%">
                <col width="25%">
                <col width="25%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
            </colgroup> -->
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Date Created</th>
                    <th class="text-center p-0">Transaction Code</th>
                    <th class="text-center p-0">Customer</th>
                    <th class="text-center p-0">Items</th>
                    <th class="text-center p-0">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT o.*,c.fullname,f.location, f.amount FROM `order_list` o inner join `customer_list` c on o.customer_id = c.customer_id inner join fees_list f on o.fee_id = f.fee_id where o.status in (1,2) order by strftime('%s',o.`date_created`) asc";
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
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['total_amount']+$row['amount'],2) ?></td>
                </tr>
                <?php endwhile; ?>
               
            </tbody>
        </table>
        </div>
    </div>
</div>
<script>
    $(function(){
       $('#print').click(function(){
           var _h = $('head').clone()
           var data = $('#outprint').clone()
           var el = $('<div>')
           _h.find('title').text('Sales Report Print Preview')
           el.append(_h)
           el.append('<h3 class="text-center">Sales Report</h3><hr/>')
           el.append(data)
           var nw = window.open("","","height=900,width=1200,left=70")
           nw.document.write(el.html())
           nw.document.close()
           setTimeout(() => {
               nw.print()
               setTimeout(() => {
                   nw.close()
               }, 200);
           }, 500);
       }) 
    })
</script>