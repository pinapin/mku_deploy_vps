<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perjanjian Kerja Sama - Program P2K</title>
    <style>
        body {
            font-size: 11pt;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            line-height: 1.4;
            margin: 0;
            /* padding: 20px; */
            background-color: white;
            color: black;
        }

        .container {
            max-width: 21cm;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }

        .header h1 {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 0;
        }

        .logo-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100px;
            z-index: 1;
            pointer-events: none;
        }

        .logo {

            display: flex;
            align-items: center;
            justify-content: center;

        }

        .header-content {
            /* padding-top: 120px; */
            z-index: 2;
            position: relative;
            text-transform: uppercase;
        }

        .document-info {
            margin: 20px 0;
            padding: 0 125px;
        }

        .document-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .document-info td {
            /* padding: 3px; */
            vertical-align: top;
        }

        .parties-info {
            margin: 20px 0;
        }

        .parties-info table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .parties-info td {
            vertical-align: top;
            /* border: 1px solid #ccc; */
        }

        .article {
            margin: 20px 0;
            text-align: justify;
        }

        .article-title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }

        .article-content {
            margin: 10px 0;
            text-indent: 0;
        }

        .article-content ol {
            margin: 0;
            padding-left: 20px;
        }

        .article-content li {
            /* margin: 8px 0; */
            text-align: justify;
        }

        .numbered-item {
            display: flex;
            align-items: flex-start;
        }

        .number {
            min-width: 25px;
            flex-shrink: 0;
            font-weight: normal;
        }

        .content {
            flex: 1;
            text-align: justify;
        }

        .highlight {
            font-weight: bold;
        }

        .signature-section {
            margin-left: 25px;
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: left;
        }

        .signature-box .title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .signature-box .org-name {
            font-weight: bold;
            margin: 5px 0;
        }

        .signature-space {
            height: 80px;
            margin: 20px 0;
            position: relative;
        }

        .qr-code {
            margin-left: 5px;
            width: 60px;
            height: 60px;
            /* background-color: #f0f0f0;
            border: 1px solid #333; */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        .materai-note {
            font-size: 10px;
            color: #666;
            margin: 5px 0;
        }

        .article p {
            margin: 0;
        }

        .red-text {
            color: red;
        }

        .underline {
            text-decoration: underline;
        }

        .page-break {
            page-break-before: always;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }

        p {
            text-align: justify;
        }

        @media print {
            .container {
                box-shadow: none;
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .page-break {
                page-break-before: always;
            }
        }

        .party-section {
            /* font-size: 12px; */
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .party-title {
            font-weight: bold;
            margin-bottom: 0;
        }

        .organization-name {
            font-weight: bold;
            margin: 0;
        }

        .university-name {
            font-weight: bold;
            margin: 0;
        }

        .address {
            margin: 0;
        }

        .contact-info {
            margin: 0;
        }

        .contact-line {
            display: flex;
            margin: 0;
        }

        .contact-label {
            width: 115px;
            flex-shrink: 0;
        }

        .contact-value {
            flex: 1;
        }

        .red-text {
            color: red;
        }

        .spacing {
            margin-bottom: 8px;
        }

        .small-margin {
            margin-bottom: 2px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Page 1 -->
        <div class="header">
            <div class="logo-container">
                <div class="logo">
                    <img src="{{ asset('assets/image/logo-umk.png') }}" alt="" style="width: 150px;">
                </div>
                <div class="logo">
                    <img src="{{ asset('storage/' . $pks->umkm->logo_umkm) }}" alt="" style="width: 100px;">
                </div>
            </div>

            <div class="header-content">
                <h1>PERJANJIAN KERJA SAMA</h1>
                <h1>ANTARA</h1>
                <h1>UPT KETERAMPILAN DAN MKU</h1>
                <h1>UNIVERSITAS MURIA KUDUS</h1>
                <h1>DENGAN</h1>
                <h1>{{ $pks->umkm->nama_umkm }}</h1>
                <h1>TENTANG</h1>
                <h1>PROGRAM PENDAMPINGAN KEWIRAUSAHAAN (P2K)</h1>
            </div>
        </div>

        <div class="document-info">
            <table>
                <tr>
                    <td style="">Nomor</td>
                    <td style="width: 20px;">:</td>
                    <td>{{ $pks->no_pks }}</td>
                </tr>
                <tr>
                    <td>Nomor</td>
                    <td>:</td>
                    <td>{{ $pks->no_pks_umkm ?? '-' }}</td>
                </tr>
            </table>
        </div>


        <p>Pada hari ini {{ $hari_indonesia }} tanggal <span
                style="text-transform: capitalize;">{{ $tgl_latin }}</span> bulan {{ $bulan_latin }} tahun
            <span style="text-transform: capitalize;">{{ $tahun_latin }}</span>
            ({{ $tanggal_pks }}),
            bertempat di Kudus kami yang bertanda tangan di bawah ini:
        </p>


        <div class="parties-info" style="margin-top: 0px;">
            <table>
                <tr>
                    <td style="width: 30px;">1.</td>
                    <td style="width: 230px;">Nama Pimpinan Unit dari UMK</td>
                    <td style="width: 20px;">:</td>
                    <td>R. Rhoedy Setiawan, M.Kom, MTA, MCE</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>Ka.UPT Keterampilan dan MKU</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>Jalan Lingkar Utara, Gondangmanis, Bae, Kudus, Jawa Tengah 59327</td>
                </tr>
            </table>
        </div>

        <p>Dalam hal ini bertindak untuk dan atas nama Unit Pelaksana Teknis Mata Kuliah Umum dan Keterampilan
            Universitas Muria Kudus selanjutnya, disebut sebagai <strong>PIHAK KESATU</strong>.</p>

        <div class="parties-info" style="margin-top: 0px;">
            <table>
                <tr>
                    <td style="width: 30px;">2.</td>
                    <td style="width: 230px;">Nama Pimpinan Unit Mitra</td>
                    <td style="width: 20px;">:</td>
                    <td style="text-transform: capitalize;">{{ $pks->umkm->nama_pemilik_umkm }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td style="text-transform: capitalize;">{{ $pks->umkm->jabatan_umkm }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $pks->umkm->alamat_umkm }}</td>
                </tr>
            </table>
        </div>

        <p>Dalam hal ini bertindak untuk dan atas nama <span>{{ $pks->umkm->nama_umkm }}</span>,
            selanjutnya disebut
            sebagai <strong>PIHAK KEDUA</strong> selanjutnya disebut sebagai <strong>PARA PIHAK</strong> telah sepakat
            untuk mengadakan Perjanjian Kerja Sama yang diatur dalam pasal-pasal sebagai berikut.</p>

        <div class="article-title">
            Pasal 1<br>
            MAKSUD DAN TUJUAN
        </div>

        <div class="article-content">
            <div class="numbered-item">
                <div class="number">(1)</div>
                <div class="content">
                    Maksud Perjanjian Kerja Sama ini adalah sebagai landasan dalam rangka pelaksanaan kerja sama yang
                    disusun oleh <span class="highlight">PARA PIHAK</span> sesuai dengan ruang lingkup Perjanjian Kerja
                    Sama ini berlandaskan prinsip keadilan, kesetaraan dan simbiosis mutualisme.
                </div>
            </div>

            <div class="numbered-item">
                <div class="number">(2)</div>
                <div class="content">
                    Tujuan Perjanjian Kerja Sama ini adalah untuk meningkatkan hubungan institusional PARA PIHAK dalam
                    bidang Pendidikan Kewirausahaan pengembangan UMKM dan Pendidikan guna mendukung pelaksanaan
                    tridharma perguruan tinggi.
                </div>
            </div>
        </div>

        <div class="article">
            <div class="article-title">Pasal 2<br>RUANG LINGKUP</div>
            <div class="article-content">
                <p>Ruang lingkup Perjanjian Kerja Sama ini meliputi namun tidak terbatas pada bidang:</p>
                <ol style="list-style-type: lower-alpha;">
                    <li>Bidang pendidikan Kewirausahaan dan Bisnis untuk Mahasiswa</li>
                    <li>Penyelanggaraan program Merdeka Belajar -- Kampus Merdeka</li>
                    <li>Peningkatan kualitas sumber daya manusia, penyedia tenaga ahli, seminar lokakarya, diskusi dan
                        kelompok terarah dan</li>
                    <li>Kegiatan lain yang disepakati PARA PIHAK</li>
                </ol>
            </div>
        </div>

        <div class="footer" style="margin-top: 0px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="text-align: left;">Paraf PIHAK KESATU: __________</td>
                    <td style="text-align: center;">Paraf PIHAK KEDUA: __________</td>
                    <td style="text-align: right;">Halaman 1 dari 4</td>
                </tr>
            </table>
        </div>

        <!-- Page 2 -->
        <div class="page-break">
            <div class="article">
                <div class="article-title">
                    Pasal 3<br>
                    PELAKSANAAN
                </div>
                <div class="article-content">
                    <div class="numbered-item">
                        <div class="number">(1)</div>
                        <div class="content">
                            <span class="highlight">PARA PIHAK</span> saling mendukung dalam pelaksanaan pelatihan baik
                            secara online ataupun offline berdasarkan ketentuan yang disepakati bersama.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(2)</div>
                        <div class="content">
                            Pelaksanaan setiap bidang kerja sama sebagaimana dimaksud dalam Pasal 2 dapat diatur dan
                            dituangkan dalam Petunjuk Teknis Penyelenggaraan Kerja Sama/ Implementation Arrangements
                            tersendiri, atau dokumen sejenis lainnya dan merupakan bagian tak terpisahkan dari
                            perjanjian ini
                        </div>
                    </div>
                </div>
            </div>

            <div class="article">
                <div class="article-title">
                    Pasal 4<br>
                    HAK DAN KEWAJIBAN
                </div>
                <div class="article-content">
                    <div class="numbered-item">
                        <div class="number">(1)</div>
                        <div class="content">
                            Masing-masing PIHAK berhak mendapatkan manfaat atas Perjanjian ini sesuai dengan ruang
                            lingkup perjanjian yang telah disepakati.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(2)</div>
                        <div class="content">
                            Masing-masing PIHAK wajib mentaati segala peraturan yang diberlakukan oleh PIHAK lainnya
                            dalam hal pelatihan kewirausahaan atau pelatihan digital marketing.
                        </div>
                    </div>
                </div>
            </div>

            <div class="article">
                <div class="article-title">
                    Pasal 5<br>
                    JANGKA WAKTU
                </div>
                <div class="article-content">
                    <div class="numbered-item">
                        <div class="number">(1)</div>
                        <div class="content">
                            Perjanjian Kerja Sama ini berlaku untuk jangka waktu
                            <span>{{ $pks->lama_perjanjian }}</span> tahun sejak tanggal ditandatangani,
                            dan dapat diperpanjang, diakhiri,
                            dan dievaluasi atas dasar kesepakatan <span class="highlight">PARA PIHAK</span>.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(2)</div>
                        <div class="content">
                            Dalam hal salah satu <span class="highlight">PIHAK</span> berkeinginan untuk mengakhiri
                            Perjanjian Kerja Sama ini sebelum jangka waktu sebagaimana dimaksud pada ayat (1), maka
                            <span class="highlight">PIHAK</span> tersebut wajib memberitahukan maksud tersebut secara
                            tertulis kepada <span class="highlight">PIHAK</span> lainnya, selambat-lambatnya 3 (tiga)
                            bulan sebelumnya.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(3)</div>
                        <div class="content">
                            Dalam hal Perjanjian Kerja Sama ini tidak diperpanjang lagi sebagaimana dimaksud pada ayat
                            (2), tidak akan mempengaruhi hak dan kewajiban masing-masing <span
                                class="highlight">PIHAK</span> yang harus diselesaikan terlebih dahulu sebagai akibat
                            pelaksanaan sebelum berakhirnya Perjanjian Kerja Sama ini.
                        </div>
                    </div>
                </div>
            </div>

            <div class="article">
                <div class="article-title">
                    Pasal 6<br>
                    PEMBIAYAAN
                </div>
                <div class="article-content">
                    <div class="numbered-item">
                        <div class="number">(1)</div>
                        <div class="content">
                            Masing-masing PIHAK memiliki kewajiban pembiayaan berdasarkan kesepakatan PARA PIHAK.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(2)</div>
                        <div class="content">
                            Pembiayaan yang menjadi kewajiban masing-masing PIHAK dapat dibiayai melalui sponsor.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(3)</div>
                        <div class="content">
                            Cara pembayaran dan pelaporan keuangan untuk setiap pembiayaan disesuaikan dengan mekanisme
                            penggunaan dana yang berlaku pada masing-masing PIHAK, sesuai dengan kewajiban pembiayaan
                            sebagaimana dimaksud pada Ayat (1)
                        </div>
                    </div>
                </div>
            </div>

            <div class="article">
                <div class="article-title">
                    Pasal 7<br>
                    PEMBATALAN PERJANJIAN
                </div>
                <div class="article-content">
                    <div class="numbered-item">
                        <div class="number">(1)</div>
                        <div class="content">
                            Pembatalan Perjanjian Kerja Sama dapat dilakukan atas permintaan salah satu <span
                                class="highlight">PIHAK</span> berdasarkan persetujuan tertulis <span
                                class="highlight">PIHAK</span> lainnya.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(2)</div>
                        <div class="content">
                            Surat Permintaan pembatalan sebagaimana dimaksud dalam ayat (1) harus dibuat secara tertulis
                            oleh <span class="highlight">PIHAK</span> lainnya dan diterima paling lambat 30 (tiga
                            puluh) hari kalender sebelum tanggal pembatalan perjanjian.
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer" style="margin-top: 130px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left;">Paraf PIHAK KESATU: __________</td>
                        <td style="text-align: center;">Paraf PIHAK KEDUA: __________</span>
                        </td>
                        <td style="text-align: right;">Halaman 2 dari 4</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Page 3 -->
        <div class="page-break">
            <div class="article-content">
                <div class="numbered-item">
                    <div class="number">(3)</div>
                    <div class="content">
                        Apabila pada saat Perjanjian Kerja Sama ini berakhir atau diputuskan terdapat kewajiban yang
                        belum dapat diselesaikan, maka ketentuan dalam Perjanjian Kerja Sama ini tetap berlaku sampai
                        diselesaikannya kewajiban tersebut.
                    </div>
                </div>

            </div>

            <div class="article">
                <div class="article-title">Pasal 8<br>FORCE MAJEURE</div>
                <div class="article-content">
                    <div class="numbered-item">
                        <div class="number">(1)</div>
                        <div class="content">
                            <strong>PARA PIHAK</strong> dibebaskan dari tanggung jawab atas keterlambatan atau kegagalan
                            dalam memenuhi kewajiban yang tercantum dalam perjanjian ini yang disebabkan oleh kejadian
                            di luar kekuasaan <strong>PARA PIHAK</strong> yang digolongkan sebagai force majeure.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(2)</div>
                        <div class="content">
                            Peristiwa yang dapat digolongkan force majeure antara lain adanya bencana alam (gempa bumi,
                            taufan, banjir, dan lain-lain), wabah penyakit, perang, revolusi, huru hara dan kekacauan
                            ekonomi/moneter yang berpengaruh pada perjanjian ini.
                        </div>
                    </div>

                    <div class="numbered-item">
                        <div class="number">(3)</div>
                        <div class="content">
                            Apabila terjadi force majeure maka <strong>PIHAK</strong> yang lebih dahulu mengetahui wajib
                            memberitahukan kepada <strong>PIHAK</strong> lainnya selambat-lambatnya 14 (empat belas)
                            hari kalender setelah terjadinya force majeure.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(4)</div>
                        <div class="content">
                            Keadaan force majeure tidak menghapuskan perjanjian dan apabila kondisi sudah normal,
                            <strong>PARA PIHAK</strong> dapat melanjutkan Perjanjian Kerja Sama sebagaimana mestinya.
                        </div>
                    </div>
                </div>
            </div>

            <div class="article">
                <div class="article-title">Pasal 9<br>KORESPONDENSI</div>
                <div class="article-content">
                    <div class="numbered-item">
                        <div class="number">(1)</div>
                        <div class="content">
                            Semua surat-menyurat atau pemberitahuan yang berhubungan dengan pelaksanaan Perjanjian Kerja
                            Sama ini akan dibuat secara tertulis, disampaikan dalam bentuk surat resmi dengan alamat
                            sebagai berikut:
                        </div>
                    </div>

                    <div style="margin-left: 25px;margin-top: 10px;margin-bottom: 10px;">
                        <div class="party-section">
                            <p class="party-title spacing">PIHAK KESATU:</p>
                            <p class="organization-name small-margin">UNIT PELAKSANA TEKNIS KETERAMPILAN DAN MATA
                                KULIAH UMUM</p>
                            <p class="university-name small-margin">UNIVERSITAS MURIA KUDUS</p>
                            <p class="address spacing">Jalan Lingkar Utara, Gondangmanis, Bae, Kudus, Jawa Tengah 59327
                            </p>
                            <p class="contact-line small-margin">
                                <span class="contact-label">U.p.</span>
                                <span class="contact-value">: Ka. UPT Keterampilan dan MKU</span>
                            </p>
                            <p class="contact-line small-margin">
                                <span class="contact-label">Nomor seluler</span>
                                <span class="contact-value">: +62 815-7529-7844</span>
                            </p>
                            <p class="contact-line spacing">
                                <span class="contact-label">E-mail</span>
                                <span class="contact-value">: rhoedy.setiawan@umk.ac.id</span>
                            </p>
                        </div>

                        <div class="party-section">
                            <p class="party-title spacing">PIHAK KEDUA:</p>
                            <p class="spacing" style="text-transform: uppercase;">{{ $pks->umkm->nama_umkm }}</p>
                            <p class="contact-line small-margin">
                                <span class="contact-label">U.p.</span>
                                <span class="contact-value">: <span
                                        style="text-transform: capitalize;">{{ $pks->pic_pks }}</span></span>
                            </p>
                            <p class="contact-line small-margin">
                                <span class="contact-label">Telp.</span>
                                <span class="contact-value">: <span>{{ $pks->umkm->no_hp_umkm }}</span></span>
                            </p>
                            <p class="contact-line">
                                <span class="contact-label">E-mail</span>
                                <span class="contact-value">: <span>{{ $pks->email_pks }}</span></span>
                            </p>
                        </div>
                    </div>

                    <div class="numbered-item">
                        <div class="number">(2)</div>
                        <div class="content">
                            Apabila ada perubahan alamat koresponden sebagaimana dimaksud pada ayat (1),
                            <strong>PIHAK</strong> yang melakukan perubahan alamat korespondensi tersebut berkewajiban
                            untuk memberitahukan secara tertulis kepada <strong>PIHAK</strong> lainnya dan tidak perlu
                            dilakukan amandemen atas Perjanjian Kerja Sama ini.
                        </div>
                    </div>
                </div>
            </div>

            <div class="article">
                <div class="article-title">Pasal 10<br>LAIN-LAIN</div>
                <div class="article-content">
                    <div class="numbered-item">
                        <div class="number">(1)</div>
                        <div class="content">
                            Perjanjian Kerja Sama ini dilaksanakan secara kelembagaan berdasarkan itikad baik kedua
                            belah <strong>PIHAK</strong>.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(2)</div>
                        <div class="content">
                            Segala perbedaan pendapat yang terjadi dalam pelaksanaan Perjanjian Kerja Sama ini akan
                            diselesaikan secara musyawarah dan mufakat.
                        </div>
                    </div>
                </div>
            </div>


            <div class="footer" style="margin-top: 50px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left;">Paraf PIHAK KESATU: __________</td>
                        <td style="text-align: center;">Paraf PIHAK KEDUA: __________</span>
                        </td>
                        <td style="text-align: right;">Halaman 3 dari 4</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Page 4 -->
        <div class="page-break">

            <div class="article">
                <div class="article-title">Pasal 11<br>KETENTUAN PENUTUP</div>
                <div class="article-content">
                    <div class="numbered-item">
                        <div class="number">(1)</div>
                        <div class="content">
                            Hal-hal yang belum cukup diatur dalam Perjanjian Kerja Sama ini, akan ditetapkan dalam
                            <em>Addendum</em> yang disepakati oleh <strong>PARA PIHAK</strong> yang merupakan bagian
                            yang tidak terpisahkan dari Perjanjian Kerja Sama ini.
                        </div>
                    </div>
                    <div class="numbered-item">
                        <div class="number">(2)</div>
                        <div class="content">
                            Perjanjian Kerja Sama ini ditandatangani oleh <strong>PARA PIHAK</strong> dalam rangkap 2
                            (dua) bermaterai cukup dan mempunyai kekuatan hukum yang sama untuk dipergunakan sebagaimana
                            mestinya.
                        </div>
                    </div>
                </div>
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="title">PIHAK KESATU,</div>
                    <div class="org-name">UPT KETERAMPILAN DAN MKU</div>
                    <div class="org-name">UNIVERSITAS MURIA KUDUS,</div>

                    <div class="qr-code">
                        {{-- QR CODE --}}
                    </div>

                    <div class="signature-space" style="margin-top: 33px">
                        <div class="underline">R. Rhoedy Setiawan, M.Kom, MTA, MCE</div>
                        <div>Kepala</div>
                    </div>
                </div>

                <div class="signature-box">
                    <div class="title">PIHAK KEDUA,</div>
                    <div class="highlight">{{ $pks->umkm->nama_umkm }}</div>

                    {{-- <div class="signature-space">
                        <div class="materai-note highlight"><strong>Nb: Materai digunakan berdasarkan kesediaan dari
                            UMKM terkait.</strong></div>
                        </div> --}}
                    <div class="signature-space">
                        <div style="position: absolute;top: 35px;color: grey;font-size: 11px;">Meterai
                            <br>Rp 10.000
                        </div>

                        <div class="materai-note highlight"><strong></strong></div>
                    </div>

                    <div class="underline" style="text-transform: capitalize;">{{ $pks->umkm->nama_pemilik_umkm }}
                    </div>
                    <div>Kepala</div>
                </div>
            </div>

            <div class="footer" style="margin-top: 560px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left;">Paraf PIHAK KESATU: __________</td>
                        <td style="text-align: center;">Paraf PIHAK KEDUA: __________</span>
                        </td>
                        <td style="text-align: right;">Halaman 4 dari 4</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    window.addEventListener('afterprint', function () {
        window.close();
    });

    window.onload = function () {
        window.print();
    };
</script>
