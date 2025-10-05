<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Stiker Barang</title>
    <style>
        @page { size: 6cm 3cm; margin: 0; }
        body { margin: 0; padding: 0; font-family: DejaVu Sans, sans-serif; }

        .print-sticker {
            width: 6cm;
            height: 3cm;
            padding: 0.2cm;
            box-sizing: border-box;
            border: 1px solid #000;
            position: relative;
        }
        .kop {
            font-size: 6pt;
            font-weight: bold;
            text-align: left;
            line-height: 1.2;
        }
        .middle-content {
            position: absolute;
            left: 0.2cm;
            top: 1.2cm;
            width: calc(100% - 2.5cm);
            text-align: center;
        }
        .nama-barang { font-size: 7pt; font-style: italic; font-weight: bold; }
        .nama-ruang { font-size: 7pt; font-weight: bold; }
        .catatan {
            position: absolute;
            bottom: 0.2cm;
            left: 0.2cm;
            font-size: 6pt;
            text-align: center;
            width: 60%;
        }
        .qr-area {
            position: absolute;
            top: 0.2cm;
            right: 0.2cm;
            width: 2.1cm;
            height: 2.4cm;
            text-align: center;
        }
        .kode-barang {
            font-size: 5pt;
            background: black;
            color: white;
            display: inline-block;
            padding: 1px 4px;
            margin-top: 3px;
        }
    </style>
</head>
<body>
@foreach ($commodities as $commodity)
    <div class="print-sticker">
        <div class="kop">
            Gereja Katolik<br>
            <strong>KELUARGA KUDUS</strong><br>
            Jl. Atmodirono no.8 Semarang
        </div>

        <div class="middle-content">
            <div class="nama-barang">{{ $commodity->name }}</div>
            <div class="nama-ruang">{{ strtoupper($commodity->commodity_location->name) }}</div>
        </div>

        <div class="qr-area">
            {!! QrCode::size(120)->generate($commodity->id) !!}
            <div class="kode-barang">{{ $commodity->item_code }}</div>
        </div>

        <div class="catatan">
            MOHON UNTUK IKUT<br>MENJAGA INVENTARIS GEREJA
        </div>
    </div>
@endforeach
</body>
</html>
