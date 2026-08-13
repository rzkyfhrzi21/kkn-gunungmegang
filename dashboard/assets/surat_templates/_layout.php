<?php
function suratHeader($desa = [])
{
    $namaDesa   = $desa['nama_desa'] ?? 'DESA NATAR';
    $kecamatan  = $desa['kecamatan'] ?? 'NATAR';
    $kabupaten  = $desa['kabupaten'] ?? 'LAMPUNG SELATAN';
    $alamatDesa = $desa['alamat'] ?? 'Alamat Kantor Desa, Natar, Lampung Selatan';

    return '
    <table width="100%" cellpadding="2">
        <tr>
            <td width="15%" align="center"><b>[LOGO]</b></td>
            <td width="85%" align="center">
                <div style="font-size:14px;font-weight:bold;">PEMERINTAH KABUPATEN ' . htmlspecialchars($kabupaten) . '</div>
                <div style="font-size:14px;font-weight:bold;">KECAMATAN ' . htmlspecialchars($kecamatan) . '</div>
                <div style="font-size:16px;font-weight:bold;">' . htmlspecialchars($namaDesa) . '</div>
                <div style="font-size:10px;">' . htmlspecialchars($alamatDesa) . '</div>
            </td>
        </tr>
    </table>
    <hr>
    ';
}

function suratFooterTTD($ttd = [])
{
    $tempat = $ttd['tempat'] ?? 'Natar';
    $tanggal = $ttd['tanggal'] ?? date('d-m-Y');
    $jabatan = $ttd['jabatan'] ?? 'Kepala Desa';
    $namaPejabat = $ttd['nama'] ?? 'Nama Pejabat';
    $nip = $ttd['nip'] ?? '';

    $nipHtml = $nip ? '<div style="font-size:11px;">NIP. ' . htmlspecialchars($nip) . '</div>' : '';

    return '
    <br><br>
    <table width="100%" cellpadding="2">
        <tr>
            <td width="60%"></td>
            <td width="40%" align="center">
                <div>' . htmlspecialchars($tempat) . ', ' . htmlspecialchars($tanggal) . '</div>
                <div><b>' . htmlspecialchars(strtoupper($jabatan)) . '</b></div>
                <br><br><br>
                <div><b><u>' . htmlspecialchars($namaPejabat) . '</u></b></div>
                ' . $nipHtml . '
            </td>
        </tr>
    </table>
    ';
}
