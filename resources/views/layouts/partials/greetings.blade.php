<script>
    function greetings() {
        let asiaTime = new Date().toLocaleString('en-US', {
            timeZone: 'Asia/Makassar'
        });
        asiaTime = new Date(asiaTime);
        let hours = asiaTime.getHours();
        if (hours <= 10) msg = 'Selamat Pagi Tim Bidang Rumah Tangga!';
        if (hours >= 11) msg = 'Selamat Siang Tim Bidang Rumah Tangga!';
        if (hours >= 16) msg = 'Selamat Sore Tim Bidang Rumah Tangga!';
        if (hours >= 19) msg = 'Selamat Malam Tim Bidang Rumah Tangga!';

        return msg;
    }
</script>