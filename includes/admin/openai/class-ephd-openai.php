<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * OpenAI utility class
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPHD_OpenAI {

	/**
	 * API URL base: the base URL part for API end-points.
	 */
	const API_V1_URL = 'https://api.openai.com/v1';

	const DEFAULT_MODEL = 'gpt-3.5-turbo';

	/**
	 * API URL end-point: Creates a completion for the provided prompt and parameters
	 * For more details see the documentation at https://platform.openai.com/docs/api-reference/completions
	 */
	const COMPLETIONS_URL = '/chat/completions';

	/**
	 * API URL end-point: Creates a new edit for the provided input, instruction, and parameters.
	 * For more details see the documentation at https://platform.openai.com/docs/api-reference/edits
	 */
	const EDITS_URL = '/edits';


	/**
	 * Number of tokens used for latest API request
	 *
	 * @var string
	 */
	public $tokens_used = 0;

	// TODO: get list of available models GET to https://api.openai.com/v1/models
	// TODO: Allow user to save an available model in settings
	// Info: https://beta.openai.com/docs/api-reference/models

	/**
	 * Create completion
	 * More Info: https://beta.openai.com/docs/api-reference/completions/create
	 *
	 * @param $model
	 * @param $prompt
	 * @return String|WP_Error
	 */
	public function complete( $model, $prompt ) {
		$messages = array(
			array(
				'role'    => 'system',
				'content' => 'You are a helpful assistant',
			),
			array(
				'role'    => 'user',
				'content' => trim( $prompt ),
			)

		);

		$api_result = $this->make_api_request( self::API_V1_URL . self::COMPLETIONS_URL, array(
				'model'      => trim( $model ),
				'messages'   => $messages,
			// 'temperature'   => $temperature,
				'n'          => 1,
		) );

		if ( is_wp_error( $api_result ) ) {
			return $api_result;
		}

		// save tokens usage.
		$this->tokens_used = $api_result['usage']['total_tokens'];

		$choices_text = ! empty( $api_result['choices'][0]['message']['content'] ) ? $api_result['choices'][0]['message']['content'] : '';
		return $choices_text;

		// return count( $choices ) > 0 ? trim( $choices[0]['text'] ) : '';
	}

	/**
	 * Edit input text
	 *
	 * @param $model
	 * @param $prompt
	 * @param $input
	 * @param $temperature
	 * @return String|WP_Error
	 */
	public function edit( $model, $prompt, $input, $temperature ) {

		$api_result = $this->make_api_request( self::API_V1_URL . self::EDITS_URL, array(
				'model'       => trim( $model ),
				'instruction' => trim( $prompt ),
				'input'       => trim( $input ),
				'temperature' => $temperature,
				'n'           => 1,
		) );

		if ( is_wp_error( $api_result ) ) {
			return $api_result;
		}

		// save tokens usage
		$this->tokens_used = $api_result['usage']['total_tokens'];

		$choices = is_array( $api_result['choices'] ) ? $api_result['choices'] : [];

		return count( $choices ) > 0 ? trim( $choices[0]['text'] ) : '';
	}

	/**
	 * Make API request
	 *
	 * @param string $url api url.
	 * @param array  $args args for api.
	 * @param array  $additional_headers any additional headers.
	 * @param string $type whether POST or GET.
	 * @return array|WP_Error
	 */
	public static function make_api_request( $url, $args, $additional_headers = array(), $type = 'POST' ) {

		// is $type valid?
		if ( ! in_array( $type, array( 'POST', 'GET', 'DELETE' ) ) ) {
			EPHD_Logging::add_log( 'Invalid type for OpenAI API request.' );
			return new WP_Error( 'invalid_type', 'Invalid type for OpenAI API request.' );
		}

		// validate API key
		$api_key = self::get_openai_api_key();
		 if ( empty( $api_key ) || ! is_string( $api_key ) ) {
			return new WP_Error( 'missing_api_key', sprintf(
				/* translators: %1$s and  %2$s will get replaced by the url for settings html*/
				esc_html__( 'Please enter your OpenAI API key in the %1$s plugin Advanced Settings %2$s', 'help-dialog' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=ephd-help-dialog-advanced-config#settings' ) ) . '" target="_blank">',
					'</a>'
			) );
		}

		$headers = array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . sanitize_text_field( $api_key ),
		);
		$headers = array_merge( $headers, $additional_headers );

		$request_args = array(
			'method'  => $type,
			'headers' => $headers,
			'timeout' => 180, // some of OpenAI API requests can take up to a few minutes.
		);

		$args = self::ai_sanitize_args( $args );

		if ( $type === 'POST' ) {
			$request_args['body'] = wp_json_encode( $args );
		}

		if ( $type === 'GET' || $type === 'DELETE' ) {
			$url = add_query_arg( $args, $url );
		}

		// make API request to OpenAI
		$http_result = wp_remote_request( $url, $request_args );
		if ( is_wp_error( $http_result ) ) {
			return $http_result;
		}

		// retrieve API response
		$api_result = json_decode( $http_result['body'], true );

		$response_code = $http_result['response']['code'];
		if ( $response_code != 200 ) {
			return self::get_wp_error( $http_result, $api_result );
		}

		// validate response body
		if ( empty( $http_result['body'] ) ) {
			EPHD_Logging::add_log( 'Empty body on OpenAI API response. HTTP response code: ' . $response_code );
			return new WP_Error( 'empty_body', 'Empty body on OpenAI API response.' );
		}

		// validate decoded JSON
		if ( empty( $api_result ) ) {
			EPHD_Logging::add_log( 'Unable to decode JSON from OpenAI API response. HTTP response code: ' . $response_code );
			return new WP_Error( 'json_decode_error', 'Unable to decode JSON from OpenAI API response.' );
		}

		return $api_result;
	}

	/**
	 * Validate HTTP response for API request - for detailed description about each response code look to https://beta.openai.com/docs/guides/error-codes/api-errors
	 *
	 * @param $http_result
	 * @param $api_result
	 * @return WP_Error
	 */
	private static function get_wp_error( $http_result, $api_result ) {

		$response_code = $http_result['response']['code'];

		// retrieve error details if possible
		$is_error_details = ! empty( $api_result['error'] ) && is_array( $api_result['error'] );
		$error_code    = $is_error_details && isset( $api_result['error']['code'] ) ? $api_result['error']['code'] : 'json_decode_error';
		$error_type    = $is_error_details && isset( $api_result['error']['type'] ) ? $api_result['error']['type'] : 'Unable to read error details for OpenAI API response.';
		$error_message = $is_error_details && isset( $api_result['error']['message'] ) ? $api_result['error']['message'] : 'OpenAI API request failed.';

		// forbidden
		if ( $response_code == 403 ) {

			// country, region, or territory not supported
			if ( $error_code == 'unsupported_country_region_territory' ) {
				return new WP_Error(
					'unsupported_country_region_territory',
					sprintf(
					/* translators: %1$s and %2$s will get replaced by the url for API service documentation page */
						esc_html__( 'You are accessing the API from an unsupported country, region, or territory. Please see %1$s this page %2$s for more information.', 'help-dialog' ),
						'<a href="https://platform.openai.com/docs/supported-countries" target="_blank">',
						'</a>',
					),
					array(
						'response_code' => $response_code,
						'error_code'    => 'unsupported_country_region_territory',
					)
				);
			}

			return new WP_Error( $error_type, 'OpenAI API request failed. Error type: ' . $error_type . '. API error message: ' . $error_message,
				array(
					'response_code' => $response_code,
					'error_code'    => $error_code,
				)
			);
		}

		// only write to logs the error details were failed to retrieve - continue with default then
		if ( ! $is_error_details ) {
			EPHD_Logging::add_log( 'Unable to read error details for OpenAI API response. HTTP response code: ' . $response_code );
		}

		// authentication
		if ( $response_code == 401 ) {

			if ( $error_code === 'invalid_api_key' ) {
				return new WP_Error(
					$error_type,
					sprintf(
					/* translators: %1$s, %2$s, %3$s and %4$s will get replaced by the url for settings html*/
						esc_html__( 'Incorrect API key provided:You can find your API key  %1$s here %2$s. Please enter your OpenAI API key in the %3$s plugin Advanced Settings %4$s', 'help-dialog' ),
						'<a href="https://platform.openai.com/account/api-keys" target="_blank">',
						'</a>',
						'<a href="' . esc_url( admin_url( 'admin.php?page=ephd-help-dialog-advanced-config#settings' ) ) . '" target="_blank">',
						'</a>'
					),
					array(
						'response_code' => $response_code,
						'error_code'    => $error_code,
					)
				);
			}

			return new WP_Error( $error_type, 'OpenAI API request failed. Error type: ' . $error_type . '. API error message: ' . $error_message,
				array(
					'response_code' => $response_code,
					'error_code'    => $error_code,
				)
			);
		}

		// handle other non-200 errors
		EPHD_Logging::add_log( 'OpenAI API request failed. HTTP response code: ' . $response_code . '. API error message: ' . $error_message );

		return new WP_Error( $error_type, 'OpenAI API request failed. Error type: ' . $error_type . '. API error message: ' . $error_message,
			array(
				'response_code' => $response_code,
				'error_code'    => $error_code,
			)
		);
	}

	private static function ai_sanitize_args( $args = [] ) {
		if ( empty( $args ) ) {
			return $args;
		}

		foreach ( $args as $key => $arg ) {

			if ( is_array( $arg ) ) {
				$args[$key] = self::ai_sanitize_args( $arg );
				continue;
			}

			// use strict comparison instead of 'switch' statement to avoid wrong sanitization method
			if ( $key === 'n' || $key === 'limit' || $key === 'max_tokens' ) {
				$args[$key] = absint( $arg );
			} else if ( $key === 'temperature' || $key === 'top_p' ) {
				$args[$key] = floatval( $arg );
			} else if ( $key === 'content' ) {
				$args[$key] = sanitize_textarea_field( $arg );
			} else {
				$args[$key] = sanitize_text_field( $arg );
			}
		}

		return $args;
	}

	public static function get_openai_api_key() {

		$old_api_key = ephd_get_instance()->global_config_obj->get_value( 'openai_api_key' );
		$new_api_key = EPHD_Utilities::get_wp_option( 'ephd_openai_key', '', false, true );

		if ( ! is_wp_error( $new_api_key ) && ! empty( $new_api_key ) ) {
			$api_key = EPHD_Utilities::decrypt_data( $new_api_key );
			$api_key = $api_key ?: '';

		} else if ( ! is_wp_error( $old_api_key ) && ! empty( $old_api_key ) ) {
			$api_key = $old_api_key;
			$result = self::save_openai_api_key( $api_key );

		} else {
			$api_key = '';
		}

		return $api_key;
	}

	public static function save_openai_api_key( $openai_api_key ) {

		$api_key = EPHD_Utilities::encrypt_data( $openai_api_key );
		$result = EPHD_Utilities::save_wp_option( 'ephd_openai_key', $api_key );

		return $result;
	}
}
