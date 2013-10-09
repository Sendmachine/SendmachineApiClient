<?php


/**
 * Description of SendmachineApiClient
 * 
 * Sendmachine Api Client 
 *
 * @author cata
 */
class SendmachineApiClient {
    
    /**
     * api username
     * @var string 
     */
    private $username;
    
    /**
     * api password
     * @var string 
     */
    private $password;
    
    /**
     * api host
     * @var string
     */
    private $api_host = 'https://api.sendmachine.com';
    
    /**
     * connect to api
     * @param string $username
     * @param string $password
     */
    public function connect_api($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * get all contact lists
     * @param sting $filter
     * @param string $order_by
     * @return type
     */
    public function get_all_contact_lists($filter = 'all', $order_by = 'email') {
        
        $process = curl_init($this->api_host."/contact/list?filter=$filter&orderby=$order_by");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        curl_close($process);
        return json_decode($return);
    }
    
    /**
     * Get a single contact list
     * @param int $list_id list id
     * @param string $filter
     * @param string $order_by
     * @return boolean
     */
    public function get_a_single_contact_list($list_id, $filter = 'all', $order_by = 'email') {
        if(!$list_id) return false;
        
        $process = curl_init($this->api_host."/contact/list/$list_id?filter=$filter&orderby=$order_by");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        
        $resp_check = $this->process_response($process);
        if($resp_check !== true) return $resp_check;
        
        curl_close($process);
        return json_decode($return);
    }
    
    /**
     * Create a new contact list 
     * @param string $list_name
     * @param array $emails
     * @return type
     */
    public function create_new_contact_list($list_name, $emails) {
        $data = array(
            'name' => $list_name,
            'contacts' => $emails
            );
        
        $data_string = json_encode($data);
        $process = curl_init($this->api_host."/contact/list");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($process, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        $return = curl_exec($process);
        
        $resp_check = $this->process_response($process);
        if($resp_check !== true) return $resp_check;
        
        curl_close($process);
        return json_decode($return);
    }
    
    /**
     * add contacts list to a existing list
     * @param int $list_id
     * @param array $emails
     * @param string $action
     * @param string $list_name optionally
     * @return boolean
     */
    public function edit_contacts_list($list_id, $emails, $action = 'subscribe', $list_name = null) {
        if(!$list_id or !$emails or !$action) return false;
        
        //check allowd action
        $action_allow = array('subscribe', 'resubscribe', 'unsubscribe');
        if(!in_array($action, $action_allow)) $action = 'subscribe';
        
        $data = array(
            'contacts' => $emails,
            'action' => $action
            );
        
        //merge data, if edit list name
        if($list_name !== NULL)
            $data = array_merge ($data, array('name' => $list_name));
        
        $data_string = json_encode($data);
        $process = curl_init($this->api_host."/contact/list/$list_id");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($process, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        $return = curl_exec($process);
        curl_close($process);
        return json_decode($return);
    }
    
    
    /**
     * Delete a contact list
     * @param int $list_id
     * @return boolean
     */
    public function delete_contact_list($list_id) {
        if(!$list_id) return false;
        
        $process = curl_init($this->api_host."/contact/list/$list_id");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json')
        );
        $return = curl_exec($process);
        
        $resp_check = $this->process_response($process);
        if($resp_check !== true) return $resp_check;
        
        curl_close($process);
        return json_decode($return);
        
    }
    
    
    /**
     * procces resoponse info
     * @param object $respone
     * @return boolean
     */
    private function process_response($process) {
        $process_info = curl_getinfo($process);
        if($process_info['http_code'] !== 200 and $process_info['http_code'] !== 201) 
            return array('code' => $process_info['http_code']);
        return true;
    }
    
}

?>
