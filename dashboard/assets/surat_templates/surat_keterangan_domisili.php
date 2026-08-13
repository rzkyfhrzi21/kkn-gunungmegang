<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: times;
            font-size: 12pt;
            line-height: 1.6;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
        }

        hr {
            border: 1px solid #000;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
        }

        td {
            vertical-align: top;
        }
    </style>
</head>

<body>

    <p class="judul">SURAT KETERANGAN DOMISILI</p>
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
            <td>Tempat, Tgl Lahir</td>
            <td>: <?= htmlspecialchars($data['tempat_lahir']); ?>, <?= htmlspecialchars($data['tanggal_lahir']); ?></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: <?= $data['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
        </tr>
        <tr>
            <td>Agama</td>
            <td>: <?= htmlspecialchars($data['agama']); ?></td>
        </tr>
        <tr>
            <td>Pekerjaan</td>
            <td>: <?= htmlspecialchars($data['pekerjaan']); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?= htmlspecialchars($data['alamat']); ?></td>
        </tr>
    </table>

    <p style="margin-top:15px;">
        Surat ini dibuat untuk keperluan:<br>
        <strong><?= htmlspecialchars($data['keperluan']); ?></strong>
    </p>

    <p style="margin-top:40px;">
        Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya.
    </p>

    <p style="text-align:right; margin-top:50px;">
        Natar, <?= date('d-m-Y'); ?><br><br><br>
        <strong>Kepala Desa</strong>
    </p>

</body>

</html>