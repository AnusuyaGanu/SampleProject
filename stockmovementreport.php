<html>
    <head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

</head>
</html>

<?php 
 include 'dbConfig.php';

// Include database configuration file 
require_once 'dbConfig.php'; 
 

$earliestdate_query = "SELECT MIN(P_DATE) as earliestDate FROM STOCKINOUT";
$query1 = sqlsrv_query($conn,$earliestdate_query); 

    while( $row = sqlsrv_fetch_array( $query1, SQLSRV_FETCH_ASSOC) ) {
        $e_date = $row['earliestDate'];
    }

$oldestdate_query = "SELECT MAX(P_DATE) as oldestDate FROM STOCKINOUT" ;
$query2 = sqlsrv_query($conn,$oldestdate_query); 

    while( $row = sqlsrv_fetch_array( $query2, SQLSRV_FETCH_ASSOC) ) {
        $o_date = $row['oldestDate'];
    }




//Get distinct item names for Table

$sql_item = "SELECT distinct(sd.item_name)as item from   stock_det sd  group by sd.item_name";
$stmt1 = sqlsrv_query( $conn, $sql_item );
if( $stmt1 === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$query_item = sqlsrv_query($conn,$sql_item); 
?>

<div class="row">
<div class="col-md-12 head">
<h5 style="text-align:center">Stock Movement Report</h5>

</div>
<div class="col-md-4 "></div>
<div class="col-md-8 ">
    <form action="stockmovementreport.php" method="post">

    <label for="from_date">From</label>
    <input type="date" id="from_date" name="from_date">


    <label for="to_date">To</label>
    <input type="date" id="to_date" name="to_date">

    <input name="search" type="submit" value="Search"/>

    </form>


    </div>

    <!-- List the members -->
    <table class="table table-striped table-bordered" style="margin-left: 40px;">
        <thead class="thead-dark">
            <tr>
                <th>S.No</th>
                <th>Item Name</th>
                <th>OS</th>
                <th>Purchase</th>
                <th>Sales Return</th>
                <th>Inward Total</th>
                <th>Sales</th>
                <th>Purchase Return</th>
                <th>Outward Total</th>
               
                
            </tr>
        </thead>
        <tbody>
                    <?php 
                    // Check if the post data is set 
                  
                    if((isset($_POST)) &&(!empty($_POST['from_date']))&&(!empty($_POST['from_date']))) {
                       if(isset($_POST['from_date']) &&(isset($_POST['to_date']))){
                        $from_date = $_POST['from_date'];
                        $to_date = $_POST['to_date'];


                       } 
                    }else{
                        $from_date = $e_date->format('Y-m-d');
                        $to_date = $o_date->format('Y-m-d');
                    }

                    // print_r($from_date);
                    // print_r($to_date);exit;

                  

                    $count = 0;
                    $summary_total = 0;
                    
                    while( $row = sqlsrv_fetch_array( $query_item, SQLSRV_FETCH_ASSOC) ) {
                    $count++;
                 
                    ?>    
               
                    <tr>
                        <td><?php echo $count; ?></td> 

                        <td>
                            <?php
                            echo $row['item']."<br />"; ?>
                        </td>
                        <td>
                            <?php
                             $item_name = $row['item'];
                             $os = "SELECT sum(SIN - SOUT) as os from  [dbo].[STOCKINOUT] where item_name = '$item_name' AND P_DATE < '$from_date' ";
                             $os_value = sqlsrv_query($conn,$os); 
                             while( $row2 = sqlsrv_fetch_array( $os_value, SQLSRV_FETCH_ASSOC) ) {
                                $os_total = $row2['os'];
                             echo $row2['os']."<br />";} 
                             sqlsrv_free_stmt(  $os_value);
                             ?>
                             
                        </td>


                        <td>
                            <?php
                          
                             //purchase
                             $purchase = "SELECT sum(SIN) as purchase from  [dbo].[STOCKINOUT] where PARTICULARS = 'PURCHASE' and item_name= '$item_name' and P_DATE between '$from_date' and '$to_date'";
                             $purchase_value = sqlsrv_query($conn,$purchase); 
                             if( $purchase_value === false ) {
                                die( print_r( sqlsrv_errors(), true));
                             }
                             while( $row2 = sqlsrv_fetch_array( $purchase_value, SQLSRV_FETCH_ASSOC) ) {
                                $purchase_total = $row2['purchase'];
                                echo $row2['purchase']."<br />";} 
                                sqlsrv_free_stmt( $purchase_value);
                            ?>
                         </td>


                        <td>
                        <?php
                            //sales return
                             $salesreturn = "SELECT sum(SIN) as salesreturn from  [dbo].[STOCKINOUT] where PARTICULARS = 'SALES RETURN' and item_name= '$item_name' and P_DATE between '$from_date' and '$to_date'";
                             $salesreturn_value = sqlsrv_query($conn,$salesreturn); 
                             if( $salesreturn_value === false ) {
                                die( print_r( sqlsrv_errors(), true));
                             }
                             while( $row2 = sqlsrv_fetch_array( $salesreturn_value, SQLSRV_FETCH_ASSOC) ) {
                                $salesreturn_total =  $row2['salesreturn'];
                                echo $row2['salesreturn']."<br />";} 
                                sqlsrv_free_stmt( $salesreturn_value);
                                ?>
                        </td>

                        <td>
                            <?php
                            $inward = $os_total + $purchase_total + $salesreturn_total ;
                            echo $inward;
                            ?>
                        </td>
                        <td>
                            <?php
                         
                             //sales
                             $sales = "SELECT sum(SOUT) as sales from  [dbo].[STOCKINOUT] where PARTICULARS = 'sales' and item_name= '$item_name' and P_DATE between '$from_date' and '$to_date'";
                             $sales_value = sqlsrv_query($conn,$sales); 
                             if( $sales_value === false ) {
                                die( print_r( sqlsrv_errors(), true));
                             }
                             while( $row2 = sqlsrv_fetch_array( $sales_value, SQLSRV_FETCH_ASSOC) ) {
                                $sales_total = $row2['sales'];
                                echo $row2['sales']."<br />";} 
                                sqlsrv_free_stmt(  $sales_value );
                            ?>
                         </td>


                        <td>
                        <?php
                            //purchase return
                             $purchasereturn = "SELECT sum(SOUT) as purchasereturn from  [dbo].[STOCKINOUT] where PARTICULARS = 'purchase return' and item_name= '$item_name' and P_DATE between '$from_date' and '$from_date'";
                             $purchasereturn_value = sqlsrv_query($conn,$purchasereturn); 
                             if( $purchasereturn_value === false ) {
                                die( print_r( sqlsrv_errors(), true));
                             }
                             while( $row2 = sqlsrv_fetch_array( $purchasereturn_value, SQLSRV_FETCH_ASSOC) ) {
                                $purchasereturn_total =  $row2['purchasereturn'];
                                echo $row2['purchasereturn']."<br />";}
                                sqlsrv_free_stmt($purchasereturn_value);?>
                        </td>

                        <td>
                            <?php
                            $outward = $sales_total + $purchasereturn_total ;
                            echo $outward;
                            ?>
                        </td>
                        
                    </tr>
                    <?php } ?>                    

                <tr>
                    <td colspan="19"></td>
                  
                </tr>
                <h5 style="margin-left:500px">Results showing from   <?php echo $from_date ?> to <?php echo $to_date ?></h5>

        </tbody>
    </table>
</div>