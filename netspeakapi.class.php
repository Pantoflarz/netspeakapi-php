<?php
/*
 *                         netspeakapi.class.php
 *                         ------------------                    
 *   created              : 29 July 2020
 *   last modified        : 01 August 2020
 *   version              : 0.1.0
 *   website              : https://net-speak.pl/api_client/test/api.php
 *   copyright            : (C) 2018 Adam Szczygiel
 *  
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
class netspeakapi{
	
	private $configuration = array('base_url' => "https://net-speak.pl/api_client/", 'api_key' => '', 'teamspeak_api_key' => '');

	function __construct(){
		
	}

	function setAPIKey($api_key){
		$this->configuration['api_key'] = $api_key;
	}
	
	function setTeamSpeakAPIKey($teamspeak_api_key){
		$this->configuration['teamspeak_api_key'] = $teamspeak_api_key;
	}
	
	function accountInfo(){
		return $this->decodeResponse($this->send("account_info"));
	}
	
	function accountIncomeCheck($searchingFor, $numberOfResults){
		
		if(!is_numeric($numberOfResults)){ return $this->returnErrorMessage("error", "numberOfResults must be a number."); }
		
		$data = $this->decodeResponse($this->send("income_check", array("income_data" => $searchingFor, "result_number" => $numberOfResults)));
		
		if($data->response->result == "success"){
			$data->response->data = unserialize(base64_decode($data->data));
		}
		
		return $data;
		
	}
	
	function accountServiceList(){
		$data = $this->decodeResponse($this->send("service_list"));
		
		if($data->response->result == "success"){
			$data->response->data = unserialize(base64_decode($data->response->service_array));
		}
		
		return $data;
	}
	
	//funkcje zaczynające nazwę na teamspeak... będą działać tylko i wyłącznie gdy klucz API pochodzi z serwera slotowego.
	function teamspeakServerInfo(){
		return $this->decodeResponse($this->send("server_info"));
	}
	
	function teamspeakServerEdit($newServerName){
		return $this->decodeResponse($this->send("serverEdit", array('server_name' => $newServerName)));
	}
	
	function teamspeakServerGroupAddClient($groupID, $clientUniqueIdentifier){
		
		if(!is_numeric($groupID)){ return $this->returnErrorMessage("error", "groupID must be a number."); }
		
		return $this->decodeResponse($this->send("serverGroupAddClient", array('group_id' => $groupID, 'uniqueid' => $clientUniqueIdentifier)));
		
	}
	
	function teamspeakServerGroupDeleteClient($groupID, $clientDatabaseID){
		
		if(!is_numeric($groupID)){ return $this->returnErrorMessage("error", "groupID must be a number."); }
		
		return $this->decodeResponse($this->send("serverGroupDeleteClient", array('group_id' => $groupID, 'cldbid' => $clientDatabaseID)));
		
	}
	
	function teamspeakChannelCreate($channelName, $channelType, $ownerUniqueID = null, $ownerAssignChannelGroup = null){
		
		if(empty($channelName)){ return $this->returnErrorMessage("error", "channelName must not be empty."); }
		
		if(!is_numeric($channelType) && ($channelType != 0 OR $channelType != 1)){ return $this->returnErrorMessage("error", "groupID must be a number."); }
		
		$array = [
			'channel_name' => $channelName,
			'channel_type' => $channelType
		];
		
		if($ownerUniqueID != null OR $ownerAssignChannelGroup != null){
			
			if($ownerUniqueID == null){ return $this->returnErrorMessage("error", "If ownerUniqueID and/or ownerAssignChannelGroup are provided, then ownerUniqueID must not be empty."); }
			if($ownerAssignChannelGroup == null){ return $this->returnErrorMessage("error", "If ownerUniqueID and/or ownerAssignChannelGroup are provided, then ownerAssignChannelGroup must not be empty."); }
			
			$array['uniqueid'] = $ownerUniqueID;
			$array['channel_group_id'] = $ownerAssignChannelGroup;
		}
		
		return $this->decodeResponse($this->send("channelCreate", $array));
		
	}
	
	function teamspeakSubChannelCreate($channelName, $parentCID, $channelType, $ownerUniqueID = null, $ownerAssignChannelGroup = null){
		
		if(empty($channelName)){ return $this->returnErrorMessage("error", "channelName must not be empty."); }
		
		if(!is_numeric($parentCID)){ return $this->returnErrorMessage("error", "parentCID must be a number."); }
		
		if(!is_numeric($channelType) && ($channelType != 0 OR $channelType != 1)){ return $this->returnErrorMessage("error", "groupID must be a number."); }
		
		$array = [
			'channel_name' => $channelName,
			'channel_type' => $channelType
		];
		
		if($ownerUniqueID != null OR $ownerAssignChannelGroup != null){
			
			if($ownerUniqueID == null){ return $this->returnErrorMessage("error", "If ownerUniqueID and/or ownerAssignChannelGroup are provided, then ownerUniqueID must not be empty."); }
			if($ownerAssignChannelGroup == null){ return $this->returnErrorMessage("error", "If ownerUniqueID and/or ownerAssignChannelGroup are provided, then ownerAssignChannelGroup must not be empty."); }
			
			$array['uniqueid'] = $ownerUniqueID;
			$array['channel_group_id'] = $ownerAssignChannelGroup;
		}
		
		return $this->decodeResponse($this->send("subchannelCreate", $array));
		
	}
	
	function teamspeakChannelList(){
		
		$data = $this->decodeResponse($this->send("channellist"));
		
		if($data->response->result == "success"){
			$data->response->data = unserialize(base64_decode($data->response->data));
		}
		
		return $data;
	}
	
	function teamspeakChannelDelete($cid){
		
		if(!is_numeric($cid)){ return $this->returnErrorMessage("error", "cid must be a number."); }
		
		return $this->decodeResponse($this->send("channelDelete", array('channel_id' => $cid)));
		
	}
	
	function teamspeakChannelEdit($cid, $channel_name){
		
		if(!is_numeric($cid)){ return $this->returnErrorMessage("error", "cid must be a number."); }
		if(empty($channel_name)){ return $this->returnErrorMessage("error", "channel_name must not be empty."); }
		
		return $this->decodeResponse($this->send("channelEdit", array('channel_id' => $cid, 'channel_name' => $channel_name)));
		
	}
	
	function teamspeakChannelDescEdit($cid, $channel_description){
		
		if(!is_numeric($cid)){ return $this->returnErrorMessage("error", "cid must be a number."); }
		if(empty($channel_description)){ return $this->returnErrorMessage("error", "channel_description must not be empty."); }
		
		return $this->decodeResponse($this->send("channelDescEdit", array('channel_id' => $cid, 'channel_description' => $channel_description)));
		
	}
	
	function teamspeakTimeBot($cid1, $cid2, $cid3, $cid4){
		if(!is_numeric($cid1)){ return $this->returnErrorMessage("error", "cid1 must be a number."); }
		if(!is_numeric($cid2)){ return $this->returnErrorMessage("error", "cid2 must be a number."); }
		if(!is_numeric($cid3)){ return $this->returnErrorMessage("error", "cid3 must be a number."); }
		if(!is_numeric($cid4)){ return $this->returnErrorMessage("error", "cid4 must be a number."); }
		
		return $this->decodeResponse($this->send("api_timebot", array('channel_id_1' => $cid1, 'channel_id_2' => $cid2, 'channel_id_3' => $cid3, 'channel_id_4' => $cid4)));
	}
	
	function teamspeakClientList($params = null){
		
		if($params != null){
		
			$data = $this->decodeResponse($this->send("clientlist", array('params' => $params)));
			
		}else{
			
			$data = $this->decodeResponse($this->send("clientlist"));
			
		}
		
		if($data->response->result == "success"){
			$data->response->data = unserialize(base64_decode($data->response->data));
		}
		
		return $data;
	}
	
	//mode	- 3: serwer, 2: kanał, 1: klient
	function teamspeakSendMessage($senderName, $mode = null, $target = null, $message){
		
		if(!(strlen($senderName) >= 3)){ return $this->returnErrorMessage("error", "senderName must consist of at least 3 characters."); }
		
		$array = [
			'newName' => $senderName,
			'text' => $message
		];
		
		if(!is_numeric($mode)){ return $this->returnErrorMessage("error", "mode must be a number - 1,2 or 3."); }
		
		$array['mode'] = $mode;
		
		if($mode == 1 or $mode == 2){ if($target == null){ return $this->returnErrorMessage("error", "target must be provided if using mode 1 or mode 2."); } }
		if($target != null){ if(!is_numeric($target)){ return $this->returnErrorMessage("error", "target must be a number."); }}
		
		$array['target'] = $target;
		
		return $this->decodeResponse($this->send("sendMessage", $array));
		
	}
	
	function teamspeakClientPoke($senderName, $clid, $message){
		
		if(!(strlen($senderName) >= 3)){ return $this->returnErrorMessage("error", "senderName must consist of at least 3 characters."); }
		if(!is_numeric($clid)){ return $this->returnErrorMessage("error", "clid must be a number specific to a client id or 0 to send the poke to everyone on the server."); }
		
		return $this->decodeResponse($this->send("clientPoke", array('newName' => $senderName, 'text' => $message, 'clid' => $clid)));
		
	}
	
	function teamspeakClientMove($moverName, $clid, $cid){
		
		if(!(strlen($moverName) >= 3)){ return $this->returnErrorMessage("error", "moverName must consist of at least 3 characters."); }
		if(!is_numeric($clid)){ return $this->returnErrorMessage("error", "clid must be a number specific to a client id or 0 to move everyone currently residing on the server."); }
		if(!is_numeric($cid)){ return $this->returnErrorMessage("error", "cid must be a number specific to a channel id."); }
		
		return $this->decodeResponse($this->send("clientMove", array('newName' => $moverName, 'clid' => $clid, 'channel_id' => $cid)));
		
	}
	
	function teamspeakClientKick($kickerName, $clid, $mode, $reason = ""){
		
		if(!(strlen($kickerName) >= 3)){ return $this->returnErrorMessage("error", "kickerName must consist of at least 3 characters."); }
		if(!is_numeric($clid)){ return $this->returnErrorMessage("error", "clid must be a number specific to a client id or 0 to move everyone currently residing on the server."); }
		if(!is_numeric($mode)){ return $this->returnErrorMessage("error", "mode must be a number - 0 for server kick, 1 for channel kick."); }
		if($mode != 0 AND $mode != 1){ return $this->returnErrorMessage("error", "mode must be either 0 or 1."); }
		
		return $this->decodeResponse($this->send("clientKick", array('newName' => $kickerName, 'clid' => $clid, 'kickMode' => $mode, 'reason' => $reason)));
		
	}
	
	function shopPaymentList(){
		
		$data = $this->decodeResponse($this->send("payment_list"));
		
		if($data->response->result == "success" && $data->response->payment_list_array != "no payments"){
			$data->response->payment_list_array = unserialize(base64_decode($data->response->payment_list_array));
		}
		
		return $data;
		
	}
	
	function shopGetPaymentStatus($payment_description){
		
		return $this->decodeResponse($this->send("status_payment", array('pay_description' => $payment_description)));
		
	}
	
	function shopCreatePayment($payment_amount, $payment_description){
		
		if(!is_numeric($payment_amount)){ return $this->returnErrorMessage("error", "payment_amount must be a number or floating point."); }
		
		$data = $this->decodeResponse($this->send("create_payment", array('pay_amount' => $payment_amount, 'pay_description' => $payment_description)));
		
		return $data;
		
	}
	
	function shopDeletePayment($payment_description){

		return $this->decodeResponse($this->send("payment_delete", array('pay_description' => $payment_description)));
		
	}
	
	function shopMadePayment($payment_description, $payment_type){
		
		return $this->decodeResponse($this->send("payment_made", array('pay_description' => $payment_description, 'payment_type' => $payment_type)));
		
	}
	
	function shopVerifyPaymentInformation($payment){
		if($payment['secret_code'] == $this->configuration['api_key']){
			return true;
		}else{
			return false;
		}
	}
	
	function send($command, $commandData = null){
		
		$ch = curl_init();

		$data = [
			'api_key' => $this->configuration['api_key'],
			'command' => $command
		];
		
		if(substr(debug_backtrace()[1]['function'], 0, 9) == "teamspeak"){
			$data['api_key'] = $this->configuration['teamspeak_api_key'];
		}
		
		if(isset($commandData) && $commandData != null){
			foreach($commandData as $key => $index){
				$data[$key] = $index;
			}
		}

		curl_setopt($ch, CURLOPT_URL, $this->configuration['base_url']) or die("error");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		//print_r($data);
		
		$curl_response = curl_exec($ch);
		curl_close($ch);

		return $curl_response;

	}
	
	function decodeResponse($apiResponse){
		
		return json_decode($apiResponse);
		
	}
	
	//ta funkcja spróbuje zwrócić konkretnie error lub success danego zapytania. Jeśli to o co jest proszona funkcja jest niedostępne w zapytaniu, funkcja zwraca false.
	//USE-CASE - np. mamy linijkę kodu od którego zależy reszta kodu 
	//- możemy sprawdzić czy checkResult("error", $zapytanie) == false zanim wyegzekwujemy resztę kodu, a możemy dać inny kod jeśli akcja się nie powiodła.
	function checkResult($expected, $haystack){
		if($expected == "error"){
			if($haystack->response->result == "error"){
				return true;
			}else{
				return false;
			}
		}
		if($expected == "success"){
			if($haystack->response->result == "success"){
				return true;
			}else{
				return false;
			}
		}
	}
	
	function getElement($element, $haystack){
		if($element == "error"){
			if($haystack->response->result == "error"){
				return $haystack->response->error_info;
			}else{
				return null;
			}
		}
		if($element == "data"){
			if($haystack->response->result == "success"){
				unset($haystack->response->result);
				return $haystack;
			}else{
				return null;
			}
		}
		if($element == "status"){
			if($haystack->response->result == "success"){
				return $haystack->response->status;
			}else{
				return null;
			}
		}
	}
	
	function returnErrorMessage($type, $data){
		
		return (object) [
			'response' => (object) [
				'result' => $type,
				'error_info' => $data
			]
		];

	}
}
?>