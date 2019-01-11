<?php

// Load main class
require_once(dirname(dirname(__FILE__)).'/core/module.php');

/**
 * WP Link Status Core Pro Tools URL class
 *
 * @package WP Link Status Pro
 * @subpackage WP Link Status Pro Core
 */
class WPLNST_Core_Pro_Tools_URL extends WPLNST_Core_Module {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Stored update data
	 */
	protected $updates = array();



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates a singleton object
	 */
	public static function instantiate($args = null) {
		return self::get_instance(get_class(), $args);
	}



	// URL tools
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Custom constructor
	 */
	protected function on_construct($args = null) {
		
		// Check submitted data
		if ($this->check_ajax_submit($response, 'edit_posts', 'wplnst_tools_url_nonce')) {
			
			// Check URLs
			if (empty($_POST['urls'])) {
				
				// Parse error
				$response['status'] = 'error';
				$response['reason'] = __('Empty URLs pack', 'wplnst');
				
			// Process data
			} elseif (false === ($response['data'] = $this->process($_POST['urls'], $_POST['op'], !empty($_POST['db'])))) {

				// Parse error
				$response['status'] = 'error';
				$response['reason'] = __('Error when process URLs pack', 'wplnst');
			}
		}
		
		// Done
		self::output_ajax_response($response);
	}



	/**
	 * Perform search and replace
	 */
	private function process($inputs, $operation, $save) {
		
		// Timeout and memory
		set_time_limit(0);
		
		// Check operation value
		if (!in_array($operation, array('nofollow', 'dofollow', 'remove', 'redirect', 'object')))
			return false;
		
		// Initialize
		$urls = $added = array();
		
		
		/* First step, create variations from input URL */
		
		// Enum URLs
		$inputs = explode("\n", $inputs);
		foreach ($inputs as $url) {
			
			// Trim and check
			$url = trim($url);
			if (empty($url) || in_array($url, $added))
				continue;
			
			// First variation
			$flavors = array($url);
				
			// Add raw URL				
			$added[] = $url;
				
			// Decoding version
			if (strpos($url, '%')) {
				$decoded = urldecode($url);
				if (!in_array($decoded, $added)) {
					$added[] = $decoded;
					$flavors[] = $decoded;
				}
			}
				
			// With entities
			if (strpos($url, '&')) {
				$ampersand = str_replace('&', '&amp;', $url);
				if (!in_array($ampersand, $added)) {
					$added[] = $ampersand;
					$flavors[] = $ampersand;
				}
			}
			
			// Revert entities
			if (strpos($url, '&amp;')) {
				$ampersand = str_replace('&amp;', '&', $url);
				if (!in_array($ampersand, $added)) {
					$added[] = $ampersand;
					$flavors[] = $ampersand;
				}
			}
			
			// Encoding
			$unencoded = utf8_decode($url);
			if (!in_array($unencoded, $added)) {
				$added[] = $unencoded;
				$flavors[] = $unencoded;
			}
			
			// Add variations
			$urls[] = $flavors;
		}
		
		
		/* Second step, check each URL finding content coincidences */
		
		// Check URLs
		if (empty($urls))
			return false;
		
		// Globals
		global $wpdb;
		
		// Result array
		$data = array();
		
		// Prepare array of status codes
		$status_codes = WPLNST_Core_Types::get_status_codes_raw();
		
		// Enum all			
		foreach ($urls as $variations) {
			
			// Flags
			$notfound = true;
			
			// Main URL and base
			$url = $variations[0];
			$base_url = $this->simplify_url($url);
			
			// Posts data
			$data_posts = array();
			
			// Variations
			foreach ($variations as $variation) {
				
				// Prepare URL
				$base_url_item = $this->simplify_url($variation);
				$base_url_item_is_host = $this->is_host_url('http://'.$base_url_item);
				
				// Check base
				if (empty($base_url_item))
					continue;
					
				// Search posts
				$posts = $wpdb->get_results('SELECT ID, post_content FROM '.$wpdb->posts.' WHERE post_type IN ("post", "page") AND post_status IN ("publish", "future", "draft", "pending", "private") AND post_content LIKE "%'.esc_sql(str_replace('%', '_', $base_url_item)).'%"');
				if (empty($posts) || !is_array($posts))
					continue;
					
				// Enum posts
				foreach ($posts as $post) {
					
					// Initialize
					$previous = false;
					$notfound_post = true;
					$content_updated = $this->process_updates_get($post->ID);
					$content = $content_before = (false === $content_updated)? $post->post_content : $content_updated;
					
					// HTML object support
					if ('object' == $operation) {
						
						// Search for objects
						if (false !== stripos($content, '<object') && preg_match_all('/<div[^>]*><object((?!<object).)+<\/object><\/div>\r?\n?|<object((?!<object).)+<\/object>\r?\n?/isUu', $content, $matches, PREG_SET_ORDER) > 0) {
							
							// Enum all matches
							foreach ($matches as $match) {
								if (false !== stripos($match[0], $base_url_item)) {												
									$content = str_ireplace($match[0], '', $content);
									$data_posts[] = array(
										'ID' 				=> $post->ID,
										'permalink' 		=> get_permalink($post->ID),
										'text' 				=> '',
										'previous' 			=> '',
										'redirect_url' 		=> '',
										'redirect_error' 	=> '',
										'result' 			=> '<b style="color: grey;">'.__('Removed object element', 'wplnst').'</b>'
									);
								}
							}
						}
					
					// Enum links
					} elseif (preg_match_all('/((<a[^>]+href=["|\'])(.+)(["|\'][^>]*>))(.*)(<\/a[^>]*>)/isUu', $content, $matches, PREG_SET_ORDER) > 0) {
						
						// Enum all matches
						foreach ($matches as $match) {
							
							// Check match
							if ($base_url_item != $this->simplify_url($match[3]))
								continue;
								
							// One match
							$notfound = false;
							$notfound_post = false;
							
							// Change to nofollow
							if ('nofollow' == $operation) {
								
								// Initialize
								$previous = false;
								$link = $link_new = $match[1];
								$result = '<i>'.__('nofollow exists', 'wplnst').'</i>';
								
								// Add new rel attribute to a
								if (false === stripos($link, 'rel=')) {
									$link_new = preg_replace('/(?=>)/', ' rel="nofollow"', $link_new);
									
								// Existing rel without nofollow
								} elseif (!preg_match('/(rel=["|\'].*)\s*\bnofollow\b\s*(.*["|\'])/iU', $link_new)) {
									$link_new = preg_replace('/(?<=rel=.)/i', 'nofollow ', $link_new);
								}
								
								// Check changes
								if ($link != $link_new) {

									// Default response
									$result = '<b>'.__('Changed to nofollow', 'wplnst').'</b>';
									
									// Compose new chunk
									$chunk = $link_new.$match[5].$match[6];
									
									// Replace in content
									$content_check = $content;
									$content = str_replace($match[0], $chunk, $content);
									
									// No replacement
									if ($content_check == $content) {
										$previous = true;
										$result = '<b style="color: grey;">'.__('Set nofollow previously', 'wplnst').'</b>';
									}
								}
							
							// Clean nofollow value
							} elseif ('dofollow' == $operation) {
								
								// Initialize
								$previous = false;
								$link = $link_new = $match[1];
								$result = '<i>'.__('nofollow not exists', 'wplnst').'</i>';
								
								// Check existing nofollow
								if (false !== stripos($link, 'nofollow')) {
									
									// Remove only nofollow value
									$link_new = preg_replace('/(rel=["|\'].*)\s*\bnofollow\b\s*(.*["|\'])/iU', '$1$2', $link_new);
									
									// Remove attribute if empty
									$link_new = preg_replace('/(\s*rel=["|\']\s*["|\']\s*)/iU', '', $link_new);	
								}
								
								// Check changes
								if ($link != $link_new) {
									
									// Default response
									$result = '<b>'.__('Removed nofollow', 'wplnst').'</b>';
									
									// Compose new chunk
									$chunk = $link_new.$match[5].$match[6];
									
									// Replace in content
									$content_check = $content;
									$content = str_replace($match[0], $chunk, $content);
									
									// No replacement
									if ($content_check == $content) {
										$previous = true;
										$result = '<b style="color: grey;">'.__('Removed nofollow previously', 'wplnst').'</b>';
									}
								}
							
							// Remove link and leave anchor
							} elseif ('remove' == $operation) {
								
								// Initialize
								$previous = false;
								$content_check = $content;
								$result = '<b>'.__('Removed link', 'wplnst').'</b>';
								
								// Attempt to remove link and leave the anchor text											
								$content = str_replace($match[0], $match[5], $content);
								
								// Check changes
								if ($content_check == $content) {
									$previous = true;
									$result = '<b style="color: grey;">'.__('Removed link before', 'wplnst').'</b>';
								}
							
							// Redirect
							} elseif ('redirect' == $operation) {
								
								// Reset flags
								$redirect_url 	 = '';
								$redirect_status = '';
								$redirect_error  = '';
								
								// Initialize
								$previous = false;
								$result = '<b style="color: red;">'.__('Something went wrong', 'wplnst').'</b>';
								
								// Check redirection
								$redirection = $this->get_url_redirect($match[3]);
								if (!empty($redirection) && is_array($redirection)) {
									
									// Check result
									if (!$redirection['result']) {
										
										// No redirection
										$redirect_error = $redirection['reason'];
										
										// Check redirection code
										$redirect_status = $redirection['code'];
										if (!empty($redirection['code']) && isset($status_codes[$redirection['code']]))
											$redirect_status .= ' '.$status_codes[$redirection['code']];
										
									// Done
									} else {
										
										// Original link
										$link = $match[1];
										
										// Copy redirect URL
										$redirect_url = $redirection['value'];
										
										// Check redirection code
										$redirect_status = $redirection['code'];
										if (!empty($redirection['code']) && isset($status_codes[$redirection['code']]))
											$redirect_status .= ' '.$status_codes[$redirection['code']];
										
										// Compose new link
										$link_new = $match[2].$redirect_url.$match[4];
										
										// Check changes
										if ($link != $link_new) {
											
											// Default response
											$result = '<b>'.__('Changed redirection location', 'wplnst').'</b>';
											
											// Compose new chunk
											$chunk = $link_new.$match[5].$match[6];
											
											// Replace in content
											$content_check = $content;
											$content = str_replace($match[0], $chunk, $content);
											
											if ($content_check == $content) {
												$previous = true;
												$result = '<b style="color: grey;">'.__('Redirection changed previously', 'wplnst').'</b>';
											}
										}
									}
								}
							}
							
							// Update data
							$data_posts[] = array(
								'ID' 				=> $post->ID,
								'permalink'			=> get_permalink($post->ID),
								'text' 				=> esc_html($match[5]),
								'previous' 			=> $previous,
								'redirect_url' 		=> isset($redirect_url)?    $redirect_url    : '',
								'redirect_error' 	=> isset($redirect_error)?  $redirect_error  : '',
								'redirect_status'	=> isset($redirect_status)? $redirect_status : '',
								'result' 			=> $result,
							);
						}
					}
					
					// Check post update
					if (!empty($content) && $content != $content_before)
						$this->process_updates_add($post->ID, $content);
					
					// No RegExp matches
					if ($notfound_post && !$base_url_item_is_host)
						$data_posts[] = array(
							'ID' 				=> $post->ID,
							'permalink' 		=> get_permalink($post->ID),
							'notfound' 			=> true,
							'text' 				=> '',
							'previous' 			=> false,
							'redirect_url' 		=> '',
							'redirect_error' 	=> '',
							'redirect_status' 	=> '',
							'result' 			=> '<i>'.__('Database Match', 'wplnst').'</i>'
						);
				}
			}
			
			// Add URL data
			$data[] = array(
				'url' 		=> esc_html($url),
				'base_url' 	=> esc_html($base_url),
				'notfound' 	=> $notfound,
				'posts' 	=> $data_posts
			);
		}
		
		// Process updates
		$this->process_updates_check($save);
		
		// Results
		return $data;
	}



	/**
	 * Retrieve content from stored updates
	 */
	private function process_updates_get($object_id) {
		$object_id = (int) $object_id;
		return isset($this->updates[$object_id])? $this->updates[$object_id] : false;
	}



	/**
	 * Add content to the updates
	 */
	private function process_updates_add($object_id, $content) {
		$object_id = (int) $object_id;
		$this->updates[$object_id] = $content;
	}



	/**
	 * URL updates check wrapper
	 */
	protected function process_updates_check($save) {}



	/**
	 * Clear protocol and www.
	 */
	private function simplify_url($url) {
		
		// Trim URL
		$url = trim(trim(trim($url), '/'));
		
		// Sanitize replacements
		$replacements = array('http//', 'http/', 'http://', 'http:/', 'http::', 'http:');
		foreach ($replacements as $replace) {
			if (0 === stripos($url, 'http://'.$replace))
				$url = 'http://'.substr($url, strlen('http://'.$replace));
		}
		
		// URL parts
		$parts = @parse_url($url);
		if (!isset($parts['scheme']))
			$parts = @parse_url('http://'.$url);
		
		// Check host
		if (isset($parts['host'])) {
			
			// Sanitize
			$host = array();
			$items = explode('.', $parts['host']);
			foreach ($items as $item) {
				
				// Check empty
				$item = trim($item);
				if ('' === $item)
					continue;
				
				// Avoid 
				if (empty($host) && 'www' == strtolower($item))
					continue;
				
				// Add to host
				$host[] = $item;
			}
		}
		
		// Return sanitized URL
		return strtolower(trim(trim(rtrim((empty($host)? '' : implode('.', $host)).(isset($parts['path'])? trim($parts['path']) : '').(isset($parts['query'])? '?'.trim($parts['query']) : ''), '?')), '/'));
	}



	/**
	 * Check domain
	 */
	private function is_host_url($url) {
		
		// Attempt to parse URL
		$parts = @parse_url($url);
		
		// Check path
		$path  = isset($parts['path'])?  trim(trim(trim($parts['path']), '/'))  : false;
		
		// Check query
		$query = isset($parts['query'])? trim(trim(trim($parts['query']), '?')) : false;
		
		// Check both values
		return (empty($path) && empty($query));
	}



	/**
	 * Returns Location header value for redirects
	 */
	private function get_url_redirect($url, $fragment = '', $step = 0) {
		
		// Initialize
		static $urlo, $cached_urls = array();
		
		// Check dependencies
		if (!isset($urlo)) {
			
			// Load cURL wrapper library
			wplnst_require('core', 'curl');
			
			// Status and URL classes
			wplnst_require('core', 'status');
			wplnst_require('core', 'url');
			
			// New URL object
			$urlo = new WPLNST_Core_URL();
		}
		
		// New
		$step++;
		
		// Default
		$redirection = array(
			'code' 		=> 0,
			'result' 	=> false,
			'reason' 	=> '',
			'value' 	=> '',
			'fragment' 	=> '',
		);
		
		// Clean URL
		$url = trim($url);
		
		// Fix double slash protocol
		if ('//' == mb_substr($url, 0, 2))
			$url = 'http:'.$url;
		
		// Parse and check crawleable
		$urlinfo = $urlo->parse($url);
		if (!$urlo->is_crawleable($urlinfo)) {
			$redirection['reason'] = __('Malformed URL', 'wplnst');
			return $redirection;
		}
		
		// Check cached urlinfo
		if (isset($cached_urls[$urlinfo['url']]))
			return $cached_urls[$urlinfo['url']];
		
		// Prepare URL hash
		$hash = md5($urlinfo['url']);
		
		// Prepare POST fields
		$postfields = array(
			'url' 				=> $urlinfo['url'],
			'hash'				=> $hash,
			'url_id'			=> 1,
			'connect_timeout' 	=> wplnst_get_nsetting('connect_timeout'),
			'request_timeout' 	=> wplnst_get_nsetting('request_timeout'),
			'max_download'		=> wplnst_get_nsetting('max_download') * 1024,
			'debug'				=> wplnst_is_debug()? '1' : '0',
			'user_agent'		=> wplnst_get_tsetting('user_agent'),
			'nonce' 			=> WPLNST_Core_Nonce::create_nonce($hash),
		);
		
		// Request crawler API
		$response = WPLNST_Core_CURL::post(array(
			'CURLOPT_URL' 				=> plugins_url('core/requests/http.php', WPLNST_FILE),
			'CURLOPT_CONNECTTIMEOUT' 	=> $postfields['connect_timeout'],
			'CURLOPT_TIMEOUT' 			=> $postfields['connect_timeout'] + (2 * $postfields['request_timeout']),
			'CURLOPT_USERAGENT' 		=> wplnst_get_tsetting('user_agent'),
		), $postfields);
		
		// Check existing response
		if (empty($response) || !is_array($response)) {
			$redirection['reason'] = __('Empty request response', 'wplnst');
		
		// Check request error
		} elseif ($response['error']) {
			$redirection['reason'] = $response['reason'];
			
		// Check available data
		} elseif (empty($response['data'])) {
			$redirection['reason'] = __('Empty response data', 'wplnst');
		
		// JSON body
		} else {
			
			// Decode JSON
			$body = @json_decode($response['data']);
			
			// Check value
			if (empty($body) || !is_object($body)) {
				$redirection['reason'] = __('Malformed response data', 'wplnst');
				
			// Check status
			} elseif (empty($body->status) || 'ok' != $body->status) {
				$redirection['reason'] = __('Request error', 'wplnst');
				
			// Check data
			} elseif (empty($body->data) || !is_object($body->data)) {
				$redirection['reason'] = __('Missing response data', 'wplnst');
				
			// Done	
			} else {
				
				// Return status from data
				$status = new WPLNST_Core_Status((array) $body->data);
				
				// Check redirect
				if ('301' == $status->code) {
					
					// Check redirect URL
					if (empty($status->redirect_url)) {
						$redirection['reason'] = __('Redirect header but missing Location', 'wplnst');
					
					// Redirected
					} else {
						
						// Parse redirect URL
						$redirect_urlinfo = $urlo->parse($status->redirect_url, $urlinfo['url']);
						
						// Check valid URL
						if (!$urlo->is_crawleable($redirect_urlinfo)) {
							$redirection['reason'] = __('Redirect header and Location but bad URL', 'wplnst').': '.$status->redirect_url;
						
						// All seems ok
						} else {
							
							// Yes, a redirection
							$redirection['result'] = true;
							
							// Check next step
							if ($step - 1 < wplnst_get_nsetting('max_redirs')) {
								
								// Perform new request
								$redirection_new = $this->get_url_redirect($redirect_urlinfo['url'], $redirect_urlinfo['fragment'], $step);
								
								// Copy results
								$redirection['code']  = $redirection_new['code'];
								$redirection['value'] = $redirection_new['value'];
							
							// Current
							} else {
								
								// No more redirections
								$redirection['code']   = $status->code;
								$redirection['value']  = $urlo->unparse_url($redirect_urlinfo);
							}
						}
					}
				
				// No redirection
				} else {
					
					// Check result
					$redirection['result'] = ($step > 1);
					$redirection['reason'] = __('No 301 redirect header', 'wplnst');
					
					// Response data
					$redirection['code']   = ($status->curl_errno > 0)? 'Request error '.$status->curl_errno : $status->code;
					$redirection['value']  = $urlinfo['url'].(empty($fragment)? '' : '#'.ltrim($fragment, '#'));
				}
			}
		}
		
		// Cached results
		$cached_urls[$urlinfo['url']] = $redirection;
		
		// Done
		return $cached_urls[$urlinfo['url']];
	}



}