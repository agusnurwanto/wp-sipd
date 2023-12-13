<!-- https://github.com/ticlekiwi/API-Documentation-HTML-Template !-->
<style>
.hljs {
    display:block;
    overflow-x:auto;
    padding:0.5em;
    background:#323F4C;
}
.hljs {
    color:#fff;
}
.hljs-strong,.hljs-emphasis {
    color:#a8a8a2
}
.hljs-bullet,.hljs-quote,.hljs-link,.hljs-number,.hljs-regexp,.hljs-literal {
    color:#6896ba
}
.hljs-code,.hljs-selector-class {
    color:#a6e22e
}
.hljs-emphasis {
    font-style:italic
}
.hljs-keyword,.hljs-selector-tag,.hljs-section,.hljs-attribute,.hljs-name,.hljs-variable {
    color:#cb7832
}
.hljs-params {
    color:#b9b9b9
}
.hljs-string {
    color:#6a8759
}
.hljs-subst,.hljs-type,.hljs-built_in,.hljs-builtin-name,.hljs-symbol,.hljs-selector-id,.hljs-selector-attr,.hljs-selector-pseudo,.hljs-template-tag,.hljs-template-variable,.hljs-addition {
    color:#e0c46c
}
.hljs-comment,.hljs-deletion,.hljs-meta {
    color:#7f7f7f
}


@charset "utf-8";

/* RESET
----------------------------------------------------------------------------------------*/

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed,
figure, figcaption, footer, hgroup,
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
    margin: 0;
    padding: 0;
    border: 0;
    font-size:100%;
    font: inherit;
    vertical-align: baseline;
}
article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {
    display: block;
}
img, embed, object, video { max-width: 100%; }
.ie6 img.full, .ie6 object.full, .ie6 embed, .ie6 video { width: 100%; }

/* BASE
----------------------------------------------------------------------------------------*/

*{
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    transition: all 0.3s ease;
}
html,
body{
    position:relative;
    min-height: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden;
    font-family: 'Roboto', sans-serif;
}
strong{
    font-weight: 500;
}
i{
    font-style: italic;
}
.overflow-hidden{
    position: relative;
    overflow: hidden;
}
.content a{
    color: #00a8e3;
    text-decoration: none;
}
.content a:hover{
    text-decoration: underline;
}
.scroll-to-link{
    cursor: pointer;
}
p, .content ul, .content ol{
    font-size: 14px;
    color: #777A7A;
    margin-bottom: 16px;
    line-height: 1.6;
    font-weight: 300;
}
.content h1:first-child{
    font-size: 1.333em;
    color: #034c8f;
    padding-top: 2.5em;
    text-transform: uppercase;
    border-top: 1px solid rgba(255,255,255,0.3);
    border-top-width: 0;
    margin-top: 0;
    margin-bottom: 1.3em;
    clear: both;
}

code,
pre{
    font-family: 'Source Code Pro', monospace;
}
.higlighted{
    background-color: rgba(0,0,0,0.05);
    padding: 3px;
    border-radius: 3px;
}

/* LEFT-MENU
----------------------------------------------------------------------------------------*/

.left-menu{
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
.content-logo{
    position: relative;
    display: block;
    width: 100%;
    box-sizing: border-box;
    padding: 1.425em 11.5%;
    padding-right: 0;
}
.content-logo img{
    display: inline-block;
    max-width: 70%;
    vertical-align: middle;
}
.content-logo span{
    display: inline-block;
    margin-left: 10px;
    vertical-align: middle;
    color: #323F4C;
    font-size: 1.1em;
}
.content-menu{
    margin: 2em auto 2em;
    padding: 0 0 100px;
}
.content-menu ul{
    list-style: none;
    margin: 0;
    padding: 0;
    line-height: 28px;
}
.content-menu ul li{
    list-style: none;
    margin: 0;
    padding: 0;
    line-height: 0;
}
.content-menu ul li:hover,
.content-menu ul li.active{
    background-color:#DCDEE9;
}
.content-menu ul li:hover a,
.content-menu ul li.active a{
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
.content-menu ul li a{
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
.content-code{
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
.content h2{
    font-size: 1.333em;
}
.content h4{
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
.content pre code, .content pre {
    font-size: 12px;
    line-height: 1.5;
}
.content blockquote,
.content pre,
.content pre code{
    padding-top: 0;
    margin-top: 0;
}
.content pre code{
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
    line-height: 1.6;
}
.content table td {
    padding: 5px 18px 5px 0;
    text-align: left;
    vertical-align: top;
    line-height: 1.6;
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
html.menu-opened .burger-menu-icon  .line2 {
    stroke-dasharray: 1 60;
    stroke-dashoffset: -30;
    stroke-width: 6;
}
html.menu-opened .burger-menu-icon  .line3 {
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

@media only screen and (max-width:980px){
    .content h1, .content h2, .content h3, .content h4, .content h5, .content h6, .content p, .content table, .content ul, .content ol, .content aside, .content dl, .content ul, .content ol {
        margin-right: 0;
    }
    .content .central-overflow-x {
        margin: 0;
        padding: 0 28px;
    }
    .content-code{
        display: none;
    }
    .content pre, .content blockquote {
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

@media only screen and (max-width:680px){
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
    .content-page{
        margin-left: 0;
        padding-top: 83px;
    }
    .burger-menu-icon {
        display: block;
    }
}

/* BROWSER AND NON-SEMANTIC STYLING
----------------------------------------------------------------------------------------*/

.cf:before, .cf:after { content: ""; display: block; }
.cf:after { clear: both; }
.ie6 .cf { zoom: 1 }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.8.0/highlight.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;1,300&family=Source+Code+Pro:wght@300&display=swap" rel="stylesheet">
<script>hljs.initHighlightingOnLoad();</script>
<div class="left-menu">
    <div class="content-logo">
        <div class="logo">
            <img alt="WP-SIPD" title="WP-SIPD" src="<?php echo WPSIPD_PLUGIN_URL; ?>public/images/logo.png" height="32" />
            <span>API Documentation</span>
        </div>
        <button class="burger-menu-icon" id="button-menu-mobile">
            <svg width="34" height="34" viewBox="0 0 100 100"><path class="line line1" d="M 20,29.000046 H 80.000231 C 80.000231,29.000046 94.498839,28.817352 94.532987,66.711331 94.543142,77.980673 90.966081,81.670246 85.259173,81.668997 79.552261,81.667751 75.000211,74.999942 75.000211,74.999942 L 25.000021,25.000058"></path><path class="line line2" d="M 20,50 H 80"></path><path class="line line3" d="M 20,70.999954 H 80.000231 C 80.000231,70.999954 94.498839,71.182648 94.532987,33.288669 94.543142,22.019327 90.966081,18.329754 85.259173,18.331003 79.552261,18.332249 75.000211,25.000058 75.000211,25.000058 L 25.000021,74.999942"></path></svg>
        </button>
    </div>
    <div class="mobile-menu-closer"></div>
    <div class="content-menu">
        <div class="content-infos">
            <div class="info"><b>Version:</b> 1.0.5</div>
            <div class="info"><b>Last Updated:</b> 15th Sep, 2021</div>
        </div>
        <ul>
            <li class="scroll-to-link active" data-target="content-get-started">
                <a>Pendahuluan</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-skpd">
                <a>Get SKPD</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-subkeg-by-skpd">
                <a>Get sub keg by id skpd</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-rka-by-kodesbl">
                <a>Get rka by kode sbl</a>
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
    <!-- <?php if (is_user_logged_in() == 1 ){
        echo 'API key = API key';
    }?> -->
    API key = diperlukan
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

            Sematkan <strong>API key</strong> yang telah dibuat yang dikhususkan untuk klien tertentu pada property HEADER saat melakukan permintaan.
            </p>
        </div>
        <div class="overflow-hidden content-section" id="content-get-skpd">
            <h2>GET SKPD</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl \
-X POST <?php echo get_site_url(); ?>/wp-admin/admin-ajax.php \
-F 'api_key=your_api_key' \
-F 'action=get_skpd' \
-F 'tahun_anggaran=2023' \
                </code>
            </pre>
            <p>
                Untuk menampilkan SKPD berdasarkan Tahun Anggaran, kamu memerlukan Proses otentikasi dengan melakukan POST pada alamat url :<br>
                <code class="higlighted break-word">
                    <?php echo get_site_url();?>/wp-admin/admin-ajax.php
                </code>
                <h4>QUERY PARAMETERS</h4>
                <table class="central-overflow-x">
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
        <div class="overflow-hidden content-section" id="content-errors">
            <h2>Errors</h2>
            <p>
                The Westeros API uses the following error codes:
            </p>
            <table>
                <thead>
                <tr>
                    <th>Error Code</th>
                    <th>Meaning</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>X000</td>
                    <td>
                        Some parameters are missing. This error appears when you don't pass every mandatory parameters.
                    </td>
                </tr>
                <tr>
                    <td>X001</td>
                    <td>
                        Unknown or unvalid <code class="higlighted">secret_key</code>. This error appears if you use an unknow API key or if your API key expired.
                    </td>
                </tr>
                <tr>
                    <td>X002</td>
                    <td>
                        Unvalid <code class="higlighted">secret_key</code> for this domain. This error appears if you use an  API key non specified for your domain. Developper or Universal API keys doesn't have domain checker.
                    </td>
                </tr>
                <tr>
                    <td>X003</td>
                    <td>
                        Unknown or unvalid user <code class="higlighted">token</code>. This error appears if you use an unknow user <code class="higlighted">token</code> or if the user <code class="higlighted">token</code> expired.
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

[].forEach.call(document.querySelectorAll('.scroll-to-link'), function (div) {
    div.onclick = function (e) {
        e.preventDefault();
        var target = this.dataset.target;
        document.getElementById(target).scrollIntoView({ behavior: 'smooth' });
        var elems = document.querySelectorAll(".content-menu ul li");
        [].forEach.call(elems, function (el) {
            el.classList.remove("active");
        });
        this.classList.add("active");
        return false;
    };
});

document.getElementById('button-menu-mobile').onclick = function (e) {
    e.preventDefault();
    document.querySelector('html').classList.toggle('menu-opened');
}
document.querySelector('.left-menu .mobile-menu-closer').onclick = function (e) {
    e.preventDefault();
    document.querySelector('html').classList.remove('menu-opened');
}

function debounce (func) {
    var timer;
    return function (event) {
        if (timer) clearTimeout(timer);
        timer = setTimeout(func, 100, event);
    };
}

function calculElements () {
    var totalHeight = 0;
    elements = [];
    [].forEach.call(document.querySelectorAll('.content-section'), function (div) {
        var section = {};
        section.id = div.id;
        totalHeight += div.offsetHeight;
        section.maxHeight = totalHeight - 25;
        elements.push(section);
    });
    onScroll();
}

function onScroll () {
    var scroll = window.pageYOffset;
    console.log('scroll', scroll, elements)
    for (var i = 0; i < elements.length; i++) {
        var section = elements[i];
        if (scroll <= section.maxHeight) {
            var elems = document.querySelectorAll(".content-menu ul li");
            [].forEach.call(elems, function (el) {
                el.classList.remove("active");
            });
            var activeElems = document.querySelectorAll(".content-menu ul li[data-target='" + section.id + "']");
            [].forEach.call(activeElems, function (el) {
                el.classList.add("active");
            });
            break;
        }
    }
    if (window.innerHeight + scroll + 5 >= document.body.scrollHeight) { // end of scroll, last element
        var elems = document.querySelectorAll(".content-menu ul li");
        [].forEach.call(elems, function (el) {
            el.classList.remove("active");
        });
        var activeElems = document.querySelectorAll(".content-menu ul li:last-child");
        [].forEach.call(activeElems, function (el) {
            el.classList.add("active");
        });
    }
}

calculElements();
window.onload = () => {
    calculElements();
};
window.addEventListener("resize", debounce(function (e) {
    e.preventDefault();
    calculElements();
}));
window.addEventListener('scroll', function (e) {
    e.preventDefault();
    onScroll();
});
</script>