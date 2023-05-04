<?php
namespace Bookmify;



// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Querify
 */
class Querify {
 
	private $_limit;
	private $_page;
	private $_query;
	private $_total;
	private $_entity;
	
	
	
	/**
     * Construct
	 * @since 1.0.0
     */
	public function __construct( $query, $entity ) {
     
		global $wpdb;
		
		$this->_entity 	= $entity;
		$this->_query 	= $query;
		$rs 			= $wpdb->get_results( $query );
		$this->_total 	= $wpdb->num_rows;

	}
	

	
	/**
     * Get Data
	 * @since 1.0.0
     */
	public function getData( $limit = 10, $page = 1, $filters = array(), $order='ASC') {
     	global $wpdb;
		
		$this->_limit   = $limit;
		$this->_page    = $page;

		$string = '';
		
		
		// APPOINTMENT
		if($this->_entity == 'appointment'){
			
			$this->_query	 	= $this->_query . " GROUP BY a.id ORDER BY a.start_date";
			// get total posts by filer
			$rs 				= $wpdb->get_results( $this->_query );
			$this->_total 		= $wpdb->num_rows;
		}
		
		// USER APPOINTMENT
		if($this->_entity == 'user_appointment'){
//			
//			$this->_query	 = $this->_query . " GROUP BY a.id ORDER BY a.start_date";
//			// get total posts by filer
//			$rs = $wpdb->get_results( $this->_query );
//			$this->_total = $wpdb->num_rows;
		}
		
		

		
		// CUSTOMER
		if($this->_entity == 'customer'){

			// FILTER
			if(!empty($filters)){
				
				
				$string_start 	= " WHERE ";
				$search_text 	= '';
				$statuses 		= '';

				foreach($filters as $key => $filter){

					if($key == 'search'){
						$search_text = $filter;
					}
				}

				// search query
				$search = '';
				if($search_text != ''){$search = "CONCAT( first_name, last_name, email, phone ) LIKE '%".$search_text."%' AND";}


				$string = $string_start.$search;
				$string = rtrim($string, 'AND');

				$this->_query		= $this->_query . $string; 
				
				// get total posts by filer
				$rs = $wpdb->get_results( $this->_query );
				$this->_total = $wpdb->num_rows;

			}
			
			
			// ORDERING 
			$this->_query	 = $this->_query . " ORDER BY first_name ".$order;
			
		}
		
		

		
		// Employee filter
		if($this->_entity == 'employee_list'){

			// FILTER
			if(!empty($filters)){
				
				
				$string_start 	= " WHERE ";
				$search_text 	= '';
				$status 		= '';
				$list			= array();

				foreach($filters as $key => $filter){

					if($key == 'search'){
						$search_text = $filter;
					}
					if($key == 'ids'){
						$list = $filter;
					}
				}

				// search query
				$search = '';
				if($search_text != ''){$search = "CONCAT( first_name, last_name, email, phone ) LIKE '%".$search_text."%' AND";}


				// service query
				$status = '';
				if(!empty($list)){$status = " `id` IN (" . implode(',', array_map('intval', $list)) . ") AND";}
				
				$string = $string_start.$search.$status;
				$string = rtrim($string, 'AND');

				$this->_query		= $this->_query . $string; 
				
				// get total posts by filer
				$rs = $wpdb->get_results( $this->_query );
				$this->_total = $wpdb->num_rows;

			}
			
			
			// ORDERING 
			$this->_query	 = $this->_query . " ORDER BY first_name ".$order;
			
			
		}
		
		
		
		// PAYMENT FILTERING
		if($this->_entity == 'payment'){
			$this->_query	 = $this->_query . " ORDER BY p.created_date";
		}
		
		
		// Add ORDER BY according to entity type
		if($this->_entity == 'dayoff'){
			$this->_query	 = $this->_query . " ORDER BY date, title";
		}
		
		if($this->_entity == 'location'){
			$this->_query	 = $this->_query . " ORDER BY title";
		}
		
		
		// SERVICE
		if($this->_entity == 'service'){

			// FILTER
			if(!empty($filters)){
				
				
				$string_start 	= " WHERE ";
				$id 			= '';
//				$statuses 		= '';

				foreach($filters as $key => $filter){

					if($key == 'id'){
						$id = $filter;
					}
				}

				// search query
				$search = '';
				if($id != 'all'){
					$search = "category_id=".$id." AND";
					
					$string = $string_start.$search;
					$string = rtrim($string, 'AND');
					
				}else{
					$string = '';
				}
				


				$this->_query		= $this->_query . $string; 
				
				// get total posts by filer
				$rs = $wpdb->get_results( $this->_query );
				$this->_total = $wpdb->num_rows;

			}
			
			
			
		}
		if($this->_entity == 'service'){
			$this->_query	 = $this->_query . " ORDER BY title";
		}

		// Add LIMIT
		if ( $this->_limit == 'all' ) {
			$query      = $this->_query;
		} else {
			$query      = $this->_query . " LIMIT " . ( ( $this->_page - 1 ) * $this->_limit ) . ", $this->_limit";
		}
		
		
		$rs = $wpdb->get_results( $query, OBJECT);

		
		$results = [];
		foreach ($rs as $row) {
			$results[]  = $row;
		}

		
		$result         = new \stdClass();
		$result->page   = $this->_page;
		$result->limit  = $this->_limit;
		$result->total  = $this->_total;
		$result->test	= $this->_query;
		$result->data   = $results;
		
	
		return $result;
	}
 
	
	/**
     * Create Links
	 * @since 1.0.0
     */
	public function getPagination( $links, $list_class ) {
		if ( $this->_limit == 'all' ) {
			return '';
		}
		$last       = ceil( $this->_total / $this->_limit );

		$start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
		$end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;

		$html       = '<ul class="' . $list_class . '" data-entity="'.$this->_entity.'">';

		$class      = ( $this->_page == 1 ) ? "disabled" : "";
		$html       .= '<li class="prev ' . $class . '"><a href="#">&laquo;</a></li>';

		if ( $start > 1 ) {
			$html   .= '<li><a href="#" data-page="1">1</a></li>';
			if(($this->_page - $links - 1) != 1){$html   .= '<li><span>...</span></li>';}
		}

		for ( $i = $start ; $i <= $end; $i++ ) {
			$class  = ( $this->_page == $i ) ? "active" : "";
			if($last > 1){
				$html   .= '<li class="' . $class . '"><a href="#" data-page="'. $i .'">' . $i . '</a></li>';
			}
			
		}

		if ( $end < $last ) {
			if(($this->_page + $links +1) != $last){$html   .= '<li><span>...</span></li>';}
			$html   .= '<li><a href="#" data-page="'. $last .'">' . $last . '</a></li>';
		}

		$class      = ( $this->_page == $last ) ? "disabled" : "";
		$html       .= '<li class="next ' . $class . '"><a href="#">&raquo;</a></li>';

		$html       .= '</ul>';

		if($this->_total != ''){return $html;}
		
	}
	
	
}

