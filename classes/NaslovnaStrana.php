<?php 

class NaslovnaStrana {

    public static function ReturnImages( $_image_size = '105', $data_sources = array(), $extra_style = '' ) {
        if( empty( $data_sources) ) {
            $sources = NaslovnaStrana::PrepareData();
        } else {
            $sources = $data_sources;
        }
        $data = '';
        foreach ($sources as $key => $src) {
            $path = 'uploads';
            $sanitized = preg_replace('/[^a-zA-Z0-9-_\.]/','', $key);
            $local_source = NaslovnaStrana::CheckIfImageExists($path, $sanitized, '.jpg');
            if( $local_source == 0) {

                // Get Url from original source
                $image_url = NaslovnaStrana::ImageExtractor( $src );
                // If image is not fetched
                if( $image_url == '') {
                    continue;
                }
                // Save image and get local copy
                $local_source = NaslovnaStrana::ImageSaveByYearMonth( $path, $image_url, $sanitized ,'.jpg');

                if( $local_source  == '' ) {
                    continue;
                }
            } else {

                $year = date("Y");
                $month = date("m");
                $day = date("d");

                $local_source = "/$path/$year/$month/$day-$sanitized.jpg";
            }
            // Construct url for a image
            $image_url =  $_SERVER['HTTP_HOST'] . $local_source;
            // Overiding image size for custom size
            if( isset( $src['override_email_width'] ) &&
                $src['override_email_width'] == true &&
                isset( $src['override_email_width_value'] )
            ) {
                $data .=  '<img src="http://'. $image_url . '"height="auto" width="'. $src['override_email_width_value'] .'">';
            } else {
                $data .=  '<img src="http://'. $image_url . '"height="auto" width="'. $_image_size .'" style="'. $extra_style .'">';
            }
        }
        return $data;
    }
	public function ForceCheck() {
		
		$sources = NaslovnaStrana::PrepareData();

		foreach ($sources as $key => $src) {
			$image_url = NaslovnaStrana::ImageExtractor( $src );

			echo '<img src="'. $image_url . '" width="100">';
		}
	}

    public static function PrepareData() {
        // Sources list
        $sources = get_option('NASLOVNA_STRANA_SOURCES_FRONT_PAGE', array() );
        return maybe_unserialize( $sources );
    }
    public static function PrepareDataEmail() {
        // Sources list
        $sources = get_option('NASLOVNA_STRANA_SOURCES', array() );
        return maybe_unserialize( $sources );
    }


    /**
     * @param $source
     * @return string
     */
    public static function ImageExtractor( $source )
	{
        $href = '';
		switch ($source['type']) {
			case 'dom_query':

				$html = file_get_contents($source['url']);
				$dom = new DOMDocument;
				@$dom->loadHTML($html);
				$xpath = new DOMXPath($dom);
				
				$nodeList = $xpath->query( $source['dom_query_string'] );
				foreach ($nodeList as $node) {
				    $href = $node->getAttribute( $source['html_object_src_type'] );


				}
				break;

			case 'img_src':
				$href = $source['img_src_address'];
				if( date('D') == 'Sat' && $source['has_weekends_isue'] == 'no')
				{
					$date_shift = date('dmy', strtotime( '-1 day' ));
					$href = str_replace( date('dmy'), $date_shift, $href );
				}
				elseif ( date('D') == 'Sun' && $source['has_weekends_isue'] == 'no')
				{
					$date_shift = date('dmy', strtotime( '-2 day' ));
					$href = str_replace( date('dmy'), $date_shift, $href );
				}
                if( $source['url'] == 'http://www.24sata.rs/' ) {
                    $href = 'http://e24.24sata.rs/issues/24sata_'. date('dmy') .'/pages/large/24sata_'. date('dmy') .'-000001.jpg';
                }
				break;

            case 'pdf_src':
                $href = self::get_url_from_pdfonline_tool( $source['pdf_src_address'], $source['extra_param'] );
                break;

			default:
				return 0;
				break;
		}
		if( isset( $source['relative'] ) && $source['relative'] ) {
			$href = $source['url'] . $href;
		}
		if( isset( $source['change_src'] ) && $source['change_src'] ) {
			$href = substr($href, 0, $source['change_src_offset']);
			$href .= $source['change_src_string'];
		}
		if( isset( $source['change_src_find'] ) && $source['change_src_find'] ) {
			$href = str_replace( $source['change_src_find'], $source['change_src_replace'], $href );
		}
        if( isset( $source['_parse_url_extract_host'] ) && $source['_parse_url_extract_host'] ){
            $parse = parse_url($source['url']);
            $href = 'http://www.' . $parse['host'] . $href;
        }

		return $href;
	}

	private static function ImageSaveByYearMonth( $path, $image_url, $img_name, $img_ext = '.jpg') {
		
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$img_file = "./$path/$year/$month/$day-$img_name$img_ext";
		if ( !file_exists( $img_file ) ) {
			$filename_year = "./". $path . '/' .$year;   
			$filename_month = "./". $path . '/' .$year."/".$month;

            // Directory creation
			if(file_exists($filename_year)){
			    if(file_exists($filename_month)==false){
			        mkdir($filename_month,0777);
			    }
			}else{
			    mkdir($filename_year,0777);
			}

			// Check if url valid
			if ( self::checkRemoteFile( $image_url ) ) {
                file_put_contents($img_file, file_get_contents($image_url));
                return substr($img_file, 1);
			}
		}
		return '';
	}

	public static function CheckIfImageExists( $path, $img_name, $img_ext ) {
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$img_file = $_SERVER['DOCUMENT_ROOT'] . "/$path/$year/$month/$day-$img_name$img_ext";
		if ( file_exists( $img_file ) ) {
			return 1;
		} else {
		    return 0;
        }
	}
    public static function checkRemoteFile($url)
    {
        // Check headers
        $headers = @get_headers($url);
        if( substr($headers[0], 9, 3) != "404" ) {

            if ( is_array( @getimagesize( $url ) ) ) {
                return true;
            }
        }
        return false;
    }
    public static function get_url_from_pdfonline_tool($pdf_url, $extra_param = '')
    {
        //resource API Key
        $api_key = 'E5EA9081F6C55751297BF6F6AF05DC9B608CA5B7';
        $url = "http://online.verypdf.com/api/?apikey=$api_key&app=pdftools&infile=$pdf_url&outfile=one.jpg&-f=1&-l=1&$extra_param";
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return (string)substr($data, 9, -4);
    }
}
?>