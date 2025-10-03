<script>
    $(document).ready(function() {
        $(".show-modal").click(function() {
            const id = $(this).data("id");
            let url = "{{ route('api.gedung.show', ':paramID') }}".replace(
                ":paramID",
                id
            );

            $.ajax({
                url: url,
                header: {
                    "Content-Type": "application/json",
                },
                success: (res) => {
                    $("#show_commodity_location #name").val(res.data.name);
                },
                error: (err) => {
                    alert("error occured, check console");
                    console.log(err);
                },
            });
        });

        $(".edit-modal").on("click", function() {
            const id = $(this).data("id");
            let url = "{{ route('api.gedung.show', ':paramID') }}".replace(
                ":paramID",
                id
            );

            let updateURL = "{{ route('gedung.update', ':paramID') }}".replace(
                ":paramID",
                id
            );

            $.ajax({
                url: url,
                method: "GET",
                header: {
                    "Content-Type": "application/json",
                },
                success: (res) => {
                    $("#gedung_edit_modal form #name").val(res.data.name);
                    $("#gedung_edit_modal form #kode").val(res.data.kode);
                    $("#gedung_edit_modal form #gedung_id").val(res.data
                    .id); // tambahkan ini
                    $("#gedung_edit_modal form").attr("action", updateURL);
                },

                error: (err) => {
                    alert("error occured, check console");
                    console.log(err);
                },
            });
        });
    });
</script>
