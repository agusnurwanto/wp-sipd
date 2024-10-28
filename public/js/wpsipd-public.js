jQuery(document).ready(function () {
	var loading = ""
		+ '<div id="wrap-loading">'
		+ '<div class="lds-hourglass"></div>'
		+ '<div id="persen-loading"></div>'
		+ '<div id="pesan-loading"></div>'
		+ "</div>";
	if (jQuery("#wrap-loading").length == 0) {
		jQuery("body").prepend(loading);
	}

	jQuery(document).on("hidden.bs.modal", function () {
		if (jQuery(".modal.show").length) {
			jQuery("body").addClass("modal-open");
		}
	});

	jQuery(document).on("ajaxComplete", function (event, xhr, settings) {
		// console.log('xhr complete!', xhr);
		if (xhr.status == 400 && settings.url.indexOf("/admin-ajax.php") != -1) {
			if (
				confirm(
					"Permintaan ke server gagal! Sepertinya session login kamu sudah habis. Apakah kamu mau pindah ke halaman user untuk memastikan?"
				)
			) {
				window.location.href = ajax.site_url + "/user";
			}
		}
	});
});

function to_number(text) {
	if (typeof text == "number") {
		return text;
	}
	text = +text.replace(/\./g, "").replace(/,/g, ".");
	if (typeof text == "NaN") {
		text = 0;
	}
	return text;
}

function run_download_excel(type, tag_html = "body") {
	var current_url = window.location.href;
	var body =
		'<a id="excel" onclick="return false;" href="#" class="btn btn-success m-2"><span class="dashicons dashicons-media-spreadsheet"></span> DOWNLOAD EXCEL</a>';
	if (type == "apbd") {
		body +=
			"" +
			'<div style="padding-top: 20px;">' +
			'<label><input id="tampil-1" type="checkbox" checked="true" onclick="tampilData(this, 1)"> Tampil Rekening</label>' +
			'<label style="margin-left: 10px;"><input id="tampil-2" type="checkbox" checked="true" onclick="tampilData(this, 2)"> Tampil Keterangan</label>' +
			'<label style="margin-left: 10px;"><input id="tampil-3" type="checkbox" checked="true" onclick="tampilData(this, 3)"> Tampil Kelompok</label>' +
			"</div>";
	}
	var download_excel =
		"" + '<div id="action-sipd" class="hide-print">' + body + "</div>";
	jQuery(tag_html).prepend(download_excel);

	var style = "";

	style = jQuery(".cetak").attr("style");
	if (typeof style == "undefined") {
		style = "";
	}
	jQuery(".cetak").attr(
		"style",
		style +
		" font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; padding:0; margin:0; font-size:13px;"
	);

	jQuery(".bawah").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " border-bottom:1px solid #000;");
	});

	jQuery(".kiri").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " border-left:1px solid #000;");
	});

	jQuery(".kanan").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " border-right:1px solid #000;");
	});

	jQuery(".atas").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " border-top:1px solid #000;");
	});

	jQuery(".text_tengah").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " text-align: center;");
	});

	jQuery(".text_kiri").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " text-align: left;");
	});

	jQuery(".text_kanan").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " text-align: right;");
	});

	jQuery(".text_block").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " font-weight: bold;");
	});

	jQuery(".text_15").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " font-size: 15px;");
	});

	jQuery(".text_20").map(function (i, b) {
		style = jQuery(b).attr("style");
		if (typeof style == "undefined") {
			style = "";
		}
		jQuery(b).attr("style", style + " font-size: 20px;");
	});

	var td = document.getElementsByTagName("td");
	for (var i = 0, l = td.length; i < l; i++) {
		style = td[i].getAttribute("style");
		if (typeof style == "undefined") {
			style = "";
		}
		td[i].setAttribute("style", style + "; mso-number-format:\\@;");
	}

	jQuery("#excel").on("click", function () {
		var name = "Laporan";
		var title = jQuery("#cetak").attr("title");
		if (title) {
			name = title;
		}

		jQuery("a").removeAttr("href");

		var cek_hide_excel = jQuery("#cetak .hide-excel");
		if (cek_hide_excel.length >= 1) {
			cek_hide_excel.remove();
			setTimeout(function () {
				alert(
					"Ada beberapa fungsi yang tidak bekerja setelah melakukan donwload excel. Refresh halaman ini!"
				);
				location.reload();
			}, 5000);
		}

		tableHtmlToExcel("cetak", name);
	});
}

function tampilData(that, type) {
	jQuery(".sub_keg").map(function (i, b) {
		jQuery(b).find("td").eq(1).css({ "padding-left": "20px" });
	});
	jQuery(".kelompok").map(function (i, b) {
		jQuery(b).find("td").eq(1).css({ "padding-left": "40px" });
	});
	jQuery(".keterangan").map(function (i, b) {
		jQuery(b).find("td").eq(1).css({ "padding-left": "60px" });
	});
	jQuery(".rekening").map(function (i, b) {
		jQuery(b).find("td").eq(1).css({ "padding-left": "80px" });
	});
	jQuery(".rincian").map(function (i, b) {
		jQuery(b).find("td").eq(1).css({ "padding-left": "100px" });
	});
	var checked = jQuery(that).eq(0).is(":checked");
	jQuery(".rekening").show();
	jQuery(".keterangan").show();
	jQuery(".kelompok").show();

	var left = 0;
	if (!checked) {
		jQuery("#tampil-1").prop("checked", true);
		jQuery("#tampil-2").prop("checked", true);
		jQuery("#tampil-3").prop("checked", true);
		if (type == "1") {
			jQuery("#tampil-1").prop("checked", false);
			jQuery(".rekening").hide();
			left = "80px";
		} else if (type == "2") {
			jQuery("#tampil-1").prop("checked", false);
			jQuery("#tampil-2").prop("checked", false);
			jQuery(".rekening").hide();
			jQuery(".keterangan").hide();
			left = "60px";
		} else if (type == "3") {
			jQuery("#tampil-1").prop("checked", false);
			jQuery("#tampil-2").prop("checked", false);
			jQuery("#tampil-3").prop("checked", false);
			jQuery(".rekening").hide();
			jQuery(".keterangan").hide();
			jQuery(".kelompok").hide();
			left = "40px";
		}
		jQuery(".rincian").map(function (i, b) {
			jQuery(b).find("td").eq(1).css({ "padding-left": left });
		});
	} else {
		jQuery("#tampil-1").prop("checked", false);
		jQuery("#tampil-2").prop("checked", false);
		jQuery("#tampil-3").prop("checked", false);
		if (type == "1") {
			jQuery("#tampil-1").prop("checked", true);
			jQuery("#tampil-2").prop("checked", true);
			jQuery("#tampil-3").prop("checked", true);
			left = "100px";
		} else if (type == "2") {
			jQuery(".rekening").hide();
			jQuery("#tampil-2").prop("checked", true);
			jQuery("#tampil-3").prop("checked", true);
			left = "80px";
		} else if (type == "3") {
			jQuery(".rekening").hide();
			jQuery(".keterangan").hide();
			jQuery("#tampil-3").prop("checked", true);
			left = "60px";
		}
		jQuery(".rincian").map(function (i, b) {
			jQuery(b).find("td").eq(1).css({ "padding-left": left });
		});
	}
}

function tableHtmlToExcel(tableID, filename = "") {
	var downloadLink;
	var dataType = "application/vnd.ms-excel";
	var tableSelect = document.getElementById(tableID);
	var tableHTML = tableSelect.outerHTML
		.replace(/ /g, "%20")
		.replace(/#/g, "%23");

	filename = filename ? filename + ".xls" : "excel_data.xls";

	downloadLink = document.createElement("a");

	document.body.appendChild(downloadLink);

	if (navigator.msSaveOrOpenBlob) {
		var blob = new Blob(["\ufeff", tableHTML], {
			type: dataType,
		});
		navigator.msSaveOrOpenBlob(blob, filename);
	} else {
		downloadLink.href = "data:" + dataType + ", " + tableHTML;

		downloadLink.download = filename;

		downloadLink.click();
	}
}

function formatRupiah(angka, prefix) {
	var cek_minus = false;
	if (!angka || angka == "" || angka == 0) {
		angka = "0";
	} else if (angka < 0) {
		angka = angka * -1;
		cek_minus = true;
	}
	try {
		if (typeof angka == "number") {
			angka = Math.round(angka * 100) / 100;
			angka += "";
			angka = angka.replace(/\./g, ",").toString();
		}
		// if(typeof angka == 'number'){
		// 	angka += '';
		// 	var number_string = angka.replace(/\./g, ',').toString();
		// }else{
		// 	angka += '';
		// 	var number_string = angka.replace(/[^,\d]/g, '').toString();
		// }
		angka += "";
		number_string = angka;
	} catch (e) {
		console.log("angka", e, angka);
		var number_string = "0";
	}
	var split = number_string.split(","),
		sisa = split[0].length % 3,
		rupiah = split[0].substr(0, sisa),
		ribuan = split[0].substr(sisa).match(/\d{3}/gi);

	// tambahkan titik jika yang di input sudah menjadi angka ribuan
	if (ribuan) {
		separator = sisa ? "." : "";
		rupiah += separator + ribuan.join(".");
	}

	rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
	if (cek_minus) {
		return "-" + (prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "");
	} else {
		return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
	}
}

function changeUrl(option) {
	var key = option.key;
	var value = option.value;
	var _url = option.url;
	var url_object = new URL(_url);
	var value_asli = url_object.searchParams.get(key);
	var _and = "&";
	if (_url.indexOf("?") == -1) {
		_url += "?";
		_and = "";
	}

	if (_url.indexOf(key) != -1) {
		_url = _url.replace(
			_and + key + "=" + value_asli,
			_and + key + "=" + value
		);
	} else {
		_url += _and + key + "=" + value;
	}
	return _url;
}

function onlyNumber(e) {
	var _string = String.fromCharCode(e.which);
	// console.log('e.which', e.which, _string);
	if (isNaN(String.fromCharCode(e.which)) && _string != ".") {
		e.preventDefault();
	}
}

function penjadwalanHitungMundur(dataHitungMundur = {}) {
	nama =
		dataHitungMundur["namaJadwal"] == ""
			? "Penjadwalan"
			: dataHitungMundur["namaJadwal"];
	mulaiJadwal =
		dataHitungMundur["mulaiJadwal"] == ""
			? "2022-08-12 16:00:00"
			: dataHitungMundur["mulaiJadwal"];
	selesaiJadwal =
		dataHitungMundur["selesaiJadwal"] == ""
			? "2022-09-12 16:00:00"
			: dataHitungMundur["selesaiJadwal"];
	thisTimeZone =
		dataHitungMundur["thisTimeZone"] == ""
			? "Asia/Jakarta"
			: dataHitungMundur["thisTimeZone"];

	cekTimeZone = thisTimeZone.includes("Asia/");

	if (cekTimeZone == false) {
		console.log("Pengaturan timezone salah");
		console.log(
			"Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  'Jakarta','Makasar','Jayapura'"
		);
	}

	var jadwal =
		'<div id="penjadwalanHitungMundur">' +
		'<label id="titles"><span class="dashicons dashicons-clock"></span>&nbsp;' +
		nama +
		"</label>" +
		'<div id="days" style="margin-left:10px">0 <span>Hari</span></div>' +
		'<div id="hours">00 <span>Jam</span></div>' +
		'<div id="minutes">00 <span>Menit</span></div>' +
		'<div id="seconds">00 <span>Detik</span></div>' +
		"</div>";

	jQuery("body").prepend(jadwal);

	function makeTimer() {
		var endTime = new Date(selesaiJadwal);
		endTime = Date.parse(endTime) / 1000;

		var now = new Date();

		now = new Date(now.toLocaleString("en-US", { timeZone: thisTimeZone }));

		now = Date.parse(now) / 1000;

		var timeLeft = endTime - now;

		var days = Math.floor(timeLeft / 86400);
		var hours = Math.floor((timeLeft - days * 86400) / 3600);
		var minutes = Math.floor((timeLeft - days * 86400 - hours * 3600) / 60);
		var seconds = Math.floor(
			timeLeft - days * 86400 - hours * 3600 - minutes * 60
		);

		if (hours < "10") {
			hours = "0" + hours;
		}
		if (minutes < "10") {
			minutes = "0" + minutes;
		}
		if (seconds < "10") {
			seconds = "0" + seconds;
		}

		jQuery("#days").html(days + "<span>Hari</span>");
		jQuery("#hours").html(hours + "<span>Jam</span>");
		jQuery("#minutes").html(minutes + "<span>Menit</span>");
		jQuery("#seconds").html(seconds + "<span>Detik</span>");

		if (timeLeft < 0) {
			clearInterval(wpsipdTimer);
			jQuery("#days").html("0 <span>Hari</span>");
			jQuery("#hours").html("00 <span>Jam</span>");
			jQuery("#minutes").html("00 <span>Menit</span>");
			jQuery("#seconds").html("00 <span>Detik</span>");
		}
	}

	var mulaiJadwal = new Date(mulaiJadwal);
	mulaiJadwal = Date.parse(mulaiJadwal);

	var now = new Date();
	now = new Date(now.toLocaleString("en-US", { timeZone: thisTimeZone }));
	now = Date.parse(now);
	if (now > mulaiJadwal) {
		var wpsipdTimer = setInterval(function () {
			makeTimer();
		}, 1000);
	} else {
		jQuery("#days").html("0 <span>Hari</span>");
		jQuery("#hours").html("00 <span>Jam</span>");
		jQuery("#minutes").html("00 <span>Menit</span>");
		jQuery("#seconds").html("00 <span>Detik</span>");
	}
}

function simpan_alamat(id_skpd, api_key, ajaxurl) {
	jQuery("#wrap-loading").show();
	jQuery.ajax({
		url: ajaxurl,
		type: "post",
		data: {
			action: "simpan_meta_skpd",
			api_key: api_key,
			id_skpd: id_skpd,
			alamat: jQuery("#alamat_skpd_" + id_skpd).val(),
		},
		dataType: "json",
		success: function (data) {
			jQuery("#wrap-loading").hide();
			return alert(data.message);
		},
		error: function (e) {
			console.log(e);
			return alert(data.message);
		},
	});
}

function validateForm(fields) {
	const formData = {};

	for (const [name, message] of Object.entries(fields)) {
		const $field = jQuery(`[name="${name}"]`);

		if ($field.is(':radio')) {
			const checkedValue = jQuery(`[name="${name}"]:checked`).val();
			if (!checkedValue) {
				return { error: message };
			}
			formData[name] = checkedValue;
		} else if ($field.is(':checkbox')) {
			const isChecked = $field.is(':checked');
			if (!isChecked) {
				return { error: message };
			}
			formData[name] = isChecked;
		} else if ($field.is('select') || $field.is('textarea') || $field.is(':input')) {
			const value = $field.val().trim();
			if (value === '') {
				return { error: message };
			}
			formData[name] = value;
		}
	}

	return { error: null, data: formData };
}

function clearAllFields() {
	jQuery('form').find('input[type="text"]:not(:disabled), input[type="number"]:not(:disabled), input[type="hidden"]:not(:disabled), textarea:not(:disabled)').val('');
	jQuery('form').find('input[type="radio"]:not(:disabled), input[type="checkbox"]:not(:disabled)').prop('checked', false);
	jQuery('form').find('select:not(:disabled), .input_rekening select').prop('selectedIndex', 0).trigger('change');
}

function formatAngka(angka) {
	return angka.toLocaleString('id-ID');
}

function terbilang(nilai) {
	nilai = Math.floor(Math.abs(nilai));

	let huruf = [
		'',
		'Satu',
		'Dua',
		'Tiga',
		'Empat',
		'Lima',
		'Enam',
		'Tujuh',
		'Delapan',
		'Sembilan',
		'Sepuluh',
		'Sebelas',
	];

	let bagi = 0;
	let penyimpanan = '';

	if (nilai < 12) {
		penyimpanan = ' ' + huruf[nilai];
	} else if (nilai < 20) {
		penyimpanan = terbilang(Math.floor(nilai - 10)) + ' Belas';
	} else if (nilai < 100) {
		bagi = Math.floor(nilai / 10);
		penyimpanan = terbilang(bagi) + ' Puluh' + terbilang(nilai % 10);
	} else if (nilai < 200) {
		penyimpanan = ' Seratus' + terbilang(nilai - 100);
	} else if (nilai < 1000) {
		bagi = Math.floor(nilai / 100);
		penyimpanan = terbilang(bagi) + ' Ratus' + terbilang(nilai % 100);
	} else if (nilai < 2000) {
		penyimpanan = ' Seribu' + terbilang(nilai - 1000);
	} else if (nilai < 1000000) {
		bagi = Math.floor(nilai / 1000);
		penyimpanan = terbilang(bagi) + ' Ribu' + terbilang(nilai % 1000);
	} else if (nilai < 1000000000) {
		bagi = Math.floor(nilai / 1000000);
		penyimpanan = terbilang(bagi) + ' Juta' + terbilang(nilai % 1000000);
	} else if (nilai < 1000000000000) {
		bagi = Math.floor(nilai / 1000000000);
		penyimpanan = terbilang(bagi) + ' Miliar' + terbilang(nilai % 1000000000);
	} else if (nilai < 1000000000000000) {
		bagi = Math.floor(nilai / 1000000000000);
		penyimpanan = terbilang(nilai / 1000000000000) + ' Triliun' + terbilang(nilai % 1000000000000);
	}

	return penyimpanan;
}
