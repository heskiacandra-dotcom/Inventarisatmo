<script>
    $(document).ready(function() {
        // === SHOW MODAL ===
        // === SHOW MODAL ===
        $(".show-modal").click(function() {
            const id = $(this).data("id");
            let url = "{{ route('api.barang.show', ':paramID') }}".replace(":paramID", id);

            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                },
                success: (res) => {
                    const d = res.data;

                    // Row 1
                    $("#show_commodity #item_code").val(d.item_code);
                    $("#show_commodity #item_number").val(d.item_number); // nomor barang
                    $("#show_commodity #name").val(d.name);

                    // Row 2
                    $("#show_commodity #gedung").val(d.gedung ? d.gedung.name : "");
                    $("#show_commodity #jenis_barang").val(d.jenis_barang ? d.jenis_barang
                        .name : "");
                    $("#show_commodity #commodity_location_id").val(d.commodity_location ? d
                        .commodity_location.name : "");
                    $("#show_commodity #commodity_acquisition_id").val(d
                        .commodity_acquisition ? d.commodity_acquisition.name : "");

                    // Row 3
                    $("#show_commodity #material").val(d.material);
                    $("#show_commodity #brand").val(d.brand);

                    // Row 4
                    $("#show_commodity #date_of_purchase").val(d.date_of_purchase);
                    $("#show_commodity #condition").val(d
                    .condition_name); // kondisi sudah di-mapping di controller

                    // Row 5
                    $("#show_commodity #quantity").val(d.quantity);

                    // Harga dengan format Rp
                    if (d.price !== null) {
                        $("#show_commodity #price").val(new Intl.NumberFormat('id-ID')
                            .format(d.price));
                    } else {
                        $("#show_commodity #price").val('');
                    }

                    if (d.price_per_item !== null) {
                        $("#show_commodity #price_per_item").val(new Intl.NumberFormat(
                            'id-ID').format(d.price_per_item));
                    } else {
                        $("#show_commodity #price_per_item").val('');
                    }

                    // Row 6
                    $("#show_commodity #note").val(d.note);

                    // Tampilkan modal
                    $("#show_commodity").modal("show");
                },
                error: (err) => {
                    alert("Error occured, check console");
                    console.log(err);
                },
            });
        });

        // === EDIT MODAL ===
        $(".edit-modal").on("click", function() {
            const id = $(this).data("id");
            let url = "{{ route('api.barang.show', ':paramID') }}".replace(
                ":paramID",
                id
            );

            let updateURL = "{{ route('barang.update', ':paramID') }}".replace(
                ":paramID",
                id
            );

            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                },
                success: (res) => {
                    // isi form edit
                    $("#edit_commodity form #item_code").val(res.data.item_code);
                    $("#edit_commodity form #item_number").val(res.data
                    .item_number); // ✅ tambahkan
                    $("#edit_commodity form #name").val(res.data.name);

                    $("#edit_commodity form #commodity_location_id").val(res.data
                        .commodity_location.id);
                    $("#edit_commodity form #commodity_acquisition_id").val(res.data
                        .commodity_acquisition.id);
                    $("#edit_commodity form #jenis_barang_id").val(res.data.jenis_barang
                    .id);
                    $("#edit_commodity form #gedung_id").val(res.data.gedung.id);

                    $("#edit_commodity form #material").val(res.data.material);
                    $("#edit_commodity form #brand").val(res.data.brand);
                    $("#edit_commodity form #date_of_purchase").val(res.data
                        .date_of_purchase);
                    $("#edit_commodity form #condition").val(res.data.condition);
                    $("#edit_commodity form #note").val(res.data.note);
                    $("#edit_commodity form #quantity").val(res.data.quantity);
                    $("#edit_commodity form #price").val(res.data.price);
                    $("#edit_commodity form #price_per_item").val(res.data.price_per_item);

                    // set action form
                    $("#edit_commodity form").attr("action", updateURL);
                },
                error: (err) => {
                    alert("Error occured, check console");
                    console.log(err);
                },
            });
        });
        // === LEPAS MODAL ===
        $(".lepas-modal").on("click", function () {
            const id   = $(this).data("id");
            const nama = $(this).data("nama");

            // Isi nama barang
            $("#lepas_commodity #lepas_nama_barang").text(nama);

            // Set action form ke route lepas
            let lepasURL = "{{ route('barang.lepas', ':id') }}".replace(":id", id);
            $("#lepas_form").attr("action", lepasURL);

            // Reset field
            $("#lepas_metode_pelepasan").val("").trigger("change");
            $("#lepas_catatan_pelepasan").val("");
        });
    });
</script>
