<?php
// Template: Surat Keterangan Tidak Mampu (SKTM)
// Variabel tersedia: $data, $_SESSION
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Tidak Mampu</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
        }

        h3 {
            text-align: center;
            margin-bottom: 5px;
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

        .center {
            text-align: center;
        }
    </style>
</head>

<body>

    <h3>SURAT KETERANGAN TIDAK MAMPU</h3>
    <p class="center">
        <b>Nomor: <?= htmlspecialchars($data['nomor_surat'] ?? ''); ?></b>
    </p>
    <hr>

    <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>

    <table cellpadding="4">
        <tr>
            <td width="30%">Nama</td>
            <td>: <?= htmlspecialchars($data['nama_lengkap'] ?? ''); ?></td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>: <?= htmlspecialchars($data['nik'] ?? ''); ?></td>
        </tr>
        <tr>
            <td>Tempat / Tgl Lahir</td>
            <td>:
                <?= htmlspecialchars($data['tempat_lahir'] ?? ''); ?> /
                <?= htmlspecialchars($data['tanggal_lahir'] ?? ''); ?>
            </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?= nl2br(htmlspecialchars($data['alamat'] ?? '')); ?></td>
        </tr>
    </table>

    <br>

    <p>
        Berdasarkan hasil verifikasi dan keterangan yang ada, yang bersangkutan
        <b>benar-benar termasuk masyarakat yang:</b>
    </p>

    <p class="center"><b>TIDAK MAMPU / KURANG MAMPU</b></p>

    <p>
        Surat keterangan ini dibuat untuk keperluan:
        <br>
        <strong><?= htmlspecialchars($data['keperluan'] ?? ''); ?></strong>
    </p>

    <?php if (!empty($data['keterangan_tambahan'])): ?>
        <p>
            Keterangan Tambahan:
            <br>
            <?= nl2br(htmlspecialchars($data['keterangan_tambahan'])); ?>
        </p>
    <?php endif; ?>

    <br><br><br>

    <table>
        <tr>
            <td width="60%"></td>
            <td width="40%" align="center">
                Natar, <?= date('d-m-Y'); ?><br>
                Kepala Desa<br><br><br><br>
                <b><?= htmlspecialchars($_SESSION['sesi_nama'] ?? ''); ?></b>
            </td>
        </tr>
    </table>

</body>

</html>