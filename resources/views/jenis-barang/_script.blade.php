{{-- resources/views/jenis_barang/_script.blade.php --}}
<script>
    $(document).ready(function() {
        // Handle edit modal
        $(".edit-modal").on("click", function() {
            const id = $(this).data("id");
            const url = "{{ route('api.jenis-barang.show', ':id') }}".replace(':id', id);
            const updateUrl = "{{ route('jenis-barang.update', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                method: "GET",
                success: (res) => {
                    $("#jenis_barang_edit_modal form #kode").val(res.data.kode);
                    $("#jenis_barang_edit_modal form #name").val(res.data.name);
                    $("#jenis_barang_edit_modal form").attr("action", updateUrl);
                },
                error: (err) => {
                    alert("Terjadi kesalahan. Lihat console untuk detail");
                    console.log(err);
                }
            });
        });
    });
</script>