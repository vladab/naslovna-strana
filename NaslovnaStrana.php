<?php 

class NaslovnaStrana {

    public static function  ReturnImages( $_image_size = '105', $data_sources = array(), $extra_style = '' ) {
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

            $data .=  '<img src="http://'. $_SERVER['HTTP_HOST'] . $local_source . '" width="'. $_image_size .'" style="'. $extra_style .'">';
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

    public function PrepareData() {
        // Sources list
        $sources = array(
            'Informer' => array(
                'url' => 'http://www.informer.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//div[@class="previewizdanja"]/a/img',
                'html_object_src_type' => 'src',

                'relative' => true,
            ),
            '24 Sata' => array(
                'url' => 'http://www.24sata.rs/',
                'type' => 'img_src',
                'img_src_address' => 'http://e24.24sata.rs/issues/24sata_'. date('dmy') .'/pages/large/24sata_'. date('dmy') .'-000001.jpg',
                'has_weekends_isue' => 'no',
            ),
            'Blic' => array(
                'url' => 'http://www.blic.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//a[@id="blic_naslovna_print"]/img',
                'html_object_src_type' => 'src',

                'relative' => true,
                'change_src' => true,
                'change_src_offset' => -5,
                'change_src_string' => 'big.jpg',
            ),
            'Politika' => array(
                'url' => 'http://www.politika.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//a[@class="home_print_img"]',
                'html_object_src_type' => 'href',

                'relative' => false,
                'change_src' => false,
            ),
            'Sportski Zurnal' => array(
                'url' => 'http://www.zurnal.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//aside/a',
                'html_object_src_type' => 'href',

                'relative' => true,
            ),
            'Narodne Novine Nis' => array(
                'url' => 'http://www.narodne.com/',
                'type' => 'img_src',
                'img_src_address' => 'http://www.narodne.com/fliper/pages/large/01NN.jpg',
            ),
            'Magazin Tabloid' => array(
                'url' => 'http://www.magazin-tabloid.com/casopis/',
                'type' => 'dom_query',
                'dom_query_string' => '//a[@class="fancybox"]/img',
                'html_object_src_type' => 'src',

                'relative' => true,
            ),
            'Danas' => array(
                'url' => 'http://www.danas.rs/',
                'type' => 'pdf_src',
                'pdf_src_address' => 'http://www.danas.rs/upload/documents/Dodaci/'.date('Y').'/Dnevni/danas_srb_01.pdf',
                'extra_param' => '-width=794&-height=1106',
            ),
            'Vecernje Novosti' => array(
                'url' => 'http://www.novosti.rs/',
                'type' => 'img_src',
                'img_src_address' => 'http://www.novosti.rs/upload/images/banners/naslovna/naslovna-velika.jpg',
            ),
            'Kurir' => array(
                'url' => 'http://www.kurir-info.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//li[@id="print_page_01"]/div/img',
                'html_object_src_type' => 'src',

                'change_src_find' => 'slika-300x380',
                'change_src_replace' => 'slika-620x910',
            ),
        );
        return $sources;
    }
    public function PrepareDataEmail() {
        // Sources list
        $sources = array(
            'Blic' => array(
                'url' => 'http://www.blic.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//a[@id="blic_naslovna_print"]/img',
                'html_object_src_type' => 'src',

                'relative' => true,
                'change_src' => true,
                'change_src_offset' => -5,
                'change_src_string' => 'big.jpg',
            ),
            'Vecernje Novosti' => array(
                'url' => 'http://www.novosti.rs/',
                'type' => 'img_src',
                'img_src_address' => 'http://www.novosti.rs/upload/images/banners/naslovna/naslovna-velika.jpg',
            ),
            'Politika' => array(
                'url' => 'http://www.politika.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//a[@class="home_print_img"]',
                'html_object_src_type' => 'href',

                'relative' => false,
                'change_src' => false,
            ),
            'Danas' => array(
                'url' => 'http://www.danas.rs/',
                'type' => 'pdf_src',
                'pdf_src_address' => 'http://www.danas.rs/upload/documents/Dodaci/'.date('Y').'/Dnevni/danas_srb_01.pdf',
                'extra_param' => '-width=794&-height=1106',
            ),
            'Sportski Zurnal' => array(
                'url' => 'http://www.zurnal.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//div[@id="danasuszimage"]/a',
                'html_object_src_type' => 'href',
            ),
            'Narodne Novine Nis' => array(
                'url' => 'http://www.narodne.com/',
                'type' => 'img_src',
                'img_src_address' => 'http://www.narodne.com/fliper/pages/large/01NN.jpg',
            ),
            'Kurir' => array(
                'url' => 'http://www.kurir-info.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//li[@id="print_page_01"]/div/img',
                'html_object_src_type' => 'src',

                'change_src_find' => 'slika-300x380',
                'change_src_replace' => 'slika-620x910',
            ),
            'Informer' => array(
                'url' => 'http://www.informer.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//div[@class="previewizdanja"]/a/img',
                'html_object_src_type' => 'src',

                'relative' => true,
            ),
            '24 Sata' => array(
                'url' => 'http://www.24sata.rs/',
                'type' => 'img_src',
                'img_src_address' => 'http://e24.24sata.rs/issues/24sata_'. date('dmy') .'/pages/large/24sata_'. date('dmy') .'-000001.jpg',
                'has_weekends_isue' => 'no',
            ),
//            'Magazin Tabloid' => array(
//                'url' => 'http://www.magazin-tabloid.com/casopis/',
//                'type' => 'dom_query',
//                'dom_query_string' => '//a[@class="fancybox"]/img',
//                'html_object_src_type' => 'src',
//
//                'relative' => true,
//            ),
            'Vijesti' => array(
                'url' => 'http://www.vijesti.me/p/naslovnice/'. date('Y').'-'.date('m').'-'.date('d'),
                'type' => 'dom_query',
                'dom_query_string' => '//article/figure/img',
                'html_object_src_type' => 'src',

                '_parse_url_extract_host' => true,
            ),
            'Corax' => array(
                'url' => 'http://www.danas.rs/',
                'type' => 'img_src',
                'img_src_address' => 'http://www.danas.rs/upload/images/corax/'.date('Y').'/'.date('n').'/'.date('j').'/coraxv-'.date('d').'-'.date('m').'-'.date('Y').'_ocp_w500_h516.jpg',
            ),
            'Novosti Strip' => array(
                'url' => 'http://novosti.rs/dodatni_sadrzaj/foto.110.html?galleryId=4',
                'type' => 'dom_query',
                'dom_query_string' => '//div[@class="visualBg"]/div/a[1]',
                'html_object_src_type' => 'href',
                '_parse_url_extract_host' => true,
            ),
            'Politika Strip' => array(
                'url' => 'http://www.politika.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//div[@id="main_holder"]/div[@class="right_col"]/div/div/a/img',
                'html_object_src_type' => 'src',
            ),
            'Blic Strip' => array(
                'url' => 'http://www.blic.rs/',
                'type' => 'dom_query',
                'dom_query_string' => '//div[@id="strip_right"]/a/img',
                'html_object_src_type' => 'src',

                'change_src' => true,
                'change_src_offset' => -9,
                'change_src_string' => '.jpg',
            ),
        );
        return $sources;
    }


    /**
     * @param $source
     * @return string
     */
    public function ImageExtractor( $source )
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
					$$href = str_replace( date('dmy'), $date_shift, $href );
				}
				elseif ( date('D') == 'Sun' && $source['has_weekends_isue'] == 'no')
				{
					$date_shift = date('dmy', strtotime( '-2 day' ));
					$$href = str_replace( date('dmy'), $date_shift, $href );
				}
				break;

            case 'pdf_src':
                $href = self::get_url_from_pdfonline_tool( $source['pdf_src_address'], $source['extra_param'] );
                break;

			default:
				return 0;
				break;
		}
		if ( $source['relative'] ) {
			$href = $source['url'] . $href;
		}
		if ( $source['change_src'] ) {
			$href = substr($href, 0, $source['change_src_offset']);
			$href .= $source['change_src_string'];
		}
		if ( $source['change_src_find'] ) {
			$href = str_replace( $source['change_src_find'], $source['change_src_replace'], $href );
		}
        if( $source['_parse_url_extract_host'] ){
            $parse = parse_url($source['url']);
            $href = 'http://www.' . $parse['host'] . $href;
        }

		return $href;
	}

	private function ImageSaveByYearMonth( $path, $image_url, $img_name, $img_ext = '.jpg') {
		
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
    public function checkRemoteFile($url)
    {
        if( is_array( getimagesize( $url ) ) ) {
            return true;
        }
        return false;
    }
    public function get_url_from_pdfonline_tool($pdf_url, $extra_param = '')
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