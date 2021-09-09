<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

if(empty($input['id_skpd']))
{
	die('<h1>SKPD tidak ditemukan!</h1>');
}

$api_key = get_option('_crb_api_key_extension' );

function button_edit_monev($class=false)
{
	$ret = ' <span style="display: none;" data-id="'.$class.'" class="edit-monev"><i class="dashicons dashicons-edit"></i></span>';
	return $ret;
}

$rumus_indikator_db = $wpdb->get_results("SELECT * from data_rumus_indikator where active=1 and tahun_anggaran=".$input['tahun_anggaran'], ARRAY_A);
$rumus_indikator = '';
foreach ($rumus_indikator_db as $k => $v)
{
	$rumus_indikator .= '<option value="'.$v['id'].'">'.$v['rumus'].'</option>';
}

$sql = $wpdb->prepare("
	select 
		* 
	from data_unit 
	where tahun_anggaran=%d
		and id_skpd =".$input['id_skpd']."
		and active=1
	order by id_skpd ASC
", $input['tahun_anggaran']);
$unit = $wpdb->get_results($sql, ARRAY_A);
$pengaturan = $wpdb->get_results($wpdb->prepare("
	select 
		* 
	from data_pengaturan_sipd 
	where tahun_anggaran=%d
", $input['tahun_anggaran']), ARRAY_A);

$awal_rpjmd = 2018;
$akhir_rpjmd = 2023;
if(!empty($pengaturan))
{
	$awal_rpjmd = $pengaturan[0]['awal_rpjmd'];
	$akhir_rpjmd = $pengaturan[0]['akhir_rpjmd'];
}
$urut = $input['tahun_anggaran']-$awal_rpjmd;
$nama_pemda = $pengaturan[0]['daerah'];

$current_user = wp_get_current_user();
$bulan = date('m');
$body_monev = '';

$data_all = array(
	'data' => array()
);

$bulan = date('m');
$subkeg = $wpdb->get_results($wpdb->prepare("
		select 
			kode_giat,
			 nama_giat,
			 kode_program,
			 nama_program 
		from data_sub_keg_bl
		where tahun_anggaran=%d
			and active=1
			and id_sub_skpd=%d group by kode_giat
		order by kode_sub_giat ASC
	", $input['tahun_anggaran'], $unit[0]['id_skpd']), ARRAY_A);

foreach ($subkeg as $kk => $sub)
{
	
	$kegs = $wpdb->get_results($wpdb->prepare("
			select 
				* 
			from data_renstra_kegiatan 
			where 
				kode_giat=%s and 
				id_unit=%d and 
				tahun_anggaran=%d", 
			$sub['kode_giat'], $unit[0]['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
	
	if(!empty($kegs))
	{

		foreach ($kegs as $keykeg => $keg)
		{
				if(empty($data_all['data'][$keg['kode_tujuan']]))
				{
					$tujuan_teks = explode("||", $keg['tujuan_teks']);
					$data_all['data'][$keg['kode_tujuan']] = array(
						'nama' => $tujuan_teks[0],
						'indikator' => array(),
						'data' => array(),
					);

					$indikators = $wpdb->get_results($wpdb->prepare("
							select 
								* 
							from data_renstra_tujuan 
							where 
								id_unik=%s and 
								active=1 and 
								tahun_anggaran=%d", 
							$keg['kode_tujuan'], $input['tahun_anggaran']), ARRAY_A);

					if(!empty($indikators))
					{
						foreach ($indikators as $key => $indikator) 
						{
							$data_all['data'][$keg['kode_tujuan']]['indikator'][] = array(
								'id_unik_indikator' => $indikator['id_unik_indikator'],
								'indikator_teks' => !empty($indikator['indikator_teks']) ? $indikator['indikator_teks'] : 'Kosong',
								'satuan' => $indikator['satuan'],
								'target_1' => $indikator['target_1'],
								'target_2' => $indikator['target_2'],
								'target_3' => $indikator['target_3'],
								'target_4' => $indikator['target_4'],
								'target_5' => $indikator['target_5'],
								'target_awal' => $indikator['target_awal'],
								'target_akhir' => $indikator['target_akhir'],
							);
						}
					}
				}

				if(empty($data_all['data'][$keg['kode_tujuan']]['data'][$keg['kode_sasaran']]))
				{
					$sasaran_teks = explode("||", $keg['sasaran_teks']);
					$data_all['data'][$keg['kode_tujuan']]['data'][$keg['kode_sasaran']] = array(
						'nama' => $sasaran_teks[0],
						'indikator' => array(),
						'data' => array(),
					);

					$indikators = $wpdb->get_results($wpdb->prepare("
						select 
							* 
						from data_renstra_sasaran 
						where 
							id_unik=%s and 
							active=1 and 
							tahun_anggaran=%d", 
					$keg['kode_sasaran'],$input['tahun_anggaran']), ARRAY_A);

					if(!empty($indikators))
					{
						foreach ($indikators as $key => $indikator) 
						{
							$data_all['data'][$keg['kode_tujuan']]['data'][$keg['kode_sasaran']]['indikator'][] = array(
								'id_unik_indikator' => $indikator['id_unik_indikator'],
								'indikator_teks' => $indikator['indikator_teks'],
								'satuan' => $indikator['satuan'],
								'target_1' => $target_1['target_1'],
								'target_2' => $target_2['target_2'],
								'target_3' => $target_3['target_3'],
								'target_4' => $target_4['target_4'],
								'target_5' => $target_5['target_5'],
								'target_awal' => $target_awal['target_awal'],
								'target_akhir' => $target_akhir['target_akhir'],
							);
						}
					}
				}

				if(empty($data_all['data'][$keg['kode_tujuan']]['data'][$keg['kode_sasaran']]['data'][$keg['kode_program']]))
				{
					$program_teks = explode("||", $keg['nama_program']);
					$data_all['data'][$keg['kode_tujuan']]['data'][$keg['kode_sasaran']]['data'][$keg['kode_program']] = array(
						'nama' => $program_teks[0],
						'indikator' => array(),
						'data' => array(),
					);

					$indikators = $wpdb->get_results($wpdb->prepare("
						select 
							* 
						from data_renstra_program 
						where 
							id_unit=%d and 
							kode_program=%s and 
							active=1 and 
							tahun_anggaran=%d",
							$input['id_skpd'], $keg['kode_program'], $input['tahun_anggaran']), ARRAY_A);

					if(!empty($indikators))
					{
						foreach ($indikators as $key => $indikator) 
						{

							$data_all['data'][$keg['kode_tujuan']]['data'][$keg['kode_sasaran']]['data'][$keg['kode_program']]['indikator'][] = array(
								'id_unik_indikator' => $indikator['id_unik_indikator'],
								'indikator_teks' => $indikator['indikator'],
								'satuan' => $indikator['satuan'],
								'target_1' => $indikator['target_1'],
								'target_2' => $indikator['target_2'],
								'target_3' => $indikator['target_3'],
								'target_4' => $indikator['target_4'],
								'target_5' => $indikator['target_5'],
								'target_awal' => $indikator['target_awal'],
								'target_akhir' => $indikator['target_akhir'],
							);
						}
					}
				}

				if(empty($data_all['data'][$keg['kode_tujuan']]['data'][$keg['kode_sasaran']]['data'][$keg['kode_program']]['data'][$keg['kode_giat']]))
				{
					$kegiatan_teks = explode("||", $keg['nama_giat']);
					$data_all['data'][$keg['kode_tujuan']]['data'][$keg['kode_sasaran']]['data'][$keg['kode_program']]['data'][$keg['kode_giat']] = array(
						'nama' => $kegiatan_teks[0],
						'indikator' => array()
					);

					$indikators = $wpdb->get_results($wpdb->prepare("
						select 
							* 
						from data_renstra_kegiatan 
						where 
							id_unit=%d and 
							kode_giat=%s and 
							active=1 and 
							tahun_anggaran=%d", 
							$input['id_skpd'], $keg['kode_giat'], $input['tahun_anggaran']), ARRAY_A);

					if(!empty($indikators))
					{
						foreach ($indikators as $key => $indikator) 
						{

							$data_all['data'][$keg['kode_tujuan']]['data'][$keg['kode_sasaran']]['data'][$keg['kode_program']]['data'][$keg['kode_giat']]['indikator'][] = array(
								'id_unik_indikator' => $indikator['id_unik_indikator'],
								'indikator_teks' => $indikator['indikator'],
								'satuan' => $indikator['satuan'],
								'target_1' => $indikator['target_1'],
								'target_2' => $indikator['target_2'],
								'target_3' => $indikator['target_3'],
								'target_4' => $indikator['target_4'],
								'target_5' => $indikator['target_5'],
								'target_awal' => $indikator['target_awal'],
								'target_akhir' => $indikator['target_akhir'],
							);
						}
					}
				}
		}
	}
	else
	{
		
		if(empty($data_all['data']['-']))
		{
			$data_all['data']['-'] = array(
				'nama' => 'Tujuan Renstra tidak ditemukan',
				'indikator' => array(),
				'data' => array(),
			);
		}

		if(empty($data_all['data']['-']['data']['-']))
		{
			$data_all['data']['-']['data']['-'] = array(
				'nama' => 'Sasaran Renstra tidak ditemukan',
				'indikator' => array(),
				'data' => array(),
			);
		}

		if(empty($data_all['data']['-']['data']['-']['data'][$sub['nama_program']]))
		{
			$data_all['data']['-']['data']['-']['data'][$sub['nama_program']] = array(
				'nama' => $sub['nama_program'],
				'indikator' => array(),
				'data' => array(),
			);
		}

		if(empty($data_all['data']['-']['data']['-']['data'][$sub['nama_program']]['data'][$sub['nama_giat']]))
		{

			$data_all['data']['-']['data']['-']['data'][$sub['nama_program']]['data'][$sub['nama_giat']] = array(
				'nama' => $sub['nama_giat'],
				'indikator' => array()
			);
		}
	}	
}

	foreach ($data_all['data'] as $key => $tujuan) 
	{

			$body_monev .= '
				<tr class="tujuan" data-kode="">
		            <td class="kiri kanan bawah text_blok"></td>
		            <td class="text_kiri kanan bawah text_blok">'.$tujuan['nama'].'</td>
		            <td class="text_tengah kanan bawah text_blok"></td>
		            <td class="kanan bawah text_blok"></td>
		            <td class="kanan bawah text_blok nama"></td>
		            <td class="kanan bawah text_blok indikator rumus_indikator"></td>
		            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
		            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
		            <td class="text_kanan kanan bawah text_blok total_renstra"></td>
		            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
		            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
		            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
		            <td class="text_tengah kanan bawah text_blok total_renja target_indikator"></td>
		            <td class="text_tengah kanan bawah text_blok total_renja satuan_indikator"></td>
		            <td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu=""></td>
		            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
		            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
		            <td class="text_kanan kanan bawah text_blok triwulan_1"></td>
		            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
		            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
		            <td class="text_kanan kanan bawah text_blok triwulan_2"></td>
		            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
		            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
		            <td class="text_kanan kanan bawah text_blok triwulan_3"></td>
		            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
		            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
		            <td class="text_kanan kanan bawah text_blok triwulan_4"></td>
		            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
		            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
		            <td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu=""></td>
		            <td class="text_tengah kanan bawah text_blok capaian_renja"></td>
		            <td class="text_kanan kanan bawah text_blok capaian_renja"></td>
		            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
		            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
		            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
		            <td class="text_tengah kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
		            <td class="text_kanan kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
	        		<td class="kanan bawah text_blok"></td>
		        </tr>';

			foreach ($tujuan['indikator'] as $key => $ind_tujuan) 
			{
				$body_monev .= '
					<tr class="program" data-kode="">
			            <td class="kiri kanan bawah text_blok"></td>
			            <td class="text_kiri kanan bawah text_blok"></td>
			            <td class="text_tengah kanan bawah text_blok"></td>
			            <td class="kanan bawah text_blok"></td>
			            <td class="kanan bawah text_blok nama"></td>
			            <td class="kanan bawah text_blok indikator rumus_indikator">'.$ind_tujuan['indikator_teks'].'</td>
			            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
			            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
			            <td class="text_kanan kanan bawah text_blok total_renstra"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_tengah kanan bawah text_blok total_renja target_indikator"></td>
			            <td class="text_tengah kanan bawah text_blok total_renja satuan_indikator"></td>
			            <td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu=""></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_1"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_2"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_3"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_4"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu=""></td>
			            <td class="text_tengah kanan bawah text_blok capaian_renja"></td>
			            <td class="text_kanan kanan bawah text_blok capaian_renja"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_tengah kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
			            <td class="text_kanan kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
		        		<td class="kanan bawah text_blok"></td>
			        </tr>';
			}		        

		foreach ($tujuan['data'] as $key => $sasaran) 
		{

				$body_monev .= '
					<tr class="sasaran" data-kode="">
			            <td class="kiri kanan bawah text_blok"></td>
			            <td class="text_kiri kanan bawah text_blok"></td>
			            <td class="text_kiri kanan bawah text_blok">'.$sasaran['nama'].'</td>
			            <td class="kanan bawah text_blok"></td>
			            <td class="kanan bawah text_blok nama"></td>
			            <td class="kanan bawah text_blok indikator rumus_indikator"></td>
			            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
			            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
			            <td class="text_kanan kanan bawah text_blok total_renstra"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_tengah kanan bawah text_blok total_renja target_indikator"></td>
			            <td class="text_tengah kanan bawah text_blok total_renja satuan_indikator"></td>
			            <td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu=""></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_1"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_2"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_3"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_4"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu=""></td>
			            <td class="text_tengah kanan bawah text_blok capaian_renja"></td>
			            <td class="text_kanan kanan bawah text_blok capaian_renja"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_tengah kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
			            <td class="text_kanan kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
		        		<td class="kanan bawah text_blok"></td>
			        </tr>';

			foreach ($sasaran['indikator'] as $key => $ind_sasaran) 
			{
				$body_monev .= '
					<tr class="ind_sasaran" data-kode="">
			            <td class="kiri kanan bawah text_blok"></td>
			            <td class="text_kiri kanan bawah text_blok"></td>
			            <td class="text_tengah kanan bawah text_blok"></td>
			            <td class="kanan bawah text_blok"></td>
			            <td class="kanan bawah text_blok nama"></td>
			            <td class="kanan bawah text_blok indikator rumus_indikator">'.$ind_sasaran['indikator_teks'].'</td>
			            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
			            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
			            <td class="text_kanan kanan bawah text_blok total_renstra"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_tengah kanan bawah text_blok total_renja target_indikator"></td>
			            <td class="text_tengah kanan bawah text_blok total_renja satuan_indikator"></td>
			            <td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu=""></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_1"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_2"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_3"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_4"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu=""></td>
			            <td class="text_tengah kanan bawah text_blok capaian_renja"></td>
			            <td class="text_kanan kanan bawah text_blok capaian_renja"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_tengah kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
			            <td class="text_kanan kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
		        		<td class="kanan bawah text_blok"></td>
			        </tr>';
			}

			foreach ($sasaran['data'] as $key => $program) 
			{
				$body_monev .= '
					<tr class="program" data-kode="">
			            <td class="kiri kanan bawah text_blok"></td>
			            <td class="text_kiri kanan bawah text_blok"></td>
			            <td class="text_kiri kanan bawah text_blok"></td>
			            <td class="kanan bawah text_blok"></td>
			            <td class="kanan bawah text_blok nama">'.$program['nama'].'</td>
			            <td class="kanan bawah text_blok indikator rumus_indikator"></td>
			            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
			            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
			            <td class="text_kanan kanan bawah text_blok total_renstra"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
			            <td class="text_tengah kanan bawah text_blok total_renja target_indikator"></td>
			            <td class="text_tengah kanan bawah text_blok total_renja satuan_indikator"></td>
			            <td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu=""></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_1"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_2"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_3"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
			            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
			            <td class="text_kanan kanan bawah text_blok triwulan_4"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu=""></td>
			            <td class="text_tengah kanan bawah text_blok capaian_renja"></td>
			            <td class="text_kanan kanan bawah text_blok capaian_renja"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
			            <td class="text_tengah kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
			            <td class="text_kanan kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
		        		<td class="kanan bawah text_blok"></td>
			        </tr>';

			    foreach ($program['indikator'] as $key => $ind_program) 
			    {
					$body_monev .= '
						<tr class="ind_program">
				            <td class="kiri kanan bawah text_blok"></td>
				            <td class="text_kiri kanan bawah text_blok"></td>
				            <td class="text_tengah kanan bawah text_blok"></td>
				            <td class="kanan bawah text_blok"></td>
				            <td class="kanan bawah text_blok nama"></td>
				            <td class="kanan bawah text_blok indikator rumus_indikator">'.$ind_program['indikator_teks'].'</td>
				            <td class="text_tengah kanan bawah text_blok total_renstra">'.$ind_program['target_5'].'</td>
				            <td class="text_tengah kanan bawah text_blok total_renstra">'.$ind_program['satuan'].'</td>
				            <td class="text_kanan kanan bawah text_blok total_renstra"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
				            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
				            <td class="text_tengah kanan bawah text_blok total_renja target_indikator">'.$ind_program['target_'.$urut].'</td>
				            <td class="text_tengah kanan bawah text_blok total_renja satuan_indikator">'.$ind_program['satuan'].'</td>
				            <td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu=""></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
				            <td class="text_kanan kanan bawah text_blok triwulan_1"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
				            <td class="text_kanan kanan bawah text_blok triwulan_2"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
				            <td class="text_kanan kanan bawah text_blok triwulan_3"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
				            <td class="text_kanan kanan bawah text_blok triwulan_4"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
				            <td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu=""></td>
				            <td class="text_tengah kanan bawah text_blok capaian_renja"></td>
				            <td class="text_kanan kanan bawah text_blok capaian_renja"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
				            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
				            <td class="text_tengah kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
				            <td class="text_kanan kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
			        		<td class="kanan bawah text_blok"></td>
				        </tr>';
				}

				foreach ($program['data'] as $key => $kegiatan) 
				{
					$body_monev .= '
						<tr class="kegiatan" data-kode="">
				            <td class="kiri kanan bawah text_blok"></td>
				            <td class="text_kiri kanan bawah text_blok"></td>
				            <td class="text_kiri kanan bawah text_blok"></td>
				            <td class="kanan bawah text_blok"></td>
				            <td class="kanan bawah text_blok nama">'.$kegiatan['nama'].'</td>
				            <td class="kanan bawah text_blok indikator rumus_indikator"></td>
				            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
				            <td class="text_tengah kanan bawah text_blok total_renstra"></td>
				            <td class="text_kanan kanan bawah text_blok total_renstra"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
				            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
				            <td class="text_tengah kanan bawah text_blok total_renja target_indikator"></td>
				            <td class="text_tengah kanan bawah text_blok total_renja satuan_indikator"></td>
				            <td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu=""></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
				            <td class="text_kanan kanan bawah text_blok triwulan_1"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
				            <td class="text_kanan kanan bawah text_blok triwulan_2"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
				            <td class="text_kanan kanan bawah text_blok triwulan_3"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
				            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
				            <td class="text_kanan kanan bawah text_blok triwulan_4"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
				            <td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu=""></td>
				            <td class="text_tengah kanan bawah text_blok capaian_renja"></td>
				            <td class="text_kanan kanan bawah text_blok capaian_renja"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
				            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
				            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
				            <td class="text_tengah kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
				            <td class="text_kanan kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
			        		<td class="kanan bawah text_blok"></td>
				        </tr>';

				        foreach ($kegiatan['indikator'] as $key => $ind_kegiatan) 
					    {
							$body_monev .= '
								<tr class="ind_kegiatan">
						            <td class="kiri kanan bawah text_blok"></td>
						            <td class="text_kiri kanan bawah text_blok"></td>
						            <td class="text_tengah kanan bawah text_blok"></td>
						            <td class="kanan bawah text_blok"></td>
						            <td class="kanan bawah text_blok nama"></td>
						            <td class="kanan bawah text_blok indikator rumus_indikator">'.$ind_kegiatan['indikator_teks'].'</td>
						            <td class="text_tengah kanan bawah text_blok total_renstra">'.$ind_kegiatan['target_5'].'</td>
						            <td class="text_tengah kanan bawah text_blok total_renstra">'.$ind_kegiatan['satuan'].'</td>
						            <td class="text_kanan kanan bawah text_blok total_renstra"></td>
						            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
						            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
						            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu"></td>
						            <td class="text_tengah kanan bawah text_blok total_renja target_indikator">'.$ind_kegiatan['target_'.$urut].'</td>
						            <td class="text_tengah kanan bawah text_blok total_renja satuan_indikator">'.$ind_kegiatan['satuan'].'</td>
						            <td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu=""></td>
						            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
						            <td class="text_tengah kanan bawah text_blok triwulan_1"></td>
						            <td class="text_kanan kanan bawah text_blok triwulan_1"></td>
						            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
						            <td class="text_tengah kanan bawah text_blok triwulan_2"></td>
						            <td class="text_kanan kanan bawah text_blok triwulan_2"></td>
						            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
						            <td class="text_tengah kanan bawah text_blok triwulan_3"></td>
						            <td class="text_kanan kanan bawah text_blok triwulan_3"></td>
						            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
						            <td class="text_tengah kanan bawah text_blok triwulan_4"></td>
						            <td class="text_kanan kanan bawah text_blok triwulan_4"></td>
						            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
						            <td class="text_tengah kanan bawah text_blok realisasi_renja"></td>
						            <td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu=""></td>
						            <td class="text_tengah kanan bawah text_blok capaian_renja"></td>
						            <td class="text_kanan kanan bawah text_blok capaian_renja"></td>
						            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
						            <td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
						            <td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan"></td>
						            <td class="text_tengah kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
						            <td class="text_kanan kanan bawah text_blok capaian_renstra_tahun_berjalan"></td>
					        		<td class="kanan bawah text_blok"></td>
						        </tr>';
						}
				}
			}
		}
	}

?>

<style type="text/css">
	table th, #mod-monev th {
		vertical-align: middle;
	}
	body {
		overflow: auto;
	}
	td[contenteditable="true"] {
	    background: #ff00002e;
	}
</style>
<input type="hidden" value="<?php echo get_option('_crb_api_key_extension' ); ?>" id="api_key">
<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
<input type="hidden" value="<?php echo $unit[0]['id_skpd']; ?>" id="id_skpd">
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi Rencana Strategis <br><?php echo $unit[0]['kode_skpd'].'&nbsp;'.$unit[0]['nama_skpd'].'<br>Tahun '.$input['tahun_anggaran'].' '.$nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th rowspan="5" style="width: 60px;" class='atas kiri kanan bawah text_tengah text_blok'>No</th>
				<th rowspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Tujuan</th>
				<th rowspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Sasaran</th>
				<th rowspan="2" style="width: 100px;" class='atas kanan bawah text_tengah text_blok'>Kode</th>
				<th rowspan="2" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Program, Kegiatan</th>
				<th rowspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Indikator Kinerja Tujuan, Sasaran, Program(outcome) dan Kegiatan (output)</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Target Renstra SKPD pada Tahun <?php echo $awal_rpjmd; ?> s/d <?php echo $akhir_rpjmd; ?> (periode Renstra SKPD)</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Realisasi Capaian Kinerja Renstra SKPD sampai dengan Renja SKPD Tahun Lalu</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Target kinerja dan anggaran Renja SKPD Tahun Berjalan Tahun <?php echo $input['tahun_anggaran']; ?> yang dievaluasi</th>
				<th colspan="12" style="width: 1200px;" class='atas kanan bawah text_tengah text_blok'>Realisasi Kinerja Pada Triwulan</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Realisasi Capaian Kinerja dan Anggaran Renja SKPD yang dievaluasi</th>
				<th rowspan="2" colspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Tingkat Capaian Kinerja dan Realisasi Anggaran Renja yang dievaluasi (%)</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Realisasi Kinerja dan Anggaran Renstra SKPD s/d Tahun <?php echo $input['tahun_anggaran']; ?> (Akhir Tahun Pelaksanaan Renja SKPD)</th>
				<th rowspan="2" colspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Tingkat Capaian Kinerja dan Realisasi Anggaran Renstra SKPD s/d tahun <?php echo $input['tahun_anggaran']; ?> (%)</th>
				<th rowspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Unit OPD Penanggung Jawab</th>
			</tr>
			<tr>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>I</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>II</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>III</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>VI</th>
			</tr>
			<tr>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>0</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>1</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>2</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>3</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>4</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>5</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>6</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>7</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>8</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>9</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>10</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>11</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>12 = 8+9+10+11</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>13 = 12/7x100</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>14 = 6 + 12</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>15 = 14/5 x100</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>16</th>
			</tr>
			<tr>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
			</tr>
			<tr>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body_monev; ?>
		</tbody>
	</table>
</div>

<div class="modal fade" id="mod-monev" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Edit MONEV Indikator Per Bulan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<form>
                  	<div class="form-group">
                  		<table class="table table-bordered">
                  			<tbody>
                  				<tr>
                  					<th style="width: 200px;">Tujuan / Sasaran</th>
                  					<td id="monev-nama"></td>
                  				</tr>
                  				<tr>
                  					<td colspan="2">
                  						<table>
                  							<thead>
                  								<tr>
                  									<th class="text_tengah">Indikator Kinerja Tujuan, Sasaran</th>
                  									<th class="text_tengah" style="width: 120px;">Target</th>
                  									<th class="text_tengah" style="width: 120px;">Total Target Realisasi</th>
                  									<th class="text_tengah" style="width: 120px;">Satuan</th>
                  								</tr>
                  							</thead>
                  							<tbody id="monev-indikator">
                  							</tbody>
                  						</table>
                  					</td>
                  				</tr>
                  				<tr>
                  					<th>Pilih Rumus Indikator</th>
                  					<td>
                  						<select style="width: 100%;" id="tipe_indikator">
                  							<?php echo $rumus_indikator; ?>
                  						</select>
                  					</td>
                  				</tr>
                  				<tr>
                  					<td colspan="2">
                  						<table>
                  							<thead>
                  								<tr>
		              								<th class="text_tengah">Bulan</th>
		              								<th class="text_tengah" style="width: 150px;">Capaian (%)</th>
		              								<th class="text_tengah" style="width: 150px;">Realisasi Target</th>
		              							</tr>
                  							</thead>
                  							<tbody>
                  								<tr>
                  									<td>Januari</td>
                  									<td class="text_tengah">-</td>
                  									<td class="text_tengah" contenteditable="true">-</td>
                  								</tr>
                  								<tr>
                  									<td>Februari</td>
                  									<td class="text_tengah">-</td>
                  									<td class="text_tengah" contenteditable="true">-</td>
                  								</tr>
                  								<tr>
                  									<td>Maret</td>
                  									<td class="text_tengah">-</td>
                  									<td class="text_tengah" contenteditable="true">-</td>
                  								</tr>
                  								<tr>
                  									<td>April</td>
                  									<td class="text_tengah">-</td>
                  									<td class="text_tengah" contenteditable="true">-</td>
                  								</tr>
                  							</tbody>
                  						</table>
                  					</td>
                  				</tr>
                  			</tbody>
                  		</table>
                  	</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="set-monev">Simpan</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	run_download_excel();
	var aksi = ''
		+'<h3 style="margin-top: 20px;">SETTING</h3>'
		+'<label><input type="checkbox" onclick="edit_monev_indikator(this);"> Edit Monev indikator</label>';
	jQuery('#action-sipd').append(aksi);
	function edit_monev_indikator(that){
		if(jQuery(that).is(':checked')){
			jQuery('.edit-monev').show();
		}else{
			jQuery('.edit-monev').hide();
		}
	}
	jQuery('.edit-monev').on('click', function(){
		jQuery('#wrap-loading').show();
		jQuery('#mod-monev').modal('show');
		jQuery('#wrap-loading').hide();
	});
</script>