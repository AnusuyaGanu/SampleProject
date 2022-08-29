<html>
    <head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

</head>
</html>

<?php 
 include 'dbConfig.php';

// Include database configuration file 
require_once 'dbConfig.php'; 
 

//Get distinct category names for Category dropdown

$sql_category = "SELECT distinct(category_name) as category FROM stock_det";
$stmt = sqlsrv_query( $conn, $sql_category );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$query_category = sqlsrv_query($conn,$sql_category); 

//Get distinct item names for Items dropdown

$sql_item = "SELECT distinct(item_name) as item FROM stock_det";
$stmt1 = sqlsrv_query( $conn, $sql_item );
if( $stmt1 === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$query_item = sqlsrv_query($conn,$sql_item); 
?>

<div class="row">
<div class="col-md-12 head">
<h5 style="text-align:center">Stock Report</h5>
</div>
<div class="col-md-4 "></div>
<div class="col-md-8 ">
    <form action="index.php" method="post">

            <select name="category" id="category">
            <option value="" selected="selected">Category</option>
            <?php
                while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
                    echo $row['category']."<br />"; ?>
                    <option value="<?= $row['category'] ?>"><?php echo $row['category'] ?></option>
            <?php } ?>
            </select> 
            
            <select name="item" id="item">
            <option value="" selected="selected">Item</option>
            <?php
                while( $row = sqlsrv_fetch_array( $stmt1, SQLSRV_FETCH_ASSOC) ) {
                    echo $row['item']."<br />"; ?>
                    <option value="<?= $row['item'] ?>"><?php echo $row['item'] ?></option>
            <?php } ?>
            </select> 
            <select name="stock" id="stock">
            <option value="" selected="selected">Stock</option>
            <option value="nil_stock" name="nil_stock">Nil Stock</option>

            </select> 

            <input name="search" type="submit" value="Search"/>

    </form>


    </div>
    
    <!-- List the members -->
    <table class="table table-striped table-bordered" style="margin-left: 40px;">
        <thead class="thead-dark">
            <tr>
                <th>S.No</th>
                <th>Category Name</th>
                <th>Item Name</th>
                <th>Item Code</th>
                <th>Supplier</th>
                <th>Available Unit</th>
                <th>Low level</th>
                <th>High level</th>
                <th>Price Unit</th>
                <th>Rack no</th>
                <th>Purchase Price</th>
                <th>Tax</th>
                <th>NPR</th>
                <th>Item Type</th>
                <th>MRP</th>
                <th>Unit</th>
                <th>Base Unit</th>
                <th>Conv Unit</th>
                <th>Conv</th>
                <th>HSN</th>
                <th>Stock Value</th>
            </tr>
        </thead>
        <tbody>
                    <?php 
                    // Fetch the data from SQL server 
                    $sql = "SELECT * FROM stock_det "; 
                    $category = '';
                    
                    if(isset($_POST)) {

                       if(isset($_POST['category']) &&(!empty($_POST['category']))){
                        $category = $_POST['category'];
                         $sql = "SELECT * FROM stock_det where category_name= '$category'"; 
                       } 
                       if(isset($_POST['item']) &&(!empty($_POST['item']))){
                        $item = $_POST['item'];
                         $sql = "SELECT * FROM stock_det where item_name= '$item'"; 
                       } 
                       if(isset($_POST['stock']) &&(!empty($_POST['stock']))){
                        $stock = $_POST['stock'];
                         $sql = "SELECT * FROM stock_det where avl_unit= 0"; 
                       }                     

                    }

                    $query = sqlsrv_query($conn,$sql); 

                    $count = 0;
                    $summary_total = 0;
                    while( $row = sqlsrv_fetch_array( $query, SQLSRV_FETCH_ASSOC) ) {
                    $count++;
                    $stock_value = $row['purchase_price'] * $row['avl_unit'];
                    $summary_total  = $summary_total + $stock_value;
                    ?>                   
                    <tr>
                        <td><?php echo $count; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><?php echo $row['item_name']; ?></td>
                        <td><?php echo $row['item_code']; ?></td>
                        <td><?php echo $row['supplier']; ?></td>
                        <td><?php echo $row['avl_unit']; ?></td>
                        <td><?php echo $row['low_level']; ?></td>
                        <td><?php echo $row['high_level']; ?></td>
                        <td><?php echo $row['price_unit']; ?></td>
                        <td><?php echo $row['rack_no']; ?></td>
                        <td><?php echo $row['purchase_price']; ?></td>
                        <td><?php echo $row['TAX']; ?></td>
                        <td><?php echo $row['NPR']; ?></td>
                        <td><?php echo $row['ITEM_TYPE']; ?></td>
                        <td><?php echo $row['MRP']; ?></td>
                        <td><?php echo $row['UNIT']; ?></td>
                        <td><?php echo $row['BASEUNIT']; ?></td>
                        <td><?php echo $row['CONVUNIT']; ?></td>
                        <td><?php echo $row['CONV']; ?></td>
                        <td><?php echo $row['HSN']; ?></td>
                        <td><?php echo $stock_value; ?></td>
                       
                    </tr>
                    
                 <?php }  ?>
                <tr>
                    <td colspan="19"></td>
                    <td> <?php echo "Summary" ?></td>
                    <td> <?php echo $summary_total; ?></td>
                </tr>
        </tbody>
    </table>
</div>