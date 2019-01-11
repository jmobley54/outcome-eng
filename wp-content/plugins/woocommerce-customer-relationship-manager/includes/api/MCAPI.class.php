<?php
class MCAPI_Wc_Crm {
	var $version = "3.0";
	var $errorMessage;
	var $errorCode;

	/**
	 * Cache the information on the API location on the server
	 */
	var $apiUrl;

	/**
	 * Default to a 300 second timeout on server calls
	 */
	var $timeout = 300;

	/**
	 * Default to a 8K chunk size
	 */
	var $chunkSize = 8192;

	/**
	 * Cache the user api_key so we only have to log in once per client instantiation
	 */
	var $api_key;

	/**
	 * Cache the user api_key so we only have to log in once per client instantiation
	 */
	var $secure = false;

	/**
	 * Connect to the MailChimp API for a given list.
	 *
	 * @param string $apikey Your MailChimp apikey
	 * @param string $secure Whether or not this should use a secure connection
	 */
	public function __construct ( $apikey, $secure = false ) {
		$this->secure = $secure;
		$this->apiUrl = parse_url( "http://api.mailchimp.com/" . $this->version . "/?output=php" );
		$this->api_key = $apikey;
	}

	function setTimeout( $seconds ) {
		if ( is_int( $seconds ) ) {
			$this->timeout = $seconds;
			return true;
		}
	}

	function getTimeout() {
		return $this->timeout;
	}

	function useSecure( $val ) {
		if ( $val === true ) {
			$this->secure = true;
		} else {
			$this->secure = false;
		}
	}

	/**
	 * Actually connect to the server and call the requested methods, parsing the result
	 * You should never have to call this function manually
	 */
	function __call( $method, $params ) {
		$dc = "us1";
		if ( strstr( $this->api_key, "-" ) ) {
			list( $key, $dc ) = explode( "-", $this->api_key, 2 );
			if ( !$dc ) $dc = "us1";
		}
		$host = $dc . "." . $this->apiUrl["host"];

		$this->errorMessage = "";
		$this->errorCode = "";

        $response = wp_remote_get(trailingslashit(esc_url($host)) . "3.0/lists", array(
            'timeout'     => $this->timeout,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent'  => 'MCAPImini/' . $this->version,
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( 'user:' . $this->api_key ),
                'Content-Type' =>  'application/json; charset=utf-8'
            ),
            'sslverify'   => $this->secure,
        ));

        if ( is_wp_error( $response ) ) {
            $this->errorMessage = $response->get_error_message();
            $this->errorCode = $response->get_error_code();
            return false;
        }


		return json_decode($response['body']);
	}

	protected $function_map = array('campaignUnschedule' => array("cid"),
		'campaignSchedule' => array("cid", "schedule_time", "schedule_time_b"),
		'campaignResume' => array("cid"),
		'campaignPause' => array("cid"),
		'campaignSendNow' => array("cid"),
		'campaignSendTest' => array("cid", "test_emails", "send_type"),
		'campaignSegmentTest' => array("list_id", "options"),
		'campaignCreate' => array("type", "options", "content", "segment_opts", "type_opts"),
		'campaignUpdate' => array("cid", "name", "value"),
		'campaignReplicate' => array("cid"),
		'campaignDelete' => array("cid"),
		'campaigns' => array("filters", "start", "limit"),
		'campaignStats' => array("cid"),
		'campaignClickStats' => array("cid"),
		'campaignEmailDomainPerformance' => array("cid"),
		'campaignMembers' => array("cid", "status", "start", "limit"),
		'campaignHardBounces' => array("cid", "start", "limit"),
		'campaignSoftBounces' => array("cid", "start", "limit"),
		'campaignUnsubscribes' => array("cid", "start", "limit"),
		'campaignAbuseReports' => array("cid", "since", "start", "limit"),
		'campaignAdvice' => array("cid"),
		'campaignAnalytics' => array("cid"),
		'campaignGeoOpens' => array("cid"),
		'campaignGeoOpensForCountry' => array("cid", "code"),
		'campaignEepUrlStats' => array("cid"),
		'campaignBounceMessage' => array("cid", "email"),
		'campaignBounceMessages' => array("cid", "start", "limit", "since"),
		'campaignEcommOrders' => array("cid", "start", "limit", "since"),
		'campaignShareReport' => array("cid", "opts"),
		'campaignContent' => array("cid", "for_archive"),
		'campaignTemplateContent' => array("cid"),
		'campaignOpenedAIM' => array("cid", "start", "limit"),
		'campaignNotOpenedAIM' => array("cid", "start", "limit"),
		'campaignClickDetailAIM' => array("cid", "url", "start", "limit"),
		'campaignEmailStatsAIM' => array("cid", "email_address"),
		'campaignEmailStatsAIMAll' => array("cid", "start", "limit"),
		'campaignEcommOrderAdd' => array("order"),
		'lists' => array("filters", "start", "limit"),
		'listMergeVars' => array("id"),
		'listMergeVarAdd' => array("id", "tag", "name", "options"),
		'listMergeVarUpdate' => array("id", "tag", "options"),
		'listMergeVarDel' => array("id", "tag"),
		'listInterestGroupings' => array("id"),
		'listInterestGroupAdd' => array("id", "group_name", "grouping_id"),
		'listInterestGroupDel' => array("id", "group_name", "grouping_id"),
		'listInterestGroupUpdate' => array("id", "old_name", "new_name", "grouping_id"),
		'listInterestGroupingAdd' => array("id", "name", "type", "groups"),
		'listInterestGroupingUpdate' => array("grouping_id", "name", "value"),
		'listInterestGroupingDel' => array("grouping_id"),
		'listWebhooks' => array("id"),
		'listWebhookAdd' => array("id", "url", "actions", "sources"),
		'listWebhookDel' => array("id", "url"),
		'listStaticSegments' => array("id"),
		'listStaticSegmentAdd' => array("id", "name"),
		'listStaticSegmentReset' => array("id", "seg_id"),
		'listStaticSegmentDel' => array("id", "seg_id"),
		'listStaticSegmentMembersAdd' => array("id", "seg_id", "batch"),
		'listStaticSegmentMembersDel' => array("id", "seg_id", "batch"),
		'listSubscribe' => array("id", "email_address", "merge_vars", "email_type", "double_optin", "update_existing", "replace_interests", "send_welcome"),
		'listUnsubscribe' => array("id", "email_address", "delete_member", "send_goodbye", "send_notify"),
		'listUpdateMember' => array("id", "email_address", "merge_vars", "email_type", "replace_interests"),
		'listBatchSubscribe' => array("id", "batch", "double_optin", "update_existing", "replace_interests"),
		'listBatchUnsubscribe' => array("id", "emails", "delete_member", "send_goodbye", "send_notify"),
		'listMembers' => array("id", "status", "since", "start", "limit"),
		'listMemberInfo' => array("id", "email_address"),
		'listMemberActivity' => array("id", "email_address"),
		'listAbuseReports' => array("id", "start", "limit", "since"),
		'listGrowthHistory' => array("id"),
		'listActivity' => array("id"),
		'listLocations' => array("id"),
		'listClients' => array("id"),
		'templates' => array("types", "category", "inactives"),
		'templateInfo' => array("tid", "type"),
		'templateAdd' => array("name", "html"),
		'templateUpdate' => array("id", "values"),
		'templateDel' => array("id"),
		'templateUndel' => array("id"),
		'getAccountDetails' => array(),
		'generateText' => array("type", "content"),
		'inlineCss' => array("html", "strip_css"),
		'folders' => array("type"),
		'folderAdd' => array("name", "type"),
		'folderUpdate' => array("fid", "name", "type"),
		'folderDel' => array("fid", "type"),
		'ecommOrders' => array("start", "limit", "since"),
		'ecommOrderAdd' => array("order"),
		'ecommOrderDel' => array("store_id", "order_id"),
		'listsForEmail' => array("email_address"),
		'campaignsForEmail' => array("email_address"),
		'chimpChatter' => array(),
		'apikeys' => array("username", "password", "expired"),
		'apikeyAdd' => array("username", "password"),
		'apikeyExpire' => array("username", "password"),
		'ping' => array());

    }

?>