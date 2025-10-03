<x-layout>
    <x-slot name="title">Halaman Daftar Peruntukan Barang</x-slot>
    <x-slot name="page_heading">Daftar Peruntukan Barang</x-slot>

    <div class="card">
        <div class="card-body">
            @include('utilities.alert')
            <div class="d-flex justify-content-end mb-3">
                <div class="btn-group">
                    @can('import gedung')
                    <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#excel_menu">
                        <i class="fas fa-fw fa-upload"></i>
                        Import Excel
                    </button>
                    @endcan

                    @can('export gedung')
                    <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#export_menu">
                        <i class="fas fa-fw fa-download"></i>
                        Export
                    </button>
                    @endcan

                    @can('tambah gedung')
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#gedung_create_modal">
                        <i class="fas fa-fw fa-plus"></i>
                        Tambah Data
                    </button>
                    @endcan
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <x-datatable>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th>Kode Gedung</th> <!-- Kolom baru -->
                                <th scope="col">Nama</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gedung as $item)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $item->kode }}</td> <!-- Tampilkan kode -->
                                <td>{{ $item->name }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('ubah gedung')
                                        <a data-id="{{ $item->id }}" 
                                           class="btn btn-sm btn-success text-white edit-modal mr-2"
                                           data-toggle="modal" data-target="#gedung_edit_modal">
                                            <i class="fas fa-fw fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('hapus gedung')
                                        <form action="{{ route('gedung.destroy', $item->id) }}" 
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
    @include('gedung.modal.create')
    @include('gedung.modal.edit')
    @include('gedung.modal.import')
    @include('gedung.modal.export')
    @endpush

    @push('js')
    @include('gedung._script')
    @endpush
</x-layout>