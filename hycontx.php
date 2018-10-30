<?php
/**
 * @file hycontx.php
 * @date 2018-09-14
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Hycon Transaction Interface
 * @framework https://github.com/gnh1201/resaonableframework
 */

// load webpagetool helper
loadHelper("webpagetool");

// set and check initial variables
$data = array();
$hycon_base_url = "http://localhost:2442";
$action = get_requested_value("action");
if(empty($action)) {
	set_error("do not empty action");
	show_errors();
}

// get response
$response = null;
switch($action) {
	case "createWallet":
		// get private key
		$response = get_web_json($hycon_base_url . "/api/v1/wallet/", "post", array(
			"language" => "default" // required
		));

		$data['mnemonic'] = get_property_value("mnemonic", $response);
		$data['privateKey'] = get_property_value("privateKey", $response);
		$data['address'] = get_property_value("address", $response);
		break;

	case "getBalance":
		$params = array(
			"address" => get_requested_value("address")
		);

		if(empty($params['address'])) {
			set_error("do not empty address");
			show_errors();
		}

		$response = get_web_json($hycon_base_url . "/api/v1/wallet/" . $params['address'] . "/balance", "get");
		$data['address'] = $params['address'];
		$data['balance'] = get_property_value("balance", $response);
		$data['params'] = $params;
		break;

	case "createTx":
		$params = array(
			"signature" => get_requested_value("signature"),
			"from" => get_requested_value("from"),
			"to" => get_requested_value("to"),
			"amount" => get_requested_value("amount"),
			"fee" => get_requested_value("fee")
		);

		$response = get_web_json($hycon_base_url . "/api/v1/signedtx/", "jsondata", array(
			"privateKey" => $params['signature'],
			"from" => $params['from'],
			"to" => $params['to'],
			"amount" => $params['amount'],
			"fee" => $params['fee']
		));

		$data['response'] = $response;
		$data['params'] = $params;
		break;

	case "getTx":
		$params = array(
			"hash" => get_requested_value("hash")
		);

		if(!empty($params['hash'])) {
			set_error("do not empty hash");
			show_errors();
		}

		$response = get_web_json($hycon_base_url . "/api/v1/tx/" . $params['hash'], "get");

		$data['hash'] = get_property_value("hash", $response);
		$data['amount'] = get_property_value("amount", $response);
		$data['fee'] = get_property_value("fee", $response);
		$data['blockHash'] = get_property_value("blockHash", $response);
		$data['receiveTime'] = get_property_value("receiveTime", $response);
		$data['estimated'] = get_property_value("estimated", $response);
		$data['confirmation'] = get_property_value("confirmation", $response);
		$data['params'] = $params;
		break;
}

// output response
header("Access-Control-Allow-Origin: *"); // allow testnet to localhost
header("Content-Type: application/json");
echo json_encode($data);
