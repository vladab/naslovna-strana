<?php
if ( !class_exists( 'WP_List_Table' ) )
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class Naslovana_Email_Captured_Table_List extends WP_List_Table
{
	private $order;
	private $orderby;
	private $post_id;
	private $posts_per_page = 10;

	public function __construct() {
		parent::__construct( array(
				'singular' => 'gb_email_captured',
				'plural'   => 'gb_email_captureds',
				'ajax'     => true
			) );
		$this->set_order();
		$this->set_orderby();
		$this->prepare_items();
		$this->display();
	}

	private function get_sql_results() {
		global $wpdb;
		$table_name = $wpdb->prefix . NASLOVAN_STRANA_EMAIL_LIST_TABLE_NAME;
		$sql_results = $wpdb->get_results( "
				SELECT ID, email_address, status, mobile_number, location, ip_address, creation_date
	            FROM $table_name e
			");
		return $sql_results;
	}

	public function set_order() {
		$order = 'DESC';
		if ( isset( $_GET['order'] ) and $_GET['order'] )
			$order = $_GET['order'];
		$this->order = esc_sql( $order );
	}

	public function set_orderby() {
		$orderby = 'post_modified';
		if ( isset( $_GET['orderby'] ) and $_GET['orderby'] )
			$orderby = $_GET['orderby'];
		$this->orderby = esc_sql( $orderby );
	}

	public function get_columns() {
		$columns = array(
			'email_address' 	=> __( 'Email Address' ),
			'from' 			    => __( 'IP / From' ),
			'mobile' 	        => __( 'Mobile Phone' ),
            'loc'               => __( 'Location' ),
			'date'      		=> __( 'Created' ),
			'actions' 			=> __( 'Actions' )
		);
		return $columns;
	}

	public function get_sortable_columns() {
		$sortable = array(
			'post_modified' => array( 'post_modified', true )
		);
		return $sortable;
	}

	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable
		);

		// SQL results
		$posts = $this->get_sql_results();
		empty( $posts ) and $posts = array();


		// >>>> Pagination
		$per_page     = $this->posts_per_page;
		$current_page = $this->get_pagenum();
		$total_items  = count( $posts );
		$this->set_pagination_args( array (
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			) );
		$last_post = $current_page * $per_page;
		$first_post = $last_post - $per_page + 1;
		$last_post > $total_items and $last_post = $total_items;

		// Setup the range of keys/indizes that contain
		// the posts on the currently displayed page(d).
		// Flip keys with values as the range outputs the range in the values.
		$range = array_flip( range( $first_post - 1, $last_post - 1, 1 ) );

		// Filter out the posts we're not displaying on the current page.
		$posts_array = array_intersect_key( $posts, $range );
		// <<<< Pagination

		// Prepare the data
		$title = __( 'Email Address' );
		foreach ( $posts_array as $key => $post ) {
			
			// From
			$from_ip =  $post->ip_address;
			// Move this and use foreach class .flag
			// "Users first, optimization second."  - Betas
			if( $from_ip != '' && $from_ip != '127.0.0.1') {
				$getter = '<span><img src="'.NASLOVNA_STRANA_RESOURCE_URL.'blank.gif" class="flag " id="flag_'. $post->ID .'"></span>
					<script language="JavaScript">jQuery.get("http://ipinfo.io/'.$from_ip.'/json", function(response) {
						var country = response.country.toString().toLowerCase();
					jQuery("#flag_'. $post->ID .'").addClass("flag-"+country);
							}, "jsonp");</script>';
				$post->from = $from_ip . ' - '. $getter;
			} else if( $from_ip == '127.0.0.1' ) {
				$post->from = 'Dev mode';
			} else {
				$post->from = 'Not set';
			}

			// Stutus 
			if ( $post->status == 'blocked' ) {
				$post->status = '<span class="red">Blocked</span>';
			} else if( $post->status == 'subscribed' ) {
				$post->status = '<span class="light_green">Subscribed</span>';	
			} else if( $post->status == 'unsubscribed' ) {
				$post->status = '<span class="white">Unsubscribed</span>';
			} else {
			    $post->status = '<span class="white">' . $post->status . '</span>';
			}

			$post->mobile = $post->mobile_number;

            $post->loc = $post->location;

			// Time Created
			$post->date = $this->convert_time_elapsed($post->creation_date).' ago';//(' . date('Y-m-d H:i:s', $timestamp) . ')';

			// Actions

//			$block_action = "<a class='block_email_address' title='Block: {$title}' rel='{$post->ID}' href='javascript:void()'>";
//			$block_action .= '<img src="' . NASLOVNA_STRANA_RESOURCE_URL . 'block.gif"></a>';
			$delete_action = "<a class='remove_email_address' rel='{$post->ID}' title='Delete: {$title}' href='javascript:void()'>";
			$delete_action .= '<img src="' . NASLOVNA_STRANA_RESOURCE_URL . 'delete.gif"></a>';
			//$post->actions = $block_action . ' - ' .$delete_action;
            $post->actions = $delete_action;
		}
		$this->items = $posts_array;
	}

	public function column_default( $item, $column_name ) {
		return $item->$column_name;
	}

	public function display_tablenav( $which ) { ?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<!--
			<div class="alignleft actions">
				<?php // $this->bulk_actions( $which ); ?>
			</div>
			 -->
			<?php
		$this->extra_tablenav( $which );
		$this->pagination( $which ); ?>
			<br class="clear" />
		</div>
		<?php
	}

	public function ajax_user_can() {
		return current_user_can( 'edit_posts' );
	}

	public function no_items() {
		_e( 'No posts found.' );
	}

	public function get_views() {
		return array();
	}
	public function extra_tablenav( $which ) {
		global $wp_meta_boxes;
		$views = $this->get_views();
		if ( empty( $views ) )
			return;

		$this->views();
	}
    public function convert_time_elapsed ($time) {

        $time = strtotime( current_time( 'mysql' ) ) - strtotime( $time ); // to get the time since that moment

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
    }
}
