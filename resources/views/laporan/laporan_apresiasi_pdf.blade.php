<!DOCTYPE html>
<html>
<head>
	<title>Laporan Apresiasi PDF</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
	<centre>
		<h5>Laporan Apresiasi</h5>
	</centre>

	<table class='table table-bordered'>
		<thead>
			<tr>
				<th>No</th>
				<th>Jenis Laporan</th>
				<th>Jenis Apresiasi</th>
				<th>Keterangan</th>
				<th>Nama Lokasi</th>
				<th>Alamat</th>
                <th>Kota</th>
			</tr>
		</thead>
		<tbody>
			@php $i=1 @endphp
			@foreach($cetak as $p)
			<tr>
				<td>{{ $i++ }}</td>
				<td>{{$p->nama_laporan}}</td>
				<td>{{$p->nama_apresiasi}}</td>
				<td>{{$p->keterangan}}</td>
				<td>{{$p->nama_lokasi}}</td>
				<td>{{$p->alamat}}</td>
                <td>{{$p->kota}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

</body>
</html>