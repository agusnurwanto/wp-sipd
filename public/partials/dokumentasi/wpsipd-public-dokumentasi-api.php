<!-- https://github.com/ticlekiwi/API-Documentation-HTML-Template !-->
<style>
    .hljs {
        display: block;
        overflow-x: auto;
        padding: 0.5em;
        background: #323F4C;
    }

    .hljs {
        color: #fff;
    }

    .hljs-strong,
    .hljs-emphasis {
        color: #a8a8a2
    }

    .hljs-bullet,
    .hljs-quote,
    .hljs-link,
    .hljs-number,
    .hljs-regexp,
    .hljs-literal {
        color: #6896ba
    }

    .hljs-code,
    .hljs-selector-class {
        color: #a6e22e
    }

    .hljs-emphasis {
        font-style: italic
    }

    .hljs-keyword,
    .hljs-selector-tag,
    .hljs-section,
    .hljs-attribute,
    .hljs-name,
    .hljs-variable {
        color: #cb7832
    }

    .hljs-params {
        color: #b9b9b9
    }

    .hljs-string {
        color: #6a8759
    }

    .hljs-subst,
    .hljs-type,
    .hljs-built_in,
    .hljs-builtin-name,
    .hljs-symbol,
    .hljs-selector-id,
    .hljs-selector-attr,
    .hljs-selector-pseudo,
    .hljs-template-tag,
    .hljs-template-variable,
    .hljs-addition {
        color: #e0c46c
    }

    .hljs-comment,
    .hljs-deletion,
    .hljs-meta {
        color: #7f7f7f
    }


    @charset "utf-8";

    /* RESET
----------------------------------------------------------------------------------------*/

    html,
    body,
    div,
    span,
    applet,
    object,
    iframe,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    p,
    blockquote,
    pre,
    a,
    abbr,
    acronym,
    address,
    big,
    cite,
    code,
    del,
    dfn,
    em,
    ins,
    kbd,
    q,
    s,
    samp,
    small,
    strike,
    strong,
    sub,
    sup,
    tt,
    var,
    b,
    u,
    i,
    center,
    dl,
    dt,
    dd,
    ol,
    ul,
    li,
    fieldset,
    form,
    label,
    legend,
    table,
    caption,
    tbody,
    tfoot,
    thead,
    tr,
    th,
    td,
    article,
    aside,
    canvas,
    details,
    embed,
    figure,
    figcaption,
    footer,
    hgroup,
    menu,
    nav,
    output,
    ruby,
    section,
    summary,
    time,
    mark,
    audio,
    video {
        margin: 0;
        padding: 0;
        border: 0;
        font-size: 100%;
        font: inherit;
        vertical-align: baseline;
    }

    article,
    aside,
    details,
    figcaption,
    figure,
    footer,
    header,
    hgroup,
    menu,
    nav,
    section {
        display: block;
    }

    img,
    embed,
    object,
    video {
        max-width: 100%;
    }

    .ie6 img.full,
    .ie6 object.full,
    .ie6 embed,
    .ie6 video {
        width: 100%;
    }

    /* BASE
----------------------------------------------------------------------------------------*/

    * {
        -webkit-transition: all 0.3s ease;
        -moz-transition: all 0.3s ease;
        -o-transition: all 0.3s ease;
        -ms-transition: all 0.3s ease;
        transition: all 0.3s ease;
    }

    html,
    body {
        position: relative;
        min-height: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        font-family: 'Roboto', sans-serif;
    }

    strong {
        font-weight: 500;
    }

    i {
        font-style: italic;
    }

    .overflow-hidden {
        position: relative;
        overflow: hidden;
    }

    .content a {
        color: #00a8e3;
        text-decoration: none;
    }

    .content a:hover {
        text-decoration: underline;
    }

    .scroll-to-link {
        cursor: pointer;
    }

    p,
    .content ul,
    .content ol {
        font-size: 14px;
        color: #777A7A;
        margin-bottom: 16px;
        line-height: 1.6;
        font-weight: 300;
    }

    .content h1:first-child {
        font-size: 1.333em;
        color: #034c8f;
        padding-top: 2.5em;
        text-transform: uppercase;
        border-top: 1px solid rgba(255, 255, 255, 0.3);
        border-top-width: 0;
        margin-top: 0;
        margin-bottom: 1.3em;
        clear: both;
    }

    code,
    pre {
        font-family: 'Source Code Pro', monospace;
    }

    .higlighted {
        background-color: rgba(0, 0, 0, 0.05);
        padding: 3px;
        border-radius: 3px;
    }

    /* LEFT-MENU
----------------------------------------------------------------------------------------*/

    .left-menu {
        position: fixed;
        z-index: 3;
        top: 0;
        left: 0;
        bottom: 0;
        width: 300px;
        box-sizing: border-box;
        background-color: #f4f5f8;
        overflow-x: hidden;
        font-size: 18px;
    }

    .left-menu .content-infos {
        position: relative;
        padding: 12px 13.25%;
        margin-bottom: 20px;
    }

    .left-menu .info {
        position: relative;
        font-size: 14px;
        margin-top: 5px;
        color: #777A7A;
    }

    .left-menu .info b {
        font-weight: 500;
        color: #034c8f;
    }

    .content-logo {
        position: relative;
        display: block;
        width: 100%;
        box-sizing: border-box;
        padding: 1.425em 11.5%;
        padding-right: 0;
    }

    .content-logo img {
        display: inline-block;
        max-width: 70%;
        vertical-align: middle;
    }

    .content-logo span {
        display: inline-block;
        margin-left: 10px;
        vertical-align: middle;
        color: #323F4C;
        font-size: 1.1em;
    }

    .content-menu {
        margin: 2em auto 2em;
        padding: 0 0 100px;
    }

    .content-menu ul {
        list-style: none;
        margin: 0;
        padding: 0;
        line-height: 28px;
    }

    .content-menu ul li {
        list-style: none;
        margin: 0;
        padding: 0;
        line-height: 0;
    }

    .content-menu ul li:hover,
    .content-menu ul li.active {
        background-color: #DCDEE9;
    }

    .content-menu ul li:hover a,
    .content-menu ul li.active a {
        color: #00a8e3;
    }

    @media (hover: none) {
        .content-menu ul li:not(.active):hover {
            background-color: inherit;
        }

        .content-menu ul li:not(.active):hover a {
            color: #777A7A;
        }
    }

    .content-menu ul li a {
        padding: 12px 13.25%;
        color: #777A7A;
        letter-spacing: 0.025em;
        line-height: 1.1;
        display: block;
        text-transform: capitalize;
    }

    /* CONTENT-PAGE
----------------------------------------------------------------------------------------*/

    .content-page {
        position: relative;
        box-sizing: border-box;
        margin-left: 300px;
        z-index: 2;
        background-color: #fff;
        min-height: 100%;
        padding-bottom: 1px;
    }

    .content-code {
        width: 50%;
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        background-color: #323f4c;
        border-color: #323f4c;
    }

    .content {
        position: relative;
        z-index: 30;
    }

    .content h1,
    .content h2,
    .content h3,
    .content h4,
    .content h5,
    .content h6,
    .content p,
    .content table,
    .content aside,
    .content dl,
    .content ul,
    .content ol,
    .content .central-overflow-x {
        margin-right: 50%;
        padding: 0 28px;
        box-sizing: border-box;
        display: block;
        max-width: 680px;
    }

    .content .central-overflow-x {
        margin-right: calc(50% + 28px);
        margin-left: 28px;
        padding: 0;
        overflow-y: hidden;
        max-width: 100%;
        display: block;
    }

    .content p .central-overflow-x {
        margin-right: 0;
        margin-left: 0;
    }

    .break-word {
        word-break: break-word;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }

    .content ul,
    .content ol {
        padding: 0 44px;
    }

    .content h2,
    .content h3,
    .content h4,
    .content h5,
    .content h6 {
        font-size: 15px;
        margin-top: 2.5em;
        margin-bottom: 0.8em;
        color: #034c8f;
        text-transform: uppercase;
    }

    .content h2 {
        font-size: 1.333em;
    }

    .content h4 {
        color: #00a8e3;
        margin-top: 0;
        text-transform: none;
        font-size: 14px;
        margin-bottom: 0.2em;
    }

    .content-page .content p,
    .content-page .content pre {
        max-width: 680px;
    }

    .content pre,
    .content blockquote {
        background-color: #323f4c;
        border-color: #323f4c;
        color: #fff;
        padding: 0 28px 2em;
        margin: 0;
        width: 50%;
        float: right;
        clear: right;
        box-sizing: border-box;
    }

    .content pre code,
    .content pre {
        font-size: 12px;
        line-height: 1;
    }

    .content blockquote,
    .content pre,
    .content pre code {
        padding-top: 0;
        margin-top: 0;
    }

    .content pre code {
        margin-top: -2em;
    }

    .content table {
        font-size: 0.825em;
        margin-bottom: 1.5em;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .content table tr:last-child {
        border-bottom: 1px solid #ccc;
    }

    .content table th {
        font-size: 0.925em;
        padding: 5px 18px 5px 0;
        border-bottom: 1px solid #ccc;
        vertical-align: bottom;
        text-align: left;
        line-height: 1;
    }

    .content table td {
        padding: 5px 18px 5px 0;
        text-align: left;
        vertical-align: top;
        line-height: 1;
        font-family: 'Roboto', sans-serif;
        font-weight: 300;
        color: #777A7A;
    }


    /* burger-menu-icon
----------------------------------------------------------------------------------------*/
    .burger-menu-icon {
        background-color: transparent;
        border: none;
        cursor: pointer;
        display: inline-block;
        vertical-align: middle;
        padding: 0;
        position: absolute;
        right: 26px;
        top: 26px;
        display: none;
    }

    .burger-menu-icon .line {
        fill: none;
        stroke: #000;
        stroke-width: 6;
        transition: stroke-dasharray 600ms cubic-bezier(0.4, 0, 0.2, 1),
            stroke-dashoffset 600ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .burger-menu-icon .line1 {
        stroke-dasharray: 60 207;
        stroke-width: 6;
    }

    .burger-menu-icon .line2 {
        stroke-dasharray: 60 60;
        stroke-width: 6;
    }

    .burger-menu-icon .line3 {
        stroke-dasharray: 60 207;
        stroke-width: 6;
    }

    html.menu-opened .burger-menu-icon .line1 {
        stroke-dasharray: 90 207;
        stroke-dashoffset: -134;
        stroke-width: 6;
    }

    html.menu-opened .burger-menu-icon .line2 {
        stroke-dasharray: 1 60;
        stroke-dashoffset: -30;
        stroke-width: 6;
    }

    html.menu-opened .burger-menu-icon .line3 {
        stroke-dasharray: 90 207;
        stroke-dashoffset: -134;
        stroke-width: 6;
    }


    /* ONE CONTENT COLUMN VERSION
----------------------------------------------------------------------------------------*/

    body.one-content-column-version .content h1,
    body.one-content-column-version .content h2,
    body.one-content-column-version .content h3,
    body.one-content-column-version .content h4,
    body.one-content-column-version .content h5,
    body.one-content-column-version .content h6,
    body.one-content-column-version .content p,
    body.one-content-column-version .content table,
    body.one-content-column-version .content ul,
    body.one-content-column-version .content ol,
    body.one-content-column-version .content aside,
    body.one-content-column-version .content dl,
    body.one-content-column-version .content ul,
    body.one-content-column-version .content ol {
        margin-right: 0;
        max-width: 100%;
    }

    body.one-content-column-version .content-page .content p,
    body.one-content-column-version .content-page .content pre {
        max-width: 100%;
    }

    body.one-content-column-version .content-page {
        background-color: #323f4c;
    }

    body.one-content-column-version .content h1:first-child,
    body.one-content-column-version .content h2,
    body.one-content-column-version .content h3,
    body.one-content-column-version .content h4,
    body.one-content-column-version .content h5,
    body.one-content-column-version .content h6 {
        color: #59C3C3;
    }

    body.one-content-column-version p {
        color: #D6F0F0;
    }

    body.one-content-column-version .content table td {
        color: #D6F0F0;
    }

    body.one-content-column-version .content thead {
        color: #417179;
    }

    /* RESPONSIVE
----------------------------------------------------------------------------------------*/

    @media only screen and (max-width:980px) {

        .content h1,
        .content h2,
        .content h3,
        .content h4,
        .content h5,
        .content h6,
        .content p,
        .content table,
        .content ul,
        .content ol,
        .content aside,
        .content dl,
        .content ul,
        .content ol {
            margin-right: 0;
        }

        .content .central-overflow-x {
            margin: 0;
            padding: 0 28px;
        }

        .content-code {
            display: none;
        }

        .content pre,
        .content blockquote {
            margin: 20px 0;
            padding: 28px;
            display: block;
            width: auto;
            float: none;
        }

        .content pre code {
            margin-top: 0;
        }
    }

    @media only screen and (max-width:680px) {
        html {
            scroll-padding-top: 83px;
        }

        html.menu-opened {
            overflow: hidden;
        }

        .left-menu {
            position: relative;
            width: auto;
        }

        .left-menu .content-menu {
            position: fixed;
            width: 400px;
            max-width: 90vw;
            z-index: 3;
            top: 0;
            bottom: 0;
            right: -405px;
            left: auto;
            background-color: #fff;
            margin: 0;
            overflow-x: hidden;
            padding-top: 83px;
            padding-bottom: 20px;
        }

        .left-menu .content-menu ul {
            position: relative;
        }

        .left-menu .mobile-menu-closer {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
            background-color: rgba(50, 63, 76, .5);
            opacity: 0;
            visibility: hidden;
        }

        html.menu-opened .left-menu .mobile-menu-closer {
            opacity: 1;
            visibility: visible;
        }

        html.menu-opened .left-menu .content-menu {
            right: 0;
        }

        .left-menu .content-logo {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 4;
            background-color: #f4f5f8;
        }

        .content-logo .logo {
            margin-right: 65px;
        }

        .content-page {
            margin-left: 0;
            padding-top: 83px;
        }

        .burger-menu-icon {
            display: block;
        }
    }

    /* BROWSER AND NON-SEMANTIC STYLING
----------------------------------------------------------------------------------------*/

    .cf:before,
    .cf:after {
        content: "";
        display: block;
    }

    .cf:after {
        clear: both;
    }

    .ie6 .cf {
        zoom: 1
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.8.0/highlight.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;1,300&family=Source+Code+Pro:wght@300&display=swap" rel="stylesheet">
<script>
    hljs.initHighlightingOnLoad();
</script>
<div class="left-menu">
    <div class="content-logo">
        <div class="logo">
            <img alt="WP-SIPD" title="WP-SIPD" src="<?php echo WPSIPD_PLUGIN_URL; ?>public/images/logo.png" height="32" />
            <span>API Documentation</span>
        </div>
        <button class="burger-menu-icon" id="button-menu-mobile">
            <svg width="34" height="34" viewBox="0 0 100 100">
                <path class="line line1" d="M 20,29.000046 H 80.000231 C 80.000231,29.000046 94.498839,28.817352 94.532987,66.711331 94.543142,77.980673 90.966081,81.670246 85.259173,81.668997 79.552261,81.667751 75.000211,74.999942 75.000211,74.999942 L 25.000021,25.000058"></path>
                <path class="line line2" d="M 20,50 H 80"></path>
                <path class="line line3" d="M 20,70.999954 H 80.000231 C 80.000231,70.999954 94.498839,71.182648 94.532987,33.288669 94.543142,22.019327 90.966081,18.329754 85.259173,18.331003 79.552261,18.332249 75.000211,25.000058 75.000211,25.000058 L 25.000021,74.999942"></path>
            </svg>
        </button>
    </div>
    <?php $versi = get_option('_wp_sipd_db_version');
    $last_update = get_option('_last_update_sql_migrate');
    $tgl = str_replace('tabel.sql', '', $last_update); ?>
    <div class="mobile-menu-closer"></div>
    <div class="content-menu">
        <div class="content-infos">
            <div class="info"><b>Version:</b> <?php echo $versi; ?></div>
            <div class="info"><b>Last Updated:</b> <?php echo $tgl; ?></div>
        </div>
        <li class="scroll-to-link active" data-target="content-get-started">
            <a>Pendahuluan</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-skpd">
            <a>Get SKPD</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-subkeg-by-skpd">
            <a>Get SUB KEGIATAN</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-rka-by-kodesbl">
            <a>Get RKA</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-spd-by-tahun-anggaran">
            <a>Get SPD</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-spp-by-tahun-anggaran">
            <a>Get SPP</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-pembiayaan-by-tahun-anggaran">
            <a>Get Pembiayaan</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-pendapatan-by-tahun-anggaran">
            <a>Get Pendapatan</a>
        </li>
        </li>
        <li class="scroll-to-link" data-target="content-get-rak-by-tahun-anggaran">
            <a>Get RAK</a>
        </li>
        <li class="scroll-to-link" data-target="content-errors">
            <a>Errors</a>
        </li>
        </ul>
    </div>
</div>
<div class="content-page">
    <div class="content-code"></div>
    <div class="content">
        <div class="overflow-hidden content-section" id="content-get-started">
            <h1>Get started</h1>
            <pre>
    API Endpoint
    <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php

    <?php
    $key = 'diperlukan';
    if (is_user_logged_in() == 1) {
        $key = get_option('_crb_api_key_extension');
    } ?>
    API key = <?php echo $key; ?>
                </pre>
            <p>
                <strong>WP SIPD</strong> telah dilengkapi dengan kemampuan untuk membuat output API sekaligus tanpa Anda perlu menambahkan backend atau modul khusus untuk pengelolaan API. Konsep pada implementasi API selaras dengan fitur-fitur pada backoffice <strong>WP SIPD</strong> yang sedang Anda buka saat ini.
            </p>
            <p>
                Anda tidak perlu lagi memikirkan hal-hal rumit yang membebani pekerjaan Anda. Seluruh permintaan API akan melalui proses otorisasi dan pengecekan hak akses, termasuk pada validasi yang telah Anda tentukan pada tiap modul yang telah atau akan Anda bangun.

                Semudah itukah? Ya, karena ini <strong>WP-SIPD!</strong>.
            </p>
            <h2><b> Mulai Dari Mana? </b></h2>
            <p>Untuk dapat menggunakan fitur permintaan API, Anda perlu menambahkan <strong>API key</strong> terlebih dahulu.

                Sematkan <strong>API key</strong> yang telah dibuat yang dikhususkan untuk klien tertentu pada property Form-data saat melakukan permintaan.
            </p>
        </div>
        <div class="overflow-hidden content-section" id="content-get-skpd">
            <h2>GET SKPD</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_skpd' \
-F 'tahun_anggaran=2023' \
                </code>
            </pre>
            <p>
                Untuk menampilkan Nama dan Kode SKPD berdasarkan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
                <code class="higlighted break-word">
                    <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php
                </code>
            <h4>QUERY PARAMETERS</h4>
            <table class="central-overflow-x" style="width:40%">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Keterangan</th>
                        <th>Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-family:Consolas">
                                api_key
                            </span>
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Kunci API yang telah ditambahkan dan diaktifkan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            action
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            tahun_anggaran
                        </td>
                        <td>
                            Angka
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
            <pre>
                <code class="json">
Result :
{
    "action": "get_skpd",
    "run": null,
    "status": "success",
    "message": "Berhasil get SKPD!",
    "data": [
        {
            "id": "215",
            "id_setup_unit": "0",
            "id_unit": "529",
            "is_skpd": "0",
            "kode_skpd": "7.01.0.00.0.00.06.0006",
            "kunci_skpd": "0",
            "nama_skpd": "Kelurahan Siring",
            "posisi": "",
            "status": "Unit SKPD",
            "id_skpd": "7052",
            "bidur_1": "47",
            "bidur_2": "0",
            "bidur_3": "0",
            "idinduk": "529",
            "ispendapatan": "0",
            "isskpd": "0",
            "kode_skpd_1": "06",
            "kode_skpd_2": "0006",
            "kodeunit": "7.01.0.00.0.00.06.0006",
            "komisi": "0",
            "namabendahara": "",
            "namakepala": "MUNIR NANANG SETYOWANDI, SH",
            "namaunit": "Kelurahan Siring",
            "nipbendahara": "",
            "nipkepala": "196702131992031007",
            "pangkatkepala": "Penata",
            "setupunit": "0",
            "statuskepala": "PLT",
            "update_at": "2023-11-28 11:11:21",
            "tahun_anggaran": "2023",
            "active": "1",
            "mapping": null,
            "id_kecamatan": null,
            "id_strategi": null,
            "is_dpa_khusus": null,
            "is_ppkd": null,
            "set_input": null,
            "id_mapping": false,
            "bidur__1": "7.01",
            "bidur__2": "0.00",
            "bidur__3": "0.00",
            "bidur1": "7.01 KECAMATAN",
            "bidur2": null,
            "bidur3": null
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-subkeg-by-skpd">
            <h2>GET SUB KEGIATAN BERDASARKAN SKPD</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_sub_keg_sipd' \
-F 'tahun_anggaran=2023' \
-F 'id_skpd=2023' \
                </code>
            </pre>
            <p>
                Untuk menampilkan Sub Kegiatan berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
                <code class="higlighted break-word">
                    <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php
                </code>
            <h4>QUERY PARAMETERS</h4>
            <table class="central-overflow-x" style="width:40%">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Keterangan</th>
                        <th>Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-family:Consolas">
                                api_key
                            </span>
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Kunci API yang telah ditambahkan dan diaktifkan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            action
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            tahun_anggaran
                        </td>
                        <td>
                            Angka
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            id_skpd
                        </td>
                        <td>
                            Angka
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian/Filter
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
            <pre>
                <code class="json">
Result :
{
    "action": "get_sub_keg_sipd",
    "status": "success",
    "message": "Berhasil get sub kegiatan!",
    "data": [
        {
            "id": "8156",
            "id_sub_skpd": "529",
            "id_lokasi": "0",
            "id_label_kokab": "0",
            "nama_dana": "",
            "no_sub_giat": "01 ",
            "kode_giat": "7.01.02.2.01",
            "id_program": "797",
            "nama_lokasi": "",
            "waktu_akhir": "12",
            "pagu_n_lalu": "0",
            "id_urusan": "17",
            "id_unik_sub_bl": "62107ff967e05-1223841137-1645248505",
            "id_sub_giat": "13480",
            "label_prov": "",
            "kode_program": "7.01.02",
            "kode_sub_giat": "7.01.02.2.01.01",
            "no_program": "02 ",
            "kode_urusan": "7",
            "kode_bidang_urusan": "7.01",
            "nama_program": "PROGRAM PENYELENGGARAAN PEMERINTAHAN DAN PELAYANAN PUBLIK",
            "target_4": "",
            "target_5": "",
            "id_bidang_urusan": "97",
            "nama_bidang_urusan": "KECAMATAN",
            "target_3": "",
            "no_giat": "2.01",
            "id_label_prov": "0",
            "waktu_awal": "1",
            "pagumurni": "296187500",
            "pagu": "296187500",
            "pagu_simda": null,
            "output_sub_giat": "",
            "sasaran": "Aparatur Kecamatan, Kelurahan, Desa dan Masyarakat",
            "indikator": "",
            "id_dana": "0",
            "nama_sub_giat": "7.01.02.2.01.01 Koordinasi/Sinergi Perencanaan dan Pelaksanaan Kegiatan Pemerintahan dengan Perangkat Daerah dan Instansi Vertikal Terkait",
            "pagu_n_depan": "1176533636",
            "satuan": "",
            "id_rpjmd": "0",
            "id_giat": "3705",
            "id_label_pusat": "7",
            "nama_giat": "Koordinasi Penyelenggaraan Kegiatan\n\nPemerintahan di Tingkat Kecamatan",
            "kode_skpd": "7.01.0.00.0.00.06.0000",
            "nama_skpd": "Kecamatan Porong",
            "kode_sub_skpd": "7.01.0.00.0.00.06.0000",
            "id_skpd": "529",
            "id_sub_bl": "0",
            "nama_sub_skpd": "Kecamatan Porong",
            "target_1": "",
            "nama_urusan": "UNSUR KEWILAYAHAN",
            "target_2": "",
            "label_kokab": "",
            "label_pusat": "Mengembangkan Wilayah Untuk Mengurangi Kesenjangan Dan Menjamin Pemerataan",
            "pagu_keg": "296187500",
            "pagu_fmis": null,
            "id_bl": "0",
            "kode_bl": "529.529.797.3705",
            "kode_sbl": "529.529.797.3705.13480",
            "active": "1",
            "update_at": "2023-11-27 03:19:20",
            "tahun_anggaran": "2023",
            "nama_skpd_data_unit": "Kecamatan Porong",
            "nama_skpd_data_unit_utama": "Kecamatan Porong",
            "sub_keg_indikator": [
                {
                    "id": "17837",
                    "outputteks": "Jumlah Laporan Koordinasi/Sinergi Perencanaan dan Pelaksanaan Kegiatan Pemerintahan dengan Perangkat Daerah dan Instansi Vertikal Terkait",
                    "targetoutput": "1",
                    "satuanoutput": "Laporan",
                    "idoutputbl": "568111",
                    "targetoutputteks": "1 Laporan",
                    "kode_sbl": "529.529.797.3705.13480",
                    "idsubbl": "0",
                    "active": "1",
                    "update_at": "2023-11-27 03:19:21",
                    "tahun_anggaran": "2023"
                }
            ],
            "sub_keg_indikator_hasil": [
                {
                    "id": "1504",
                    "hasilteks": "Nilai SAKIP Kecamatan Porong",
                    "satuanhasil": "Nilai",
                    "targethasil": "8.3",
                    "targethasilteks": "8.3 Nilai",
                    "kode_sbl": "529.529.797.3705.13480",
                    "idsubbl": "0",
                    "active": "1",
                    "update_at": "2023-11-27 03:19:21",
                    "tahun_anggaran": "2023"
                }
            ],
            "tag_sub_keg": [
                {
                    "id": "6874",
                    "idlabelgiat": "0",
                    "namalabel": "",
                    "idtagbl": "1208338",
                    "kode_sbl": "529.529.797.3705.13480",
                    "idsubbl": "0",
                    "active": "1",
                    "update_at": "2023-11-27 03:19:21",
                    "tahun_anggaran": "2023"
                }
            ],
            "capaian_prog_sub_keg": [
                {
                    "id": "2042",
                    "satuancapaian": "%",
                    "targetcapaianteks": "100 %",
                    "capaianteks": "Persentase desa yang mendapat pembinaan tentang lingkungan hidup,  usaha ekonomi masyarakat, dan PKL",
                    "targetcapaian": "100",
                    "kode_sbl": "529.529.797.3705.13480",
                    "idsubbl": "0",
                    "active": "1",
                    "update_at": "2023-11-27 03:19:21",
                    "tahun_anggaran": "2023"
                },
                {
                    "id": "2043",
                    "satuancapaian": "%",
                    "targetcapaianteks": "0 %",
                    "capaianteks": "Persentase izin yang menjadi  kewenangan kecamatan yang  diterbitkan",
                    "targetcapaian": "0",
                    "kode_sbl": "529.529.797.3705.13480",
                    "idsubbl": "0",
                    "active": "1",
                    "update_at": "2023-11-27 03:19:22",
                    "tahun_anggaran": "2023"
                },
                {
                    "id": "2044",
                    "satuancapaian": "%",
                    "targetcapaianteks": "100 %",
                    "capaianteks": "Persentase Pelayanan kewenangan kecamatan yang dilaksanakan sesuai standar",
                    "targetcapaian": "100",
                    "kode_sbl": "529.529.797.3705.13480",
                    "idsubbl": "0",
                    "active": "1",
                    "update_at": "2023-11-27 03:19:22",
                    "tahun_anggaran": "2023"
                }
            ],
            "output_giat": [
                {
                    "id": "8367",
                    "outputteks": "Jumlah laporan hasil koordinasi  bidang kesejahteraan sosial,  agama dan kemasyarakatan;  pembangunan dan lingkungan  hidup; pemerintahan dan  perekonomian yang disusun",
                    "satuanoutput": "Laporan",
                    "targetoutput": "17",
                    "targetoutputteks": "17 Laporan",
                    "kode_sbl": "529.529.797.3705.13480",
                    "idsubbl": "0",
                    "active": "1",
                    "update_at": "2023-11-27 03:19:22",
                    "tahun_anggaran": "2023"
                }
            ],
            "lokasi_sub_keg": [
                {
                    "id": "10952",
                    "camatteks": "Porong",
                    "daerahteks": "Kab. Sidoarjo",
                    "idcamat": "1817",
                    "iddetillokasi": "1127624",
                    "idkabkota": "101",
                    "idlurah": "0",
                    "lurahteks": "Semua Kelurahan",
                    "kode_sbl": "529.529.797.3705.13480",
                    "idsubbl": "0",
                    "active": "1",
                    "update_at": "2023-11-27 03:19:22",
                    "tahun_anggaran": "2023"
                }
            ],
            "sumber_dana": [
                {
                    "id_sumber_dana": "281",
                    "kode_dana": "1.1",
                    "nama_dana": "[DANA UMUM] - PENDAPATAN ASLI DAERAH (PAD)"
                }
            ]
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-rka-by-kodesbl">
            <h2>GET RKA berdasarkan Kode SBL</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_sub_keg_rka_sipd' \
-F 'tahun_anggaran=2023' \
-F 'kode_sbl=3276.3276.825.3804.14140' \
                </code>
            </pre>
            <p>
                Untuk menampilkan Rincian Belanja pada RKA SKPD berdasarkan kode rincian belanja (kode SBL) dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
                <code class="higlighted break-word">
                    <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php
                </code>
            <h4>QUERY PARAMETERS</h4>
            <table class="central-overflow-x" style="width:40%">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Keterangan</th>
                        <th>Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-family:Consolas">
                                api_key
                            </span>
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Kunci API yang telah ditambahkan dan diaktifkan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            action
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            tahun_anggaran
                        </td>
                        <td>
                            Angka
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            kode_sbl
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian atau filter berdasarkan kode belanja RKA
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
            <pre>
                <code class="json">
Result :
{
    "action": "get_sub_keg_rka_sipd",
    "status": "success",
    "message": "Berhasil RKA get sub kegiatan!",
    "data": [
        {
            "id": "1174310",
            "created_user": null,
            "createddate": null,
            "createdtime": null,
            "harga_satuan": "30000",
            "harga_satuan_murni": "30000",
            "id_daerah": "101",
            "id_jadwal": null,
            "id_rinci_sub_bl": "13055791",
            "id_standar_harga": null,
            "is_locked": null,
            "jenis_bl": "barjas-modal",
            "ket_bl_teks": "[-]  ",
            "substeks": "-",
            "id_dana": "281",
            "nama_dana": "PENDAPATAN ASLI DAERAH (PAD)",
            "is_paket": "0",
            "kode_dana": "1.1",
            "subtitle_teks": "-",
            "id_akun": null,
            "kode_akun": "5.1.02.01.01.0052",
            "koefisien": "245 Orang / Kali",
            "koefisien_murni": "245 Orang / Kali",
            "lokus_akun_teks": "",
            "nama_akun": "5.1.02.01.01.0052 Belanja Makanan dan Minuman Rapat ",
            "nama_komponen": "Konsumsi Rapat/Kegiatan (Makan)",
            "spek_komponen": "Spesifikasi :  ",
            "satuan": "Orang/Kali",
            "spek": "Spesifikasi :  ",
            "sat1": "Orang / Kali",
            "sat2": "",
            "sat3": "",
            "sat4": "",
            "volum1": "245",
            "volum2": "",
            "volum3": "",
            "volum4": "",
            "volume": "245",
            "volume_murni": "245",
            "subs_bl_teks": "[#] -",
            "total_harga": "7350000",
            "rincian": "7350000",
            "rincian_murni": "7350000",
            "totalpajak": "0",
            "pajak": "0",
            "pajak_murni": "0",
            "updated_user": null,
            "updateddate": null,
            "updatedtime": null,
            "user1": "WAHIB ACHMADI, ST., MT",
            "user2": "",
            "active": "1",
            "update_at": "2023-11-27 03:19:31",
            "tahun_anggaran": "2023",
            "idbl": "0",
            "idsubbl": "0",
            "kode_bl": "529.529.797.3705",
            "kode_sbl": "529.529.797.3705.13480",
            "id_prop_penerima": null,
            "id_camat_penerima": null,
            "id_kokab_penerima": null,
            "id_lurah_penerima": null,
            "id_penerima": null,
            "idkomponen": "0",
            "idketerangan": "0",
            "idsubtitle": "2287437",
            "id_standar_nfs": "127",
            "sumber_dana": [
                {
                    "id_sumber_dana": "281",
                    "kode_dana": "1.1",
                    "nama_dana": "[DANA UMUM] - PENDAPATAN ASLI DAERAH (PAD)"
                }
            ]
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-spd-by-tahun-anggaran">
            <h2>GET SPD berdasarkan SKPD dan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_spd_sipd' \
-F 'tahun_anggaran=2023' \
-F 'id_skpd=101' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data SPD berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
                <code class="higlighted break-word">
                    <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php
                </code>
            <h4>QUERY PARAMETERS</h4>
            <table class="central-overflow-x" style="width:40%">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Keterangan</th>
                        <th>Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-family:Consolas">
                                api_key
                            </span>
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Kunci API yang telah ditambahkan dan diaktifkan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            action
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            id_skpd
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            tahun_anggaran
                        </td>
                        <td>
                            Angka
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
            <pre>
                <code class="json">
Result :
{
    "action": "get_spd_sipd",
    "status": "success",
    "message": "Berhasil Get SPD SIPD!",
    "data": [
        {
            "idSpd": "1",
            "nomorSpd": "SPD001",
            "keteranganSpd": "Perjalanan Dinas 1",
            "ketentuanLainnya": "Ketentuan 1",
            "id_skpd": "101",
            "totalSpd": "1500",
            "active": "1",
            "created_at": "2024-02-07 10:30:00",
            "tahun_anggaran": "2024"
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-spp-by-tahun-anggaran">
            <h2>GET SPP berdasarkan SKPD dan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_spp_sipd' \
-F 'tahun_anggaran=2023' \
-F 'id_skpd=201' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data SPP berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
                <code class="higlighted break-word">
                    <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php
                </code>
            <h4>QUERY PARAMETERS</h4>
            <table class="central-overflow-x" style="width:40%">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Keterangan</th>
                        <th>Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-family:Consolas">
                                api_key
                            </span>
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Kunci API yang telah ditambahkan dan diaktifkan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            action
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            id_skpd
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            tahun_anggaran
                        </td>
                        <td>
                            Angka
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
            <pre>
                <code class="json">
Result :
{
    "action": "get_spp_sipd",
    "status": "success",
    "message": "Berhasil Get SPP SIPD!",
    "data": [
        {
            "id": "1",
            "nomorSpp": "SPP001",
            "nilaiSpp": "5000",
            "tanggalSpp": "2024-02-07",
            "keteranganSpp": "Pembayaran 1",
            "idSkpd": "201",
            "idSubUnit": "301",
            "nilaiDisetujuiSpp": "4800",
            "tanggalDisetujuiSpp": "2024-02-08",
            "jenisSpp": "LS",
            "verifikasiSpp": "1",
            "keteranganVerifikasi": "Terverifikasi",
            "idSpd": "1",
            "idPengesahanSpj": "101",
            "kunciRekening": "0",
            "alamatPenerimaSpp": "Alamat Penerima 1",
            "bankPenerimaSpp": "Bank Penerima 1",
            "nomorRekeningPenerimaSpp": "1234567890",
            "npwpPenerimaSpp": "NPWP123456789",
            "idUser": "1",
            "jenisLs": "Jenis LS 1",
            "isUploaded": "1",
            "tahunSpp": "2024",
            "idKontrak": "301",
            "idBA": "401",
            "created_at": "2024-02-07 10:30:00",
            "updated_at": "2024-02-07 10:30:00",
            "isSpm": "0",
            "statusPerubahan": "0",
            "isDraft": "1",
            "idSpp": "1",
            "kodeDaerah": "Kode Daerah 1",
            "idDaerah": "501",
            "isGaji": "0",
            "is_sptjm": "0",
            "tanggal_otorisasi": "2024-02-07 10:30:00",
            "is_otorisasi": "0",
            "bulan_gaji": "Bulan Gaji 1",
            "id_pegawai_pptk": "701",
            "nama_pegawai_pptk": "Nama PPTK 1",
            "nip_pegawai_pptk": "NIP PPTK 1",
            "id_jadwal": "801",
            "id_tahap": "901",
            "status_tahap": "Status Tahap 1",
            "kode_tahap": "Kode Tahap 1",
            "is_tpp": "1",
            "bulan_tpp": "2",
            "id_pengajuan_tu": "1001",
            "nomor_pengajuan_tu": "Nomor Pengajuan TU 1",
            "id_npd": "1101",
            "tipe": "Tipe 1",
            "active": "1",
            "update_at": "2024-02-07 10:30:00",
            "tahun_anggaran": "2024"
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-pembiayaan-by-tahun-anggaran">
            <h2>GET Pembiayaan berdasarkan SKPD dan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_pembiayaan_sipd' \
-F 'tahun_anggaran=2023' \
-F 'id_skpd=3282' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data Pembiayaan berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
                <code class="higlighted break-word">
                    <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php
                </code>
            <h4>QUERY PARAMETERS</h4>
            <table class="central-overflow-x" style="width:40%">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Keterangan</th>
                        <th>Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-family:Consolas">
                                api_key
                            </span>
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Kunci API yang telah ditambahkan dan diaktifkan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            action
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            id_skpd
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            tahun_anggaran
                        </td>
                        <td>
                            Angka
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
            <pre>
                <code class="json">
Result :
{
    "action": "get_pembiayaan_sipd",
    "status": "success",
    "message": "Berhasil Get Pembiayaan SIPD!",
    "data": [
        {
            "id": "1",
            "created_user": "23537",
            "createddate": "24-11-2020",
            "createdtime": "14:42",
            "id_pembiayaan": "1014",
            "keterangan": "Silpa",
            "kode_akun": "6.1.01.07.01.0001",
            "nama_akun": "Sisa Dana Akibat Tidak Tercapainya Capaian Target Kinerja",
            "nilaimurni": "17000000000",
            "program_koordinator": "0",
            "rekening": "6.1.01.07.01.0001 Sisa Dana Akibat Tidak Tercapainya Capaian Target Kinerja",
            "skpd_koordinator": "0",
            "total": "17000000000",
            "pagu_fmis": null,
            "updated_user": "21852",
            "updateddate": "27-11-2020",
            "updatedtime": "16:53",
            "uraian": "Sisa Dana Akibat Tidak Tercapainya Capaian Target Kinerja",
            "urusan_koordinator": "0",
            "type": "penerimaan",
            "user1": "FATONI KURNIAWAN, S.Sos M,Si",
            "user2": "PRABOWO ,S.Sos., M.Si",
            "id_skpd": "3282",
            "active": "1",
            "update_at": "2021-11-02 20:54:05",
            "tahun_anggaran": "2021",
            "id_akun": null,
            "id_jadwal_murni": null,
            "koefisien": null,
            "kua_murni": null,
            "kua_pak": null,
            "rkpd_murni": null,
            "rkpd_pak": null,
            "satuan": null,
            "volume": null
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-pendapatan-by-tahun-anggaran">
            <h2>GET Pendapatan berdasarkan SKPD dan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_pembiayaan_sipd' \
-F 'tahun_anggaran=2023' \
-F 'id_skpd=3300' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data Pendapatan berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
                <code class="higlighted break-word">
                    <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php
                </code>
            <h4>QUERY PARAMETERS</h4>
            <table class="central-overflow-x" style="width:40%">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Keterangan</th>
                        <th>Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-family:Consolas">
                                api_key
                            </span>
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Kunci API yang telah ditambahkan dan diaktifkan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            action
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            id_skpd
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            tahun_anggaran
                        </td>
                        <td>
                            Angka
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
            <pre>
                <code class="json">
Result :
{
    "action": "get_pembiayaan_sipd",
    "status": "success",
    "message": "Berhasil Get Pembiayaan SIPD!",
    "data": [
        {
            "id": "1",
            "created_user": "23837",
            "createddate": "23-11-2020",
            "createdtime": "12:31",
            "id_pendapatan": "19716",
            "keterangan": "",
            "kode_akun": "4.1.04.16.01.0001",
            "nama_akun": "Pendapatan BLUD",
            "nilaimurni": "845456027",
            "program_koordinator": "0",
            "rekening": "4.1.04.16.01.0001 Pendapatan BLUD",
            "skpd_koordinator": "0",
            "total": "845456027",
            "pagu_fmis": null,
            "updated_user": "0",
            "updateddate": "",
            "updatedtime": "",
            "uraian": "Pendapatan BLUD",
            "urusan_koordinator": "0",
            "user1": "dr. SULIS RAHADI",
            "user2": "",
            "id_skpd": "3300",
            "active": "1",
            "update_at": "2021-11-02 20:37:25",
            "tahun_anggaran": "2021",
            "id_akun": null,
            "id_jadwal_murni": null,
            "koefisien": null,
            "kua_murni": null,
            "kua_pak": null,
            "rkpd_murni": null,
            "rkpd_pak": null,
            "satuan": null,
            "volume": null
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-rak-by-tahun-anggaran">
            <h2>GET RAK berdasarkan SKPD dan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_rak_sipd' \
-F 'tahun_anggaran=2023' \
-F 'id_skpd=3300' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data RAK berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
                <code class="higlighted break-word">
                    <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php
                </code>
            <h4>QUERY PARAMETERS</h4>
            <table class="central-overflow-x" style="width:40%">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Keterangan</th>
                        <th>Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-family:Consolas">
                                api_key
                            </span>
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Kunci API yang telah ditambahkan dan diaktifkan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            action
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            id_skpd
                        </td>
                        <td>
                            String
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            tahun_anggaran
                        </td>
                        <td>
                            Angka
                        </td>
                        <td>
                            Bidang spesifik yang akan dilakukan pencarian
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
            <pre>
                <code class="json">
Result :
{
    "action": "get_rak_sipd",
    "status": "success",
    "message": "Berhasil Get RAK SIPD!",
    "data": [
        {
            "id":"1",
            "bulan_1":"1173063559",
            "bulan_2":"0",
            "bulan_3":"0",
            "bulan_4":"0",
            "bulan_5":"0",
            "bulan_6":"0",
            "bulan_7":"0",
            "bulan_8":"0",
            "bulan_9":"0",
            "bulan_10":"0",
            "bulan_11":"0",
            "bulan_12":"0",
            "id_akun":"16119",
            "id_bidang_urusan":"101",
            "id_daerah":"89",
            "id_giat":"3797",
            "id_program":"825",
            "id_skpd":"3259",
            "id_sub_giat":"14073",
            "id_sub_skpd":"3259",
            "id_unit":"3259",
            "kode_akun":"5.1.01.01.01.0001",
            "nama_akun":"5.1.01.01.01.0001 Belanja Gaji Pokok PNS",
            "selisih":"0",
            "tahun":null,
            "total_akb":"1173063559",
            "total_rincian":"1173063559",
            "active":"1",
            "kode_sbl":"3259.3259.3259.101.825.3797.14073",
            "type":"belanja",
            "tahun_anggaran":"2023",
            "updated_at":"2023-01-24 17:32:14"
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-errors">
            <h2>Errors</h2>
            <p>
                The WP-SIPD API uses error status:
            </p>
            <table style="width:40%">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge badge-danger">error</span></td>
                        <td>
                            Format Data Salah.
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-danger">error</span></td>
                        <td>
                            APIKEY tidak sesuai!.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="content-code"></div>
</div>
<script>
    var elements = [];

    [].forEach.call(document.querySelectorAll('.scroll-to-link'), function(div) {
        div.onclick = function(e) {
            e.preventDefault();
            var target = this.dataset.target;
            document.getElementById(target).scrollIntoView({
                behavior: 'smooth'
            });
            var elems = document.querySelectorAll(".content-menu ul li");
            [].forEach.call(elems, function(el) {
                el.classList.remove("active");
            });
            this.classList.add("active");
            return false;
        };
    });

    document.getElementById('button-menu-mobile').onclick = function(e) {
        e.preventDefault();
        document.querySelector('html').classList.toggle('menu-opened');
    }
    document.querySelector('.left-menu .mobile-menu-closer').onclick = function(e) {
        e.preventDefault();
        document.querySelector('html').classList.remove('menu-opened');
    }

    function debounce(func) {
        var timer;
        return function(event) {
            if (timer) clearTimeout(timer);
            timer = setTimeout(func, 100, event);
        };
    }

    function calculElements() {
        var totalHeight = 0;
        elements = [];
        [].forEach.call(document.querySelectorAll('.content-section'), function(div) {
            var section = {};
            section.id = div.id;
            totalHeight += div.offsetHeight;
            section.maxHeight = totalHeight - 25;
            elements.push(section);
        });
        onScroll();
    }

    function onScroll() {
        var scroll = window.pageYOffset;
        console.log('scroll', scroll, elements)
        for (var i = 0; i < elements.length; i++) {
            var section = elements[i];
            if (scroll <= section.maxHeight) {
                var elems = document.querySelectorAll(".content-menu ul li");
                [].forEach.call(elems, function(el) {
                    el.classList.remove("active");
                });
                var activeElems = document.querySelectorAll(".content-menu ul li[data-target='" + section.id + "']");
                [].forEach.call(activeElems, function(el) {
                    el.classList.add("active");
                });
                break;
            }
        }
        if (window.innerHeight + scroll + 5 >= document.body.scrollHeight) { // end of scroll, last element
            var elems = document.querySelectorAll(".content-menu ul li");
            [].forEach.call(elems, function(el) {
                el.classList.remove("active");
            });
            var activeElems = document.querySelectorAll(".content-menu ul li:last-child");
            [].forEach.call(activeElems, function(el) {
                el.classList.add("active");
            });
        }
    }

    calculElements();
    window.onload = () => {
        calculElements();
    };
    window.addEventListener("resize", debounce(function(e) {
        e.preventDefault();
        calculElements();
    }));
    window.addEventListener('scroll', function(e) {
        e.preventDefault();
        onScroll();
    });
</script>