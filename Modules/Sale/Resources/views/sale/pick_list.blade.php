<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    body{
  font-family: Arial, Helvetica, sans-serif;
}
table  {
  border-collapse: collapse;
}
  td, th {
    padding: 0.5rem;
    border: 1px solid black;
    vertical-align: top;
}
tfoot tr:last-child td{
  border-bottom: none;
}
caption{
  font-weight: bold;
  text-align: left;
  font-size: 3rem;
  padding-bottom: 1.5rem;
  color: black;
}
th{
  text-align: left;
  background-color: white;
  color: black;
}
tfoot tr td:first-child {
  background-color: white;
  border: none;
}
tfoot{
 border-top: 5px solid black 
}
tbody tr:nth-child(even){
  background-color: white;
}
tfoot tr:nth-child(odd){
  background-color: white;
}
/*responsive table*/
.tablescroll{
  overflow: auto;
}
</style>
<body>
    <table class="table Crm_table_active3">
        <caption>Pick List</caption>
        <thead>
        <tr>
            <th scope="col">SKU</th>
            <th scope="col">Image</th>
            <th scope="col">Name</th>
            <th scope="col">Order ID</th>
            <th scope="col">Resi</th>
            <th scope="col">Qty</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($result as $item)
                <tr>
                    <td>{{ $item['sku'] }}</td>
                    <td><img src="{{ $item['product_main_image'] }}" alt="" style="max-width: 100px"></td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['order_id'] }}</td>
                    <td>{{ $item['tracking_code'] }}</td>
                    <td>{{ $item['qty'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>