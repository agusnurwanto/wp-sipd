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
        <li class="scroll-to-link" data-target="content-get-sub-kegiatan">
            <a>Get Master Sub Kegiatan</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-rekening">
            <a>Get Master Rekening</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-sumberdana">
            <a>Get Master Sumber Dana</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-subkeg-by-skpd">
            <a>Get Pagu Sub Kegiatan</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-rka-by-kodesbl">
            <a>Get RKA</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-pembiayaan-by-tahun-anggaran">
            <a>Get Pembiayaan</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-pendapatan-by-tahun-anggaran">
            <a>Get Pendapatan</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-up-by-tahun-anggaran">
            <a>Get SK UP</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-rak-by-tahun-anggaran">
            <a>Get RAK</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-spd-by-tahun-anggaran">
            <a>Get SPD</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-pegawai">
            <a>Get Pegawai Penatausahaan</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-spp-by-tahun-anggaran">
            <a>Get SPP</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-spm-by-tahun-anggaran">
            <a>Get SPM</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-sp2d-by-tahun-anggaran">
            <a>Get SP2D</a>
        </li>
        <li class="scroll-to-link" data-target="content-get-capaian-kinerja-by-tahun-anggaran">
            <a>Get Capaian Kinerja & Serapan Anggaran</a>
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
                            get_skpd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
            "id_unit": "xxxxx",
            "is_skpd": "0",
            "kode_skpd": "7.01.xxxxxxxxxxxxxxxxx",
            "kunci_skpd": "0",
            "nama_skpd": "xxxxxxxxxxxxxxx",
            "posisi": "",
            "status": "Unit SKPD",
            "id_skpd": "xxxxx",
            "bidur_1": "47",
            "bidur_2": "0",
            "bidur_3": "0",
            "idinduk": "529",
            "ispendapatan": "0",
            "isskpd": "0",
            "kode_skpd_1": "06",
            "kode_skpd_2": "0006",
            "kodeunit": "7.01.xxxxxxxxxxxxxxxxx",
            "komisi": "0",
            "namabendahara": "",
            "namakepala": "xxxxxxxxxxx",
            "namaunit": "xxxxxxxxxxxxxxx",
            "nipbendahara": "",
            "nipkepala": "xxxxxx",
            "pangkatkepala": "xxxxxxxxxxx",
            "setupunit": "0",
            "statuskepala": "xxxxxxxx",
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
        <div class="overflow-hidden content-section" id="content-get-sub-kegiatan">
            <h2>GET Master Sub Kegiatan</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_master_sub_keg_sipd' \
-F 'tahun_anggaran=2023' \
                </code>
            </pre>
            <p>
                Untuk menampilkan master data sub kegiatan berdasarkan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
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
                            get_master_sub_keg_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
    "action": "get_master_sub_keg_sipd",
    "run": null,
    "status": "success",
    "message": "Berhasil mendapatkan data sub kegiatan!",
    "data": [
        {
            "id": "15154",
            "id_bidang_urusan": "205",
            "id_program": "1029",
            "kode_program": "1.05.02",
            "nama_program": "1.05.02 PROGRAM PENINGKATAN KETENTERAMAN DAN KETERTIBAN UMUM",
            "id_giat": "8125",
            "kode_giat": "1.05.02.2.03",
            "nama_giat": "1.05.02.2.03 Pembinaan Penyidik Pegawai Negeri Sipil (PPNS) Kabupaten/Kota",
            "id_sub_giat": "18108",
            "kode_sub_giat": "1.05.02.2.03.0002",
            "nama_sub_giat": "1.05.02.2.03.0002 Pembentukan Sekretariat PPNS"
        }
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-rekening">
            <h2>GET REKENING</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_rekening_akun' \
-F 'tahun_anggaran=2023' \
                </code>
            </pre>
            <p>
                Untuk menampilkan rekening berdasarkan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
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
                            get_rekening_akun
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
                            Contoh <b>2023</b> atau <b>2024</b>
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
    "action": "get_rekening_akun",
    "status": "true",
    "data": [
        {
            "id_akun": "16119",
            "kode_akun": "5.1.01.01.01.0001",
            "nama_akun": "Belanja Gaji Pokok PNS"
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-sumberdana">
            <h2>GET SUMBER DANA DESA</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_sumber_dana_desa' \
-F 'tahun_anggaran=2023' \
                </code>
            </pre>
            <p>
                Untuk menampilkan sumber dana berdasarkan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
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
                            get_sumber_dana_desa
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
                            Contoh <b>2023</b> atau <b>2024</b>
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
    "action": "get_sumber_dana_desa",
    "status": "true",
    "data": [
        {
            "id_dana": "282",
            "kode_dana": "1.1.01",
            "nama_dana": "Pajak Daerah"
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
                            get_sub_keg_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
            "nama_skpd": "xxxxxxxxxx xxxxxx",
            "kode_sub_skpd": "7.01.0.00.0.00.06.0000",
            "id_skpd": "529",
            "id_sub_bl": "0",
            "nama_sub_skpd": "xxxxxxxxxx xxxxxx",
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
            "nama_skpd_data_unit": "xxxxxxxxxx xxxxxxx",
            "nama_skpd_data_unit_utama": "xxxxxxxxxx xxxxxxx",
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
                    "hasilteks": "xxxxxxxxxxxxxxxxxx",
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
                    "camatteks": "xxxxxxx",
                    "daerahteks": "xxxxxxxxxxxx",
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
                            get_sub_keg_rka_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
            "user1": "XXXXXXXXXXXXX, XX, XX",
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
                            get_pembiayaan_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
                        <td class="teParameter tidak lengkap!xt-center">
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
                            get_pembiayaan_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
            "nilaimurni": "xxxxxxxxxxxx",
            "program_koordinator": "0",
            "rekening": "4.1.04.16.01.0001 Pendapatan BLUD",
            "skpd_koordinator": "0",
            "total": "xxxxxxxxxxxx",
            "pagu_fmis": null,
            "updated_user": "0",
            "updateddate": "",
            "updatedtime": "",
            "uraian": "Pendapatan BLUD",
            "urusan_koordinator": "0",
            "user1": "xxxxxxxxxxxxxxxxxxx",
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
        <div class="overflow-hidden content-section" id="content-get-up-by-tahun-anggaran">
            <h2>GET UP berdasarkan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_up_sipd' \
-F 'tahun_anggaran=2023' \
-F 'type=pendapatan' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data SK UP berdasarkan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
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
                            get_up_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            type
                        </td>
                        <td>
                            string
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
    "action": "get_up_sipd",
    "status": "success",
    "message": "Berhasil get Data SK UP",
    "data": [
        {
            "id":"1",
            "id_besaran_up":"604",
            "id_skdp":"3254",
            "pagu":"118079125327",
            "active":"1",
            "tahun_anggaran":"2023",
            "id_sub_skpd":"10626",
            "besaran_up":"210000000",
            "besaran_up_kkpd":"140000000"
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-rak-by-tahun-anggaran">
            <h2>GET RAK berdasarkan SKPD, Tipe dan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_rak_sipd' \
-F 'tahun_anggaran=2023' \
-F 'id_skpd=3283' \
-F 'type=pendapatan' \
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
                            get_rak_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">
                                Dibutuhkan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            type
                        </td>
                        <td>
                            string
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
            "id":"8756",
            "bulan_1":"16500000",
            "bulan_2":"0",
            "bulan_3":"0",
            "bulan_4":"27500000",
            "bulan_5":"0",
            "bulan_6":"0",
            "bulan_7":"38500000",
            "bulan_8":"0",
            "bulan_9":"0",
            "bulan_10":"27500000",
            "bulan_11":"0",
            "bulan_12":"0",
            "id_akun":"8917",
            "id_bidang_urusan":null,
            "id_daerah":"xx",
            "id_giat":null,
            "id_program":null,
            "id_skpd":"3283",
            "id_sub_giat":null,
            "id_sub_skpd":"3283",
            "id_unit":"3283",
            "kode_akun":"4.1.01.06.01.0001",
            "nama_akun":"Pajak Hotel",
            "selisih":null,
            "tahun":"2024",
            "total_akb":"110000000",
            "total_rincian":"110000000",
            "active":"1",
            "kode_sbl":"3283.5.02.0.00.0.00.02.0000",
            "type":"pendapatan",
            "tahun_anggaran":"2024",
            "updated_at":"2024-01-26 15:56:57"
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
-F 'tahun_anggaran=2024' \
-F 'id_skpd=3346' \
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
                            get_spd_sipd
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
                            Contoh <b>2023</b> atau <b>2024</b>
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
            "id":"1",
            "idSpd":"1758",
            "nomorSpd":"35.19/01.0/000001/2.19.3.26.0.00.01.0000/M/2/2024",
            "keteranganSpd":"TW1-M",
            "ketentuanLainnya":null,
            "id_skpd":"3346",
            "totalSpd":null,
            "active":"1",
            "created_at":"2024-02-07 15:54:49",
            "tahun_anggaran":"2024",
            "idDetailSpd":"16420",
            "id_program":null,
            "id_giat":"8710",
            "id_sub_giat":"20302",
            "id_akun":"16420",
            "nilai":"7106000",
            "kode_akun": "5.1.02.02.05.0037",
            "nama_akun": "Belanja Sewa Bangunan Gedung Tempat Kerja Lainnya",
            "kode_program": "3.26.05",
            "nama_program": "3.26.05 PROGRAM PENGEMBANGAN SUMBER DAYA PARIWISATA DAN EKONOMI KREATIF",
            "kode_giat": "3.26.05.2.01",
            "nama_giat": "3.26.05.2.01 Pelaksanaan Peningkatan Kapasitas Sumber Daya Manusia Pariwisata dan Ekonomi Kreatif Tingkat Dasar",
            "kode_sub_giat": "3.26.05.2.01.0006",
            "nama_sub_giat": "3.26.05.2.01.0006 Fasilitasi Pengembangan Kompetensi Sumber Daya Manusia Ekonomi Kreatif"
        },
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-pegawai">
            <h2>GET Pegawai Penatausahaan</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_pegawai_sipd' \
-F 'tahun_anggaran=2024' \
-F 'id_skpd=3346' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data Pegawai berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
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
                            get_pegawai_sipd
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
                            Contoh <b>2023</b> atau <b>2024</b>
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
    "action": "get_pegawai_sipd",
    "status": "success",
    "message": "Berhasil Get Pegawai SIPD!",
    "data": [
       {
            id: '1',
            idSkpd: '1111',
            namaSkpd: '',
            kodeSkpd: '',
            idDaerah: 'xx',
            userName: 'xxxxxxxx',
            nip: 'xxxxxxxxxx',
            nik: 'xxxxxxxxxx',
            fullName: 'Pegawai A',
            lahir_user: '0001-01-01 00:00:00',
            nomorHp: '',
            isRank: '',
            npwp: 'xxxxxxxxxxx',
            idJabatan: 0,
            namaJabatan: BENDAHARA PENGELUARAN,
            idRole: 3,
            _order: 0,
            kpa: '',
            bank: '',
            _group: '',
            kodeBank: '',
            nama_rekening: '',
            nomorRekening: '',
            pangkatGolongan: '',
            tahunPegawai: 2024,
            kodeDaerah: 0,
            is_from_sipd: 0,
            is_from_generate: 0,
            is_from_external: 0,
            idSubUnit: 0,
            idUser: 100,
            idPegawai: 0,
            alamat: 'xxxxxxxxxx',
            tahun_anggaran: '2024',
            updated_at: '0001-01-01 00:00:00',
       }
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
-F 'tahun_anggaran=2024' \
-F 'id_skpd=3346' \
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
                            get_spp_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
    "data": [{
        "id": "18188",
        "nomorSpp": "35.19/02.0/000005/LS/2.19.3.26.0.00.01.0000/P/2/2024",
        "nilaiSpp": "116338787",
        "tanggalSpp": "2024-02-07",
        "keteranganSpp": "Pembayaran Gaji Bulan Februari 2024 Dinas Pariwisata, Pemuda, dan Olahraga (DAU)",
        "idSkpd": "3346",
        "idSubUnit": "0",
        "nilaiDisetujuiSpp": "116338787",
        "tanggalDisetujuiSpp": null,
        "jenisSpp": "LS",
        "verifikasiSpp": "1",
        "keteranganVerifikasi": "",
        "idSpd": null,
        "idPengesahanSpj": null,
        "kunciRekening": "0",
        "alamatPenerimaSpp": null,
        "bankPenerimaSpp": null,
        "nomorRekeningPenerimaSpp": null,
        "npwpPenerimaSpp": null,
        "idUser": "0",
        "jenisLs": "gaji",
        "isUploaded": "0",
        "tahunSpp": "2024",
        "idKontrak": "0",
        "idBA": "0",
        "created_at": "2024-02-07 09:45:37",
        "updated_at": "0001-01-01 00:00:00",
        "isSpm": "0",
        "statusPerubahan": "0",
        "isDraft": null,
        "idSpp": "23051",
        "kodeDaerah": null,
        "idDaerah": "89",
        "isGaji": "0",
        "is_sptjm": null,
        "tanggal_otorisasi": "0001-01-01 00:00:00",
        "is_otorisasi": "1",
        "bulan_gaji": "2",
        "id_pegawai_pptk": "0",
        "nama_pegawai_pptk": null,
        "nip_pegawai_pptk": null,
        "id_jadwal": "0",
        "id_tahap": "0",
        "status_tahap": "",
        "kode_tahap": "",
        "is_tpp": "0",
        "bulan_tpp": "0",
        "id_pengajuan_tu": "0",
        "nomor_pengajuan_tu": null,
        "id_npd": null,
        "tipe": "LS",
        "active": "1",
        "update_at": "2024-02-20 00:56:48",
        "tahun_anggaran": "2024",
        "detail": [
            {
                "id": "3479",
                "id_skpd": "3346",
                "id_spp": "23051",
                "nomor_spd": "35.19/01.0/000001/2.19.3.26.0.00.01.0000/M/2/2024",
                "tanggal_spd": "2024-02-05 00:00:00",
                "total_spd": "7307025031",
                "jumlah": "0",
                "kode_rekening": "",
                "uraian": "NOMOR SPD: 35.19/01.0/000001/2.19.3.26.0.00.01.0000/M/2/2024",
                "bank_bp_bpp": "Bank JATIM",
                "jabatan_bp_bpp": "BENDAHARA PENGELUARAN",
                "jabatan_pa_kpa": "PENGGUNA ANGGARAN",
                "jenis_ls_spp": "gaji",
                "keterangan": "Pembayaran Gaji Bulan Februari 2024 Dinas Pariwisata, Pemuda, dan Olahraga (DAU)",
                "nama_bp_bpp": "xxxxxxxxxxxx",
                "nama_daerah": "Kab. Madiun",
                "nama_ibukota": "Caruban",
                "nama_pa_kpa": "xxxxxxxxxxxx",
                "nama_pptk": "xxxxxxxxxxxxxxxx",
                "nama_rek_bp_bpp": "BEND PENG DISPARPORA KABMADIUN",
                "nama_skpd": "Dinas Pariwisata Pemuda dan Olah Raga",
                "nama_sub_skpd": "Dinas Pariwisata Pemuda dan Olah Raga",
                "nilai": "116338787",
                "nip_bp_bpp": "xxxxxxxxxxxx",
                "nip_pa_kpa": "xxxxxxxxxxx",
                "nip_pptk": "xxxxxxxxxxxx",
                "no_rek_bp_bpp": "xxxxxxxxx",
                "nomor_transaksi": "35.19/02.0/000005/LS/2.19.3.26.0.00.01.0000/P/2/2024",
                "npwp_bp_bpp": "xxxxxxxxxxxxx",
                "tahun": "2024",
                "tanggal_transaksi": "2024-02-07 00:00:00",
                "tipe": "LS",
                "active": "1",
                "update_at": "2024-02-20 01:43:24",
                "tahun_anggaran": "2024"
            }
        ]
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-spm-by-tahun-anggaran">
            <h2>GET SPM berdasarkan SKPD dan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_spm_sipd' \
-F 'tahun_anggaran=2024' \
-F 'id_skpd=3346' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data SPM berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
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
                            get_spm_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
    "action": "get_spm_sipd",
    "status": "success",
    "message": "Berhasil Get SPM SIPD!",
    "data": [
        {
            "id": "29",
            "idSpm": "15248",
            "idSpp": "17426",
            "created_at": "2024-02-01 22:22:38",
            "updated_at": "0001-01-01 00:00:00",
            "idDetailSpm": null,
            "id_skpd": "3346",
            "tahun_anggaran": "2024",
            "id_jadwal": "0",
            "id_tahap": "0",
            "status_tahap": "",
            "nomorSpp": "35.19/02.0/000004/UP/2.19.3.26.0.00.01.0000/M/2/2024",
            "nilaiSpp": "210000000",
            "tanggalSpp": null,
            "keteranganSpp": "Pembayaran Uang Persediaan ( UP TUNAI / REGULER ) Pada Dinas Pariwisata Pemuda dan Olahraga Kabupaten Madiun Tahun Anggaran 2024",
            "idSkpd": null,
            "idSubUnit": "3346",
            "nilaiDisetujuiSpp": "210000000",
            "tanggalDisetujuiSpp": null,
            "jenisSpp": "0",
            "verifikasiSpp": "1",
            "keteranganVerifikasi": null,
            "idSpd": null,
            "idPengesahanSpj": null,
            "kunciRekening": "1",
            "alamatPenerimaSpp": null,
            "bankPenerimaSpp": null,
            "nomorRekeningPenerimaSpp": null,
            "npwpPenerimaSpp": null,
            "jenisLs": "",
            "isUploaded": null,
            "tahunSpp": "2024",
            "idKontrak": null,
            "idBA": null,
            "isSpm": "1",
            "statusPerubahan": "1",
            "isDraft": null,
            "isGaji": null,
            "is_sptjm": "1",
            "tanggal_otorisasi": null,
            "is_otorisasi": null,
            "bulan_gaji": "0",
            "id_pegawai_pptk": null,
            "nama_pegawai_pptk": null,
            "nip_pegawai_pptk": null,
            "kode_tahap": "",
            "is_tpp": null,
            "bulan_tpp": null,
            "id_pengajuan_tu": null,
            "nomor_pengajuan_tu": null,
            "nomorSpm": null,
            "tanggalSpm": "2024-02-02",
            "keteranganSpm": "Pembayaran Uang Persediaan ( UP TUNAI / REGULER ) Pada Dinas Pariwisata Pemuda dan Olahraga Kabupaten Madiun Tahun Anggaran 2024",
            "verifikasiSpm": "1",
            "tanggalVerifikasiSpm": "0001-01-01",
            "jenisSpm": "UP",
            "nilaiSpm": "210000000",
            "keteranganVerifikasiSpm": "",
            "isOtorisasi": null,
            "tanggalOtorisasi": null,
            "active": "1",
            "update_at": "2024-02-22 07:02:47",
            "detail": [],
            "potongan": []
        }
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-sp2d-by-tahun-anggaran">
            <h2>GET SP2D berdasarkan SKPD dan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_sp2d_sipd' \
-F 'tahun_anggaran=2024' \
-F 'id_skpd=3346' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data SP2D berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
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
                            get_sp2d_sipd
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
    "status": "success",
    "message": "Berhasil Get SP2D SIPD!",
    "data": [
        {
            "id": "19",
            "bulan_gaji": "2",
            "bulan_tpp": "0",
            "created_at": "2024-02-07 10:02:28",
            "created_by": "0",
            "deleted_at": "0001-01-01 00:00:00",
            "deleted_by": "0",
            "id_bank": "0",
            "id_daerah": "89",
            "id_jadwal": "0",
            "id_pegawai_bud_kbud": "41404",
            "id_rkud": "0",
            "id_skpd": "3346",
            "id_sp_2_d": "13902",
            "id_spm": "20523",
            "id_sub_skpd": "3346",
            "id_sumber_dana": "0",
            "id_tahap": "0",
            "id_unit": "0",
            "is_gaji": "0",
            "is_kunci_rekening_sp_2_d": "0",
            "is_pelimpahan": "0",
            "is_status_perubahan": "0",
            "is_tpp": "0",
            "is_transfer_sp_2_d": "0",
            "is_verifikasi_sp_2_d": "1",
            "jenis_gaji": "0",
            "jenis_ls_sp_2_d": "gaji",
            "jenis_rkud": "",
            "jenis_sp_2_d": "LS",
            "jurnal_id": "0",
            "keterangan_sp_2_d": "Pembayaran Gaji Bulan Februari 2024 Dinas Pariwisata, Pemuda, dan Olahraga (DAU)",
            "keterangan_transfer_sp_2_d": "",
            "keterangan_verifikasi_sp_2_d": "",
            "kode_skpd": "",
            "kode_sub_skpd": "2.19.3.26.0.00.01.0000",
            "kode_tahap": "",
            "metode": "",
            "nama_bank": "",
            "nama_bud_kbud": "",
            "nama_rek_bp_bpp": "",
            "nama_skpd": "",
            "nama_sub_skpd": "Dinas Pariwisata Pemuda dan Olah Raga",
            "nilai_materai_sp_2_d": "0",
            "nilai_sp_2_d": "116338787",
            "nip_bud_kbud": "",
            "no_rek_bp_bpp": "",
            "nomor_jurnal": "",
            "nomor_sp_2_d": "35.19/04.0/000003/LS/2.19.3.26.0.00.01.0000/M/2/2024",
            "nomor_spm": "35.19/03.0/000005/LS/2.19.3.26.0.00.01.0000/M/2/2024",
            "status_aklap": "0",
            "status_perubahan_at": "0001-01-01 00:00:00",
            "status_perubahan_by": "0",
            "status_tahap": "",
            "tahun": "2024",
            "tahun_gaji": "0",
            "tahun_tpp": "0",
            "tanggal_sp_2_d": "2024-02-07 00:00:00",
            "tanggal_spm": "0001-01-01 00:00:00",
            "transfer_sp_2_d_at": "0001-01-01T00:00:00Z",
            "transfer_sp_2_d_by": "0",
            "updated_at": "0001-01-01 00:00:00",
            "updated_by": "0",
            "verifikasi_sp_2_d_at": "0001-01-01 00:00:00",
            "verifikasi_sp_2_d_by": "0",
            "active": "1",
            "tahun_anggaran": "2024",
            "detail": [],
            "potongan": []
        }
    ]
}
                </code>
            </pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-capaian-kinerja-by-tahun-anggaran">
            <h2>GET Persentase Capaian Kinerja dan Serapan Anggaran berdasarkan SKPD dan Tahun Anggaran</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=<?php echo $key; ?>' \
-F 'action=get_serapan_anggaran_capaian_kinerja' \
-F 'tahun_anggaran=2024' \
-F 'id_skpd=3346' \
                </code>
            </pre>
            <p>
                Untuk menampilkan semua data Persentase Capaian Kinerja dan Serapan Anggaran berdasarkan SKPD dan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
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
                            get_serapan_anggaran_capaian_kinerja
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
                            Contoh tahun <b>2023</b> atau <b>2024</b>
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
  "status": "success",
  "message": "Berhasil Get Serapan Anggaran dan Capaian Kinerja!",
  "data": {
    "capaian_kinerja": {
      "total": "5.88%",
      "tw1": "5.88%",
      "tw2": "0%",
      "tw3": "0%",
      "tw4": "0%"
    },
    "serapan_anggaran": {
      "total": "35.32%",
      "tw1": "72.9%",
      "tw2": "40.24%",
      "tw3": "0%",
      "tw4": "0%"
    },
    "anggaran": {
      "total": 552258803301,
      "tw1": 166081537531.96,
      "tw2": 166081537531.96,
      "tw3": 166081537531.96,
      "tw4": 166081537531.96
    },
    "realisasi_anggaran": {
      "total": 195068541627,
      "tw1": 121066799019,
      "tw2": 74001742608,
      "tw3": 0,
      "tw4": 0
    },
    "opd": [
      {
        "id": "173",
        "id_setup_unit": "xxxx",
        "id_unit": "xxxx",
        "is_skpd": "1",
        "kode_skpd": "1.xx.xxx.xxx.xxx.xxx.xxxxxx.xx",
        "kunci_skpd": "0",
        "nama_skpd": "DINAS XXXXXXXXXXXXXXX XXXXXXXX",
        "posisi": "",
        "status": "SKPD",
        "id_skpd": "2185",
        "bidur_1": "x",
        "bidur_2": "xx",
        "bidur_3": "x",
        "idinduk": "xxx",
        "ispendapatan": "1",
        "isskpd": "1",
        "kode_skpd_1": "xxx",
        "kode_skpd_2": "xxxx",
        "kodeunit": "1.xx.xxx.xxx.xxx.xxx.xxxxxx.xx",
        "komisi": null,
        "namabendahara": "",
        "namakepala": "xxxxx xxxx xxxxxx",
        "namaunit": "DINAS XXXXXXXXXXXXXXX XXXXXXXX",
        "nipbendahara": "",
        "nipkepala": "xxxxxxxxxxxxxxxxxx",
        "pangkatkepala": "xxxxxxxxx xxxxxxxx",
        "setupunit": "1",
        "statuskepala": "PA",
        "mapping": "",
        "id_kecamatan": null,
        "id_strategi": "xxxxxx",
        "is_dpa_khusus": "0",
        "is_ppkd": "0",
        "set_input": "1",
        "update_at": "2024-07-04 15:53:45",
        "tahun_anggaran": "2024",
        "active": "1"
      }
    ]
  }
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
                    <tr>
                        <td><span class="badge badge-danger">error</span></td>
                        <td>
                            Parameter tidak lengkap!.
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