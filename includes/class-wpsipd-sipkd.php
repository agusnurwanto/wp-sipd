<?php
class Wpsipd_Sipkd
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	public function __construct($plugin_name, $version){

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$sipkd = get_option( '_crb_singkron_sipkd' );
		if($sipkd == 1){
			$this->status_koneksi_sipkd = true;
		}else{
			$this->status_koneksi_sipkd = false;
		}
	}

	public function CurlSipkd($options, $debug=false, $debug_req=false){
		if(
			false == $this->status_koneksi_sipkd
			|| (
				!empty($_GET) 
				&& !empty($_GET['no_sipkd'])
			)
		){
			return;
		}
        $query = $options['query'];
        $curl = curl_init();
        $req = array(
            'api_key' => get_option( '_crb_apikey_sipd' ),
            'query' => $query,
            'db' => get_option('_crb_db_sipd')
        );
        set_time_limit(0);
        $url = get_option( '_crb_url_api_sipkd' );
    	if($debug_req){
        	print_r($req); die($url);
    	}
        $req = http_build_query($req);
        $timeout = (int) get_option('_crb_timeout_sipkd');
        if(empty($timeout)){
        	$timeout = 10;
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $req,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_NOSIGNAL => 1,
            CURLOPT_CONNECTTIMEOUT => 100,
            CURLOPT_TIMEOUT => $timeout
        ));

        $response = curl_exec($curl);
        // die($response);
        $err = curl_error($curl);

        curl_close($curl);

        $debug_option = false;
        if ($err) {
        	$this->status_koneksi_sipkd = false;
        	$msg = "cURL Error #:".$err." (".$url.")";
        	if($debug_option == 1){
            	die($msg);
        	}else{
        		return $msg;
        	}
        } else {
        	if($debug){
            	print_r($response); die();
        	}
            $ret = json_decode($response);
            if(!empty($ret->error)){
            	if(empty($options['no_debug']) && $debug_option==1){
                	echo "<pre>".print_r($ret, 1)."</pre>"; die();
                }
            }else{
            	if(isset($ret->msg)){
                	return $ret->msg;
            	}else{
        			$this->status_koneksi_sipkd = false;
            		$msg = $response.' (terkoneksi tapi gagal parsing data!)';
        			if($debug_option == 1){
            			die($msg);
            		}else{
            			return $msg;
            		}
            	}
            }
        }
    }

}