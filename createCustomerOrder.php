<html>
<head>
<title>MPC - Create a New Order</title>


<style>

h6 {
color: 4d4d4d;
   width: 90%; 
   text-align: left; 
   border-bottom: 1px solid #018DB1; 
   line-height: 0.1em;
   margin: 10px 0 10px; 
} 

div.wrapper {
    text-align: center;
}
div.b {
	font-size: 9px;
	color: #018DB1;
	text-indent: 100px;
}
h6 span {
	background:#fff;
	padding: 0 40px;
	length:10px;
}
table {
    border-collapse: collapse;
	table-layout: fixed;
}

th,td {
text-align: center;
align: center;
    padding: 8px;
}
tr:nth-child(even) {background-color: #f2f2f2;}
tr:hover {background-color: #bfbfbf;}
th {
    background-color: #018DB1;
    color: white;
font-size: 16px;
font-weight: normal;
}
</style>
</head>
<body>
<?php
include ("AIPHeader.php");
include ("conn.php");

$sqll="select id, customername from Customers";



session_start();
 if(empty($_SESSION['count'])) $_SESSION['count'] = 0;
 $order =  $_SESSION['count']+1;
 $_SESSION['count'] =  $order;



echo "<div class='content'>";
echo '<h6><span>Create Customer Order</span></h6><br>';
echo '<form name="createcustomerorder" id="createcustomerorder"
        action="createCustomerOrder.php" method="post">';
 echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo "<label for='customerSelection'>Select a Customer &nbsp;&nbsp;</label>";
        echo '<select name="customerSelection" required="required">';
        echo "<option value='' selected disabled>Select an customer</option>";

	foreach ($conn->query($sqll) as $row)
        {
                echo '<option value="';
                echo $row["id"];
                echo '">';
                echo $row["customername"];
                echo '</option>';
        }
echo '</select>&nbsp;&nbsp;';
echo '<br>';
echo '<br>';



$query = "SELECT * FROM Items";
echo '<h6><span>Select Items</span></h6><br>';
echo "<table style='width:880px' align='center'>";
echo "<tr><th>" . "Select" . "</th>". "<th>" ."Name" . "</th><th>" ."Description" . "</th><th>" ."Price". "</th>" . "<th>" . "Quantity" . "</th></tr>";
foreach($conn->query($query) as $row)
{
echo "<tr>" . "<td>" . "<input type='checkbox' id='itemSelect' name='itemSelect[]' value=$row[id]  >" . "</td>" . "<td>" . $row['name'] . "</td><td>" . $row['description'] . "</td><td>\$" . $row['price'] . "</td>" . "<td>" . "<input type='text' id=$row[id] name=$row[id] size='10' pattern=[0-9]+ title='Only numbers'>" . "</td></tr>";

}

echo "</table>";
echo '<div class="wrapper">';
echo '<br><br><input type="reset" style="width: 200px;" align="center" value="Cancel">';
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '<input type="submit" style="width: 200px;" align="center" value="Place Order">';
 echo '<input type="hidden" name="which" value="createcustomerorder">';

echo "</div>";

echo "</form>";
echo "</div>";
/************************************************************/
if ($_SERVER['REQUEST_METHOD']=='POST')
{
        if ($_POST['which'] == 'createcustomerorder')
        {
		$ordernum=$order;
		$customerid=$_POST['customerSelection'];
		if(isset($_POST['itemSelect'])){
                        if(!empty($_POST['itemSelect'])){
			$ok="true";
			foreach ($_POST['itemSelect'] as $selected){
				if(empty($_POST[$selected])){$ok="false";}
			}
			if($ok!="false")
			{

			try {
			//create a table
			$addOrderSQL="INSERT INTO allOrders(cust_id) VALUES(?)";
			$selectOrderNumSQL="SELECT MAX(order_id) from allOrders WHERE cust_id=?";
			$addItemPurchaseSQL="INSERT INTO purchases(order_id,item_id,qty) VALUES(?,?,?)";

			$stmt=$conn->prepare($addOrderSQL);
			$ok=$stmt->execute(array($customerid));

			$stmt=$conn->prepare($selectOrderNumSQL);
                        $stmt->execute(array($customerid));
			$orderid=$stmt->fetchALL();

			//echo $orderid[0][0];

			foreach ($_POST['itemSelect'] as $selected){
			$stmt=$conn->prepare($addItemPurchaseSQL);
			$ok=$stmt->execute(array($orderid[0][0],$selected,$_POST[$selected]));
			//echo $selected; the item id
			//echo $_POST[$selected]; the quantity
			}
			echo "<script type='text/javascript'>alert('Order placed successfully!')</script>";
			}

			catch (PDOException $e) {
			echo "Oops, customer order could not be placed.<br>";
			echo "error: ".$e->getMessage();
			}
			}else{echo "<script type='text/javascript'>alert('Must input a quantity.')</script>";}
		}else{echo "There is no informarion provided";}
		}else{echo "<script type='text/javascript'>alert('Must select at least 1 item in order to process an order.')</script>";}
//when button is pressed, create an order number
//for each order, create a table as "ordernum(num)"
//for each table, use all attributes from the selected customer
//for each selected item, put the item id in the customertable
//for each selected item, put the quantity in the customertable
        }
}








?>

</body>
</html>
