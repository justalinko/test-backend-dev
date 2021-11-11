<html>
<head>
	<title>#INVOICE-{{ $bill_id }} </title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
	<center>
		<h5>#INVOICE-{{ $bill_id }}</h5>
		<h6><a target="_blank" href="{{ url()->current() }}">{{  url()->current() }}</a></h6>
	</center>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th>No</th>
				<th>Billing ID</th>
				<th>Payment Method</th>
				<th>Amount</th>
				<th>Description</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			@php $i=1 @endphp
			@foreach($data as $p)
			<tr>
				<td>{{ $i++ }}</td>
				<td>{{$p->billing_id}}</td>
				<td>{{$p->payment_method}}</td>
				<td>{{str_replace(',','.' ,number_format($p->amount)) }} IDR</td>
				<td>{{$p->description}}</td>
				<td>{{strtoupper($p->status) }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
 
</body>
</html>