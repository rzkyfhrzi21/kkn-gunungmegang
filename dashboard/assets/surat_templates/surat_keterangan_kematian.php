<?php
// Template: Surat Keterangan Kematian
?>
<h3 align="center">SURAT KETERANGAN KEMATIAN</h3>
<p align="center"><b>Nomor: <?= htmlspecialchars($data['nomor_data'] ?? ''); ?></b></p>
<hr>

<p>Yang bertanda tangan di bawah ini, Kepala Desa menerangkan bahwa:</p>

<!-- DATA ALMARHUM / ALMARHUMAH -->
<table cellpadding="4">
    <tr>
        <td width="180">Nama Almarhum/Almarhumah</td>
        <td>: <?= htmlspecialchars($data['nama_meninggal'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>NIK</td>
        <td>: <?= htmlspecialchars($data['nik_meninggal'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Tempat / Tanggal Lahir</td>
        <td>:
            <?= htmlspecialchars($data['tempat_lahir_meninggal'] ?? ''); ?> /
            <?= htmlspecialchars($data['tanggal_lahir_meninggal'] ?? ''); ?>
        </td>
    </tr>
    <tr>
        <td>Alamat Terakhir</td>
        <td>: <?= nl2br(htmlspecialchars($data['alamat_meninggal'] ?? '')); ?></td>
    </tr>
</table>

<br>

<p>Telah meninggal dunia pada:</p>

<table cellpadding="4">
    <tr>
        <td width="180">Hari / Tanggal</td>
        <td>: <?= htmlspecialchars($data['tanggal_meninggal'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Tempat Meninggal</td>
        <td>: <?= htmlspecialchars($data['tempat_meninggal'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Sebab Meninggal</td>
        <td>: <?= htmlspecialchars($data['sebab_meninggal'] ?? ''); ?></td>
    </tr>
</table>

<br>

<!-- DATA PEMOHON SEBAGAI SAKSI -->
<p>Adapun keterangan tersebut disampaikan oleh:</p>

<table cellpadding="4">
    <tr>
        <td width="180">Nama Saksi / Pemohon</td>
        <td>: <?= htmlspecialchars($data['nama_lengkap'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>NIK</td>
        <td>: <?= htmlspecialchars($data['nik'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Tempat / Tanggal Lahir</td>
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
    Surat keterangan ini dibuat untuk keperluan:
    <br>
    <strong><?= htmlspecialchars($data['keperluan'] ?? ''); ?></strong>
</p>

<br><br><br>

<table width="100%">
    <tr>
        <td width="60%"></td>
        <td width="40%" align="center">
            Natar, <?= date('d-m-Y'); ?><br>
            Kepala Desa<br><br><br><br>
            <b><?= htmlspecialchars($_SESSION['sesi_nama'] ?? ''); ?></b>
        </td>
    </tr>
</table>