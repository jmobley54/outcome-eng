<?php
/** 
 * EVOLI - ajax
 * @version 0.1
 */
class EVOLI_ajax{
	public function __construct(){
		$ajax_events = array(
				'evoliajax_list'=>'evoliajax_list',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {				
				add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
			}
	}
	function evoliajax_list(){

		$status = 'good';
		global $eventon_li;

		$filters = array(array(
			'filter_type'=>'tax',
			'filter_name'=>$_POST['tax'],
			'filter_val'=>$_POST['termid']
		));

		$content = $eventon_li->frontend->get_events_list(array(
			'filters'=>$filters, 
			$_POST['tax']=>$_POST['termid'],
			'sep_month'=>$_POST['sepm'],
			'number_of_months'=>$_POST['numm'],
			'ux_val'=>$_POST['ux'],
			'el_type'=>'ue'
		));

		echo json_encode(array(
			'content'=>$content, 'status'=>$status
		));
		exit;
	}
}
new EVOLI_ajax();