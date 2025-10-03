<!-- pdf.blade.php -->
<x-layout>
    <x-slot name="title">QR Code Barang</x-slot>
    <link href="https://fonts.googleapis.com/css2?family=Droid+Serif&family=Poppins:ital,wght@0,500;0,600;1,500;1,600&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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
            margin-top: 1px;
            /* Sesuaikan dengan vertikal kop */
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
            font-size: 7pt;
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

        /* Dalam bagian <style> pdf.blade.php */
        @media print {
            body * {
                visibility: hidden;
            }

            .print-sticker,
            .print-sticker * {
                visibility: visible !important;
            }

            .print-sticker {
                width: 6cm !important;
                height: 3cm !important;
                page-break-after: always;
                position: relative !important;
                margin: 0 !important;
                padding: 0.2cm !important;
                top: 0 !important;
                left: 0 !important;
                transform: none !important;
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

            .preview-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    justify-content: center;
    padding: 20px;
}

.preview-grid .print-sticker {
    transform: scale(0.9);
    transform-origin: top left;
    box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
}

/* Responsive grid untuk layar kecil */
@media (max-width: 1200px) {
    .preview-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .preview-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .preview-grid .print-sticker {
        transform: scale(0.8);
    }
}

@media (max-width: 480px) {
    .preview-grid {
        grid-template-columns: repeat(1, 1fr);
    }

    .preview-grid .print-sticker {
        transform: scale(0.7);
    }
}
        }
    </style>

    <!-- PREVIEW STICKER -->
    <div class="preview-container">
      <div class="preview-grid">
        @foreach ($commodities as $commodity)
            <div class="print-sticker">
                <!-- KOP + LOGO -->
                <div class="kop-container" style="display: flex; align-items: start; gap: 4px;">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="logo-img" width="0.7cm"
                        height="0.7cm">
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
        @endforeach
      </div>

        <!-- Tombol Download -->
        <div class="no-print" style="margin-top: 20px;">
            <button onclick="generatePDF()" class="btn btn-primary">📥 Download PDF</button>
        </div>
    </div>

    <script>
        async function generatePDF() {
            const elements = document.querySelectorAll('.print-sticker');
            const pdf = new window.jspdf.jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: [60, 30],
                compress: true
            });

            const mmToPx = 3.78; // Konversi mm ke pixel

            for (let i = 0; i < elements.length; i++) {
                const element = elements[i];
                const clone = element.cloneNode(true);
                clone.style.width = `${60 * mmToPx}px`;
                clone.style.height = `${30 * mmToPx}px`;
                clone.style.position = 'fixed';
                clone.style.left = '-9999px';
                clone.style.transform = 'none';
                clone.style.boxShadow = 'none';
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

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 0, 0, 60, 30);

                if (i < elements.length - 1) {
                    pdf.addPage([60, 30], 'landscape');
                }
            }

            pdf.save('Stickers-Barang.pdf');
        }
    </script>

</x-layout>
