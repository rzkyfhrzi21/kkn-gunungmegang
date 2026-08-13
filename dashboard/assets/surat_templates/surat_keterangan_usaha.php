<?php
// Template: Surat Keterangan Usaha
// Variabel tersedia: $data (array data), $_SESSION
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Usaha</title>
    <style>
        body {
            font-family: Times, "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.6;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
        }

        .nomor {
            text-align: center;
            margin-bottom: 10px;
        }

        hr {
            border: 1px solid #000;
        }

        table {
            width: 100%;
        }

        td {
            vertical-align: top;
        }

        .ttd {
            margin-top: 50px;
            text-align: right;
        }
    </style>
</head>

<body>

    <p class="judul">SURAT KETERANGAN USAHA</p>
    <p class="nomor">
        Nomor: <?= htmlspecialchars($data['nomor_'] ?? ''); ?>
    </p>
    <hr>

    <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>

    <table cellpadding="4">
        <tr>
            <td width="35%">Nama</td>
            <td>: <?= htmlspecialchars($data['nama_lengkap']); ?></td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>: <?= htmlspecialchars($data['nik']); ?></td>
        </tr>
        <tr>
            <td>Tempat / Tgl Lahir</td>
            <td>:
                <?= htmlspecialchars($data['tempat_lahir'] ?? ''); ?>,
                <?= htmlspecialchars($data['tanggal_lahir'] ?? ''); ?>
            </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?= htmlspecialchars($data['alamat'] ?? ''); ?></td>
        </tr>
    </table>

    <p>
        Berdasarkan keterangan yang ada, yang bersangkutan benar memiliki usaha:
    </p>

    <table cellpadding="4">
        <tr>
            <td width="35%">Nama Usaha</td>
            <td>: <?= htmlspecialchars($data['nama_usaha'] ?? ''); ?></td>
        </tr>
        <tr>
            <td>Jenis Usaha</td>
            <td>: <?= htmlspecialchars($data['jenis_usaha'] ?? ''); ?></td>
        </tr>
        <tr>
            <td>Alamat Usaha</td>
            <td>: <?= htmlspecialchars($data['alamat_usaha'] ?? ''); ?></td>
        </tr>
    </table>

    <p>
        Surat ini dibuat untuk keperluan:
        <br>
        <strong><?= htmlspecialchars($data['keperluan'] ?? ''); ?></strong>
    </p>

    <p>
        Demikian data keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya.
    </p>

    <div class="ttd">
        Natar, <?= date('d F Y'); ?><br>
        Kepala Desa<br><br><br><br>
        <strong><?= htmlspecialchars($_SESSION['sesi_nama'] ?? ''); ?></strong>
    </div>

</body>

</html>