<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Permohonan Izin Pendampingan Usaha</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20mm;
            color: #333;
            background-image: url('data:image/png;base64,{{ $kopImage }}');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            min-height: 100vh;
        }

        .header {
            /* margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c5aa0; */
            height: 60px;
            /* Memberikan ruang untuk header */
            background: transparent;
        }

        .header-image {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: contain;
        }

        .letter-info {
            margin: 30px 0;
            width: 100%;
        }

        .letter-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .letter-info-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .letter-details {
            width: 60%;
        }

        .letter-date {
            width: 40%;
            text-align: right;
        }

        .letter-row {
            margin: 5px 0;
        }

        .letter-row strong {
            /* display: inline-block; */
            width: 300px;
        }

        .letter-row span {
            display: inline-block;
            width: 60px;
        }

        .recipient {
            margin: 10px 0;
            margin-left: 70px;
        }

        .content {
            text-align: justify;
            margin: 20px 0;
            margin-left: 70px;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            /* margin: 10px 0; */
        }

        .student-table th,
        .student-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .student-table .mhs {
            text-align: left;
        }

        .student-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .student-table .no-col {
            width: 8%;
        }

        .student-table .nim-col {
            width: 23%;
        }

        .student-table .nama-col {
            width: 35%;
        }

        .student-table .prodi-col {
            width: 34%;
        }

        .closing {
            /* margin: 10px 0; */
        }

        .signature {
            margin-top: 40px;
            margin-left: 400px;
            text-align: left;
        }

        .signature-title {
            /* margin-bottom: 5px; */
        }

        .signature-qr {
            /* margin: 15px 0; */
        }

        .signature-qr img {
            width: 80px;
            height: 80px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-nip {
            margin-top: 5px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #2c5aa0;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 10px;
            font-style: italic;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-image: url('{{ public_path('storage/mku/kop-mku.png') }}');
                background-size: cover;
                background-position: center top;
                background-repeat: no-repeat;
                margin: 0;
                padding: 20mm;
                min-height: 100vh;
            }

            .footer {
                position: fixed;
                bottom: 0;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        {{-- <img src="data:image/png;base64,{{ $kopImage }}" alt="Kop Surat UMK" class="header-image"> --}}
    </div>

    <div class="letter-info">
        <table class="letter-info-table">
            <tr>
                <td class="letter-details">
                    <div class="letter-row">
                        <span>No.</span> : {{ $no_surat }}
                    </div>
                    <div class="letter-row">
                        <span>Lamp.</span> : -
                    </div>
                    <div class="letter-row">
                        <span>Hal</span> : <strong>Permohonan Izin Pendampingan Usaha</strong>
                    </div>
                </td>
                <td class="letter-date">
                    {{ $tanggal_surat }}
                </td>
            </tr>
        </table>
    </div>

    <div class="recipient">
        <p>Yth. Pimpinan/Pemilik {{ $nama_umkm }}</p>
        <p>di-</p>
        <p>{{ $alamat_umkm ?? 'Tempat' }}</p>
    </div>

    <div class="content">
        <p>Disampaikan dengan hormat, bersamaan dengan surat ini kami sampaikan bahwa dalam rangka penyelesaian tugas
            kuliah keterampilan wajib kewirausahaan mahasiswa S1 Universitas Muria Kudus melakukan pendampingan di
            tempat usaha Bapak/Ibu.</p>

        <p>Sehubungan dengan itu kami mohon dapatlah kirannya beberapa mahasiswa yang Namanya tercantum dibawah ini
            diizinkan untuk melakukan pendampingan di tempat usaha Bapak/Ibu. Adapun nama mahasiswa yang dimaksud adalah
            sebagai berikut:</p>

        <table class="student-table">
            <thead>
                <tr>
                    <th class="no-col">No</th>
                    <th class="nim-col">NIM</th>
                    <th class="nama-col">NAMA</th>
                    <th class="prodi-col">Prodi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mahasiswas as $index => $mhs)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $mhs->nim }}</td>
                        <td class="mhs">{{ $mhs->nama_mahasiswa }}</td>
                        <td>{{ $mhs->programStudi->nama_prodi ? $mhs->programStudi->nama_prodi : 'Program Studi tidak tersedia' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="closing">
            <p>Demikian atas perhatian dan perkenannya kami sampaikan terima kasih.</p>
        </div>
    </div>

    <div class="signature">
        <div class="signature-title">Ka.UPT Keterampilan dan MKU,</div>
        <div class="signature-qr">
            <img src="data:image/png;base64,{{ $qrImage }}">
        </div>
        <div class="signature-name">R. Rhoedy Setiawan, M.Kom, MTA, MCE</div>
        <div class="signature-nip">NIDN. 0607067001</div>
    </div>

    {{-- <div class="footer">
        <em>Dignity, Quality, Integrity | umk.ac.id</em>
    </div> --}}
</body>

</html>
