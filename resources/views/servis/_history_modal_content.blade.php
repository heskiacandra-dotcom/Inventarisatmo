<div>
    <p><strong>Nama Barang:</strong> {{ $barang->name }}</p>
    <p><strong>Lokasi:</strong> {{ $barang->commodity_location->nama_lokasi ?? '-' }}</p>
    <p><strong>Gedung:</strong> {{ $barang->commodity_location->gedung->nama_gedung ?? '-' }}</p>
    <p><strong>Letak Barang:</strong> {{ $barang->commodity_acquisition->nama_letak ?? '-' }}</p>
</div>

</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Diservis</th>
            <th>Kerusakan</th>
            <th>Perbaikan</th>
            <th>Tanggal Selesai</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($servisList as $index => $servis)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $servis->tanggal_diservis->format('d-m-Y') }}</td>
                <td>{{ $servis->kerusakan }}</td>
                <td>{{ $servis->perbaikan }}</td>
                <td>{{ $servis->tanggal_selesai ? $servis->tanggal_selesai->format('d-m-Y') : '-' }}</td>
                <td>{{ $servis->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
