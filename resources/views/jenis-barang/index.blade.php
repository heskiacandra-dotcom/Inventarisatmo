{{-- resources/views/jenis_barang/index.blade.php --}}
<x-layout>
    <x-slot name="title">Halaman Daftar Jenis Aset Tetap</x-slot>
    <x-slot name="page_heading">Daftar Jenis Aset Tetap</x-slot>

    <div class="card">
        <div class="card-body">
            @include('utilities.alert')
            
            <div class="d-flex justify-content-end mb-3">
                <div class="btn-group">
                    @can('tambah jenis barang')
                    <button type="button" class="btn btn-primary" 
                            data-toggle="modal" data-target="#jenis_barang_create_modal">
                        <i class="fas fa-fw fa-plus"></i> Tambah Data
                    </button>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <x-datatable>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jenisBarang as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->kode }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('ubah jenis barang')
                                        <a data-id="{{ $item->id }}" 
                                           class="btn btn-sm btn-success text-white edit-modal mr-2"
                                           data-toggle="modal" data-target="#jenis_barang_edit_modal">
                                            <i class="fas fa-fw fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('hapus jenis barang')
                                        <form action="{{ route('jenis-barang.destroy', $item->id) }}" 
                                              method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger text-white delete-button">
                                                <i class="fas fa-fw fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </x-datatable>
                </div>
            </div>
        </div>
    </div>

    @push('modal')
    @include('jenis-barang.modal.create')
    @include('jenis-barang.modal.edit')
    @endpush

    @push('js')
    @include('jenis-barang._script')
    @endpush
</x-layout>