<x-layout>
    <x-slot name="title">QR Code Barang</x-slot>

    <link href="https://fonts.googleapis.com/css2?family=Droid+Serif&family=Poppins:ital,wght@0,500;0,600;1,500;1,600&display=swap" rel="stylesheet">

    <style>
        .print-sticker {
            width: 6cm;
            height: 3cm;
            background: white;
            display: flex;
            flex-direction: column;
            padding: 0.2cm;
            font-family: 'Poppins', sans-serif;
            position: relative;
            box-sizing: border-box;
            color: #000;
        }

        .kop {
            font-family: 'Droid Serif', serif;
            line-height: 1.2;
            text-align: left;
            margin-bottom: 2px;
        }

        .logo-img {
    width: 0.7cm;
    height: 0.7cm;
    object-fit: contain;
    margin-top: 1px; /* Sesuaikan dengan vertikal kop */
}


        .kop .line1 {
            font-size: 5pt;
            font-weight: bold;
        }

        .kop .line2 {
            font-size: 6pt;
            font-weight: bolder;
        }

        .kop .line3 {
            font-size: 5pt;
            font-weight: bold;
        }

        .line-divider {
            border: none;
            border-top: 1px solid black;
            margin: 2px 0;
            width: 55%;
        }

        .middle-content {
            position: absolute;
            left: 0.1cm;
            width: calc(100% - 2.5cm);
            text-align: center;
        }

        .nama-barang {
            font-size: 7pt;
            font-style: italic;
            font-weight: 800;
        }

        .nama-ruang {
            font-size: 7pt;
            font-style: italic;
            font-weight: 1000;
        }

        .catatan {
            font-size: 6pt;
            font-weight: 500;
            line-height: 1.2;
        }

        .qr-area {
            position: absolute;
            top: 0.2cm;
            right: 0.2cm;
            width: 2.1cm;
            height: 2.4cm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .qr-code svg {
            width: 100% !important;
            height: auto !important;
        }

        .kode-barang {
            font-size: 5pt;
            font-weight: 600;
            background-color: black;
            color: white;
            padding: 1px 4px;
            border-radius: 2px;
            letter-spacing: 0.5px;
            font-family: 'Poppins', sans-serif;
            filter: contrast(115%);
            margin-top: 5px;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .print-sticker, .print-sticker * {
                visibility: visible !important;
            }

            .print-sticker {
                position: absolute;
                top: 0;
                left: 0;
                margin: 0;
                padding: 0;
                border: none;
                page-break-inside: avoid;
                page-break-after: avoid;
                transform: none !important;
                box-shadow: none !important;
            }

            @page {
                size: 6cm 3cm;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        @media screen {
    .preview-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 40px 0;
        gap: 170px;
    }

    .print-sticker {
        transform-origin: top center;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    /* Mobile: Lebih kecil */
    @media (max-width: 480px) {
        .print-sticker {
            transform: scale(1.2);
        }
    }

    /* Tablet: Ukuran sedang */
    @media (min-width: 481px) and (max-width: 768px) {
        .print-sticker {
            transform: scale(1.6);
        }
    }

    /* Desktop kecil */
    @media (min-width: 769px) and (max-width: 1200px) {
        .print-sticker {
            transform: scale(2);
        }
    }

    /* Desktop besar */
    @media (min-width: 1201px) {
        .print-sticker {
            transform: scale(2.5);
        }
    }
}
    </style>

    <!-- PREVIEW STICKER -->
    <div class="preview-container">
        <div class="print-sticker">
            <!-- KOP + LOGO -->
<div class="kop-container" style="display: flex; align-items: start; gap: 4px;">
    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="logo-img">
    
    <div class="kop">
        <div class="line1">Gereja Katolik</div>
        <div class="line2">KELUARGA KUDUS</div>
        <div class="line3">Jl. Atmodirono no.8 Semarang</div>
    </div>
</div>


            <hr class="line-divider">

            <!-- Nama Barang & Ruang -->
            <div class="middle-content" style="top: 1.2cm;">
                <div class="nama-barang">{{ $commodity->name }}</div>
                <div class="nama-ruang">{{ strtoupper($commodity->commodity_location->name) }}</div>
            </div>

            <!-- Garis Kedua -->
            <hr class="line-divider" style="position: absolute; bottom: 0.8cm;">

            <!-- Catatan Footer -->
            <div class="middle-content" style="bottom: 0.2cm; position: absolute;">
                <div class="catatan">
                    MOHON UNTUK IKUT<br>
                    MENJAGA INVENTARIS GEREJA
                </div>
            </div>

            <!-- QR -->
            <div class="qr-area">
                <div class="qr-code">
                    {!! QrCode::size(150)->style('round')->generate($commodity->id) !!}
                </div>
                <div class="kode-barang">{{ $commodity->item_code }}</div>
            </div>
        </div>
       <!-- Tombol Download - Dipindah ke dalam container -->
       <div class="no-print" style="margin-top: 20px;">
        <button onclick="generatePDF()" class="btn btn-primary">
            📥 Download PDF
        </button>
    </div>
</div>

    <!-- Script PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        async function generatePDF() {
            const element = document.querySelector('.print-sticker');
            const clone = element.cloneNode(true);
            const mmToPx = 3.78;
            clone.style.width = (60 * mmToPx) + 'px';
            clone.style.height = (30 * mmToPx) + 'px';
            clone.style.position = 'fixed';
            clone.style.left = '-9999px';
            clone.style.transform = 'none'; // Reset transform preview
            clone.style.boxShadow = 'none'; // Remove shadow
            document.body.appendChild(clone);

            const canvas = await html2canvas(clone, {
                scale: 3,
                useCORS: true,
                logging: false,
                width: 60 * mmToPx,
                height: 30 * mmToPx,
                windowWidth: 60 * mmToPx,
                windowHeight: 30 * mmToPx
            });

            document.body.removeChild(clone);

            const pdf = new window.jspdf.jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: [60, 30],
                compress: true
            });

            const imgData = canvas.toDataURL('image/png');
            pdf.addImage({
                imageData: imgData,
                format: 'PNG',
                x: 0,
                y: 0,
                width: 60,
                height: 30
            });

            pdf.save('Sticker-Barang.pdf');
        }
    </script>
</x-layout>
