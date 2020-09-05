<?php
/*
 *                         netspeakapi.class.php
 *                         ------------------                    
 *   created              : 29 July 2020
 *   last modified        : 24 August 2020
 *   version              : 1.0.0
 *   website              : https://net-speak.pl/api_client/test/api.php
 *   copyright            : (C) 2020 Adam Szczygiel
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

    //private runtime variables
	private $configuration = array('base_url' => "https://net-speak.pl/api_client/", 'api_key' => '', 'teamspeak_api_key' => '');

	function __construct(){

	}

    /**
     * setAPIKey
     *
     *	Sets a new API Key for the class instance.
     *  API Key is obtainable from http://www.net-speak.pl/loged/panel/clientarea/set.php?cmd=api
     *
     * <b>Output:</b>
     * <pre>
     *
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param		string	$api_key		api_key
     */
	function setAPIKey($api_key){
		$this->configuration['api_key'] = $api_key;
	}

    /**
     * setTeamSpeakAPIKey
     *
     *	Sets a new TeamSpeak API Key for the class instance.
     *  API Key is obtainable from http://www.net-speak.pl/loged/panel/clientarea/set.php?cmd=api_ts3
     *
     * <b>Output:</b>
     * <pre>
     *
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param		string	$teamspeak_api_key		api_key
     */
	function setTeamSpeakAPIKey($teamspeak_api_key){
		$this->configuration['teamspeak_api_key'] = $teamspeak_api_key;
	}

    /**
     * accountInfo
     *
     *	Grabs information about customer account.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [account_income] => 100.10
     *       [account_outcome] => 75.50
     *       [account_id] => 12345
     *       [account_income_url] => https://net-speak.pl/portfel_add.php?portfel_id=12345
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @return object accountInfo
     */
	function accountInfo(){
		return $this->decodeResponse($this->send("account_info"));
	}

    /**
     * accountIncomeCheck
     *
     *	Grabs a list of payments made into the customer account.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [data] => Array(
     *           [0] => Array(
     *               [pay_data] => 2020-04-30
     *               [hour] => 14:34
     *               [amount] => 118.22
     *               [description] => test@gmail.com (Opłaty PayPal 2.78 zł)
     *           )
     *
     *           [1] => Array(
     *               [pay_data] => 2019-10-24
     *               [hour] => 18:57
     *               [amount] => 58.62
     *               [description] => test@gmail.com (Opłaty PayPal 1.38 zł)
     *           )
     *       )
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param		string	$searchingFor		What we are looking for in the details of a given payee (e.g. filter by email address).
     * @param       integer $numberOfResults    The number of results to display.
     * @return object accountIncomeCheck
     */
	function accountIncomeCheck($searchingFor, $numberOfResults){

		if(!is_numeric($numberOfResults)){ return $this->returnErrorMessage("error", "numberOfResults must be a number."); }

		$data = $this->decodeResponse($this->send("income_check", array("income_data" => $searchingFor, "result_number" => $numberOfResults)));

		if($data->response->result == "success"){
			$data->response->data = unserialize(base64_decode($data->response->data));
		}

		return $data;

	}

    /**
     * accountServiceList
     *
     *	Grabs a list of services connected to user account.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [data] => Array(
     *           [0] => Array(
     *               [service_type] => TeamSpeak
     *               [service_id] => ts7-125
     *               [service_endtime] => 29-08-2020 14:41
     *               [service_status] => Online
     *           )
     *
     *           [1] => Array(
     *               [service_type] => VoiceVPS
     *               [service_id] => 3711
     *               [service_endtime] => 31-10-2020 12:56
     *               [service_status] => Online
     *           )
     *       )
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @return object accountServiceList
     */
	function accountServiceList(){
		$data = $this->decodeResponse($this->send("service_list"));

		if($data->response->result == "success"){
			$data->response->data = unserialize(base64_decode($data->response->service_array));
            unset($data->response->service_array);
		}

		return $data;
	}

    //funkcje zaczynające nazwę na teamspeak... będą działać tylko i wyłącznie gdy klucz API pochodzi z serwera slotowego.

    /**
     * teamspeakServerInfo
     *
     *	Grabs serverInfo for the server that owns provided API key.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [virtualserver_name] => TeamSpeak
     *       [virtualserver_clientsonline] => 100
     *       [virtualserver_queryclientsonline] => 0
     *       [virtualserver_maxclients] => 512
     *       [virtualserver_uptime] => 12543545
     *       [virtualserver_endtime] => 29-08-2020 14:41
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @return object teamspeakServerInfo
     */

	function teamspeakServerInfo(){
		return $this->decodeResponse($this->send("server_info"));
	}

    /**
     * teamspeakServerEdit
     *
     *	Edits the server name of the server that owns the provided API key.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param		string	$newServerName	The new name for the server.
     * @return object teamspeakServerEdit
     */
	function teamspeakServerEdit($newServerName){
		return $this->decodeResponse($this->send("serverEdit", array('server_name' => $newServerName)));
	}

    /**
     * teamspeakServerGroupAddClient
     *
     *	Adds a user to a group.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [cldbid] => 2
     *       [uniqueid] => uLgqMHJOzxI4TOgQrDfP6TEqtsk=
     *       [group_id] => 25
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param		int 	$groupID    The ID of the group the user should be added to.
     * @param       string  $clientUniqueIdentifier     The client_unique_identifier which is the identifier of the user we want to be added.
     * @return object teamspeakServerGroupAddClient
     */
	function teamspeakServerGroupAddClient($groupID, $clientUniqueIdentifier){

		if(!is_numeric($groupID)){ return $this->returnErrorMessage("error", "groupID must be a number."); }

		return $this->decodeResponse($this->send("serverGroupAddClient", array('group_id' => $groupID, 'uniqueid' => $clientUniqueIdentifier)));

	}

    /**
     * teamspeakServerGroupDeleteClient
     *
     *	Deletes a user from a group.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [cldbid] => 2
     *       [group_id] => 25
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param		int 	$groupID    The ID of the group the user should be removed from.
     * @param       int     $clientDatabaseID     The client_database_id which is the id of the user we want to be removed.
     * @return object teamspeakServerGroupDeleteClient
     */
	function teamspeakServerGroupDeleteClient($groupID, $clientDatabaseID){

		if(!is_numeric($groupID)){ return $this->returnErrorMessage("error", "groupID must be a number."); }

		return $this->decodeResponse($this->send("serverGroupDeleteClient", array('group_id' => $groupID, 'cldbid' => $clientDatabaseID)));

	}

    /**
     * teamspeakChannelCreate
     *
     *	Creates a new channel.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [channel_name] => Test
     *       [channel_id] => 230
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param		string      $channelName   The name of the new channel.
     * @param       int     $channelType    The type of channel - (0 - semi-permanent, 1 - permanent).
     * @param       string  $ownerUniqueID  The client_unique_identifier of the owner (if applicable).
     * @param       string  $ownerAssignChannelGroup  The id of the channel group to assign to ownerUniqueID (this must be sent, if ownerUniqueID is sent and vice versa).
     * @return object teamspeakChannelCreate
     */
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

    /**
     * teamspeakSubChannelCreate
     *
     *	Creates a new sub channel.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [channel_name] => Test Sub Channel
     *       [channel_id] => 231
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param		string      $channelName   The name of the new channel.
     * @param       int     $parentCID      The channel_id of the parent channel this channel should be created under.
     * @param       int     $channelType    The type of channel - (0 - semi-permanent, 1 - permanent).
     * @param       string  $ownerUniqueID  The client_unique_identifier of the owner (if applicable).
     * @param       string  $ownerAssignChannelGroup  The id of the channel group to assign to ownerUniqueID (this must be sent, if ownerUniqueID is sent and vice versa).
     * @return object teamspeakSubChannelCreate
     */
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

    /**
     * teamspeakChannelList
     *
     *	Grabs a list of channels on the server.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [data] => Array(
     *           [0] => Array(
     *               [channel_name] => Test
     *               [cid] => 230
     *           )
     *           [1] => Array(
     *               [channel_name] => Test Sub Channel
     *               [cid] => 231
     *           )
     *       )
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @return object teamspeakChannelList
     */
	function teamspeakChannelList(){

		$data = $this->decodeResponse($this->send("channellist"));

		if($data->response->result == "success"){
			$data->response->data = unserialize(base64_decode($data->response->data));
		}

		return $data;
	}

    /**
     * teamspeakChannelDelete
     *
     *	Deletes a channel on the server.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [channel_id] => 231
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param       int     $cid    The channel_id of the channel that should be deleted.
     * @return object teamspeakChannelDelete
     */
	function teamspeakChannelDelete($cid){

		if(!is_numeric($cid)){ return $this->returnErrorMessage("error", "cid must be a number."); }

		return $this->decodeResponse($this->send("channelDelete", array('channel_id' => $cid)));

	}

    /**
     * teamspeakChannelEdit
     *
     *	Edits a channel name.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [channel_id] => 230
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param       int     $cid    The channel_id of the channel that should be edited.
     * @param       string     $channel_name    The new channel name.
     * @return object teamspeakChannelEdit
     */
	function teamspeakChannelEdit($cid, $channel_name){

		if(!is_numeric($cid)){ return $this->returnErrorMessage("error", "cid must be a number."); }
		if(empty($channel_name)){ return $this->returnErrorMessage("error", "channel_name must not be empty."); }

		return $this->decodeResponse($this->send("channelEdit", array('channel_id' => $cid, 'channel_name' => $channel_name)));

	}

    /**
     * teamspeakChannelDescEdit
     *
     *	Edits a channel description.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [channel_id] => 230
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param       int     $cid    The channel_id of the channel that should be edited.
     * @param       string     $channel_description    The new channel description.
     * @return object teamspeakChannelDescEdit
     */
	function teamspeakChannelDescEdit($cid, $channel_description){

		if(!is_numeric($cid)){ return $this->returnErrorMessage("error", "cid must be a number."); }
		if(empty($channel_description)){ return $this->returnErrorMessage("error", "channel_description must not be empty."); }

		return $this->decodeResponse($this->send("channelDescEdit", array('channel_id' => $cid, 'channel_description' => $channel_description)));

	}

    /**
     * teamspeakChannelEdit
     *
     *	Sets the hour on 4 configured channels (in order, from top to bottom)
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param       int     $cid1    The channel_id of the top channel that should be edited.
     * @param       int     $cid2    The channel_id of the second channel that should be edited.
     * @param       int     $cid3    The channel_id of the third channel that should be edited.
     * @param       int     $cid4    The channel_id of the last channel that should be edited.
     * @return object teamspeakTimeBot
     */
	function teamspeakTimeBot($cid1, $cid2, $cid3, $cid4){
		if(!is_numeric($cid1)){ return $this->returnErrorMessage("error", "cid1 must be a number."); }
		if(!is_numeric($cid2)){ return $this->returnErrorMessage("error", "cid2 must be a number."); }
		if(!is_numeric($cid3)){ return $this->returnErrorMessage("error", "cid3 must be a number."); }
		if(!is_numeric($cid4)){ return $this->returnErrorMessage("error", "cid4 must be a number."); }

		return $this->decodeResponse($this->send("api_timebot", array('channel_id_1' => $cid1, 'channel_id_2' => $cid2, 'channel_id_3' => $cid3, 'channel_id_4' => $cid4)));
	}

    /**
     * teamspeakChannelList
     *
     *	Grabs a list of clients on the server.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [data] => Array(
     *           [0] => Array(
     *               [clid] => 25
     *               [cid] => 230
     *               [client_database_id] => 2
     *               [client_nickname] => Pantoflarz
     *               [client_type] => 0
     *               [client_unique_identifier] => uLgqMHJOzxI4TOgQrDfP6TEqtsk=
     *           )
     *       )
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param       int     $params    The additional parameters - -uid, -away, -voice, -times, -groups, -info, -icon.
     * @return object teamspeakClientList
     */
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

    /**
     * teamspeakSendMessage
     *
     *	Sends a message to a client, channel or server.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [status] => OK
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param       string     $senderName    Nickname of client sending the message.
     * @param       int     $mode    3 = send message to server, 2 = send message to channel, 1 = send message to client.
     * @param       int     $target    The target of the message (e.g. if mode = 3 then target is a server id, if mode = 2 then target is a channel_id and if mode = 1 then target is a clid..
     * @param       string     $message    The message to be sent.
     * @return object teamspeakSendMessage
     */
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

    /**
     * teamspeakClientPoke
     *
     *	Sends a poke to a client.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [status] => OK
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param       string     $senderName    Nickname of client sending the message.
     * @param       int     $clid    The clid of the target.
     * @param       string     $message    The message to be sent.
     * @return object teamspeakClientPoke
     */
	function teamspeakClientPoke($senderName, $clid, $message){

		if(!(strlen($senderName) >= 3)){ return $this->returnErrorMessage("error", "senderName must consist of at least 3 characters."); }
		if(!is_numeric($clid)){ return $this->returnErrorMessage("error", "clid must be a number specific to a client id or 0 to send the poke to everyone on the server."); }

		return $this->decodeResponse($this->send("clientPoke", array('newName' => $senderName, 'text' => $message, 'clid' => $clid)));

	}

    /**
     * teamspeakClientMove
     *
     *	Moves a client to a channel.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [status] => OK
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param       string     $moverName    Nickname of client doing the moving.
     * @param       int     $clid    The clid of the client to be moved.
     * @param       int     $cid    The channel to be moved to.
     * @return object teamspeakClientMove
     */
	function teamspeakClientMove($moverName, $clid, $cid){

		if(!(strlen($moverName) >= 3)){ return $this->returnErrorMessage("error", "moverName must consist of at least 3 characters."); }
		if(!is_numeric($clid)){ return $this->returnErrorMessage("error", "clid must be a number specific to a client id or 0 to move everyone currently residing on the server."); }
		if(!is_numeric($cid)){ return $this->returnErrorMessage("error", "cid must be a number specific to a channel id."); }

		return $this->decodeResponse($this->send("clientMove", array('newName' => $moverName, 'clid' => $clid, 'channel_id' => $cid)));

	}

    /**
     * teamspeakClientKick
     *
     *	Kicks a client from a channel or server.
     *
     * <b>Output:</b>
     * <pre>
     * stdClass Object(
     *   [response] => stdClass Object(
     *       [result] => success
     *       [status] => OK
     *   )
     * )
     * </pre>
     *
     * @author     Adam Szczygiel
     * @param       string     $kickerName    Nickname of client doing the kicking.
     * @param       int     $clid    The clid of the client to be kicked.
     * @param       int     $mode   The mode - 0 = kick from server, 1 = kick from channel.
     * @param       string  $reason An optional parameter specifying the reason for the kick.
     * @return object teamspeakClientKick
     */
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

	//internal functions which will not be commented or explained
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
		if(PHP_VERSION_ID >= 70100) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
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
