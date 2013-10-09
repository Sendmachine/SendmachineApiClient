<?php


/**
 * Description of SendmachineApiClient
 * 
 * Sendmachine Api Client 
 *
 * @author rscata
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
        if(!$username or !$password) return false;
        
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
        
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        if(!$filter or !$order_by) {
            throw new Exception('invalid filter or order_by!');
            return false;
        }
        
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
        
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        if(!$list_id) {
            throw new Exception('invalid list id!');
            return false;
        }
        
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
        
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        if(!$list_name or !$emails) {
            throw new Exception('invalid list name or emails');
            return false;
        }
        
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
        
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        if(!$list_id or !$emails or !$action) {
            throw new Exception('invalid list id or emails or action!');
            return false;
        }
        
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
        
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        //check if list id is set
        if(!$list_id) {
            throw new Exception('invalid list id!');
            return false;
        }
        
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
     * get sender list
     * @param string $status (active, pending, active+pending, all)
     * @param string $type (email, domain, all)
     * @param string $group (none, domain, flat)
     * @param int $limit
     * @param int $offset
     * @return boolean
     */
    public function get_sender_list($status = 'active', $type = 'email', $group = null, $limit = null, $offset = null) {
        
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        $status_allowed = array('active', 'pending', 'active+pending', 'all');
        $type_allowed = array('email', 'domain', 'all');
        $group_allowed = array('none', 'domain', 'flat');
        
        if(!in_array($status, $status_allowed) or !in_array($type, $type_allowed)) {
            throw new Exception('invalid status or type!');
            return false;
        }
        
        if(!is_null($group) and !in_array($group, $group_allowed)) {
            throw new Exception('invalid group!');
            return false;
        }
        
        $q = $this->api_host."/sender/list?status=$status&type=$type";
        if(!is_null($group)) $q .= "&group=$group";
        if(!is_null($limit)) $q .= "&limit=$limit";
        if(!is_null($offset)) $q .= "&offset=$offset";
        
        $process = curl_init($q);
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        
        $resp_check = $this->process_response($process);
        if($resp_check !== true) return $resp_check;
        
        curl_close($process);
        return json_decode($return);
    }
    
    /**
     * add a new sender
     * @param string $email
     * @return boolean
     */
    public function add_new_sender($email) {
        
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        if(!$email) {
            throw new Exception('invalid email');
            return false;
        }
        
        $data = array(
            'type' => 'email',
            'address' => $email
            );
        
        $data_string = json_encode($data);
        $process = curl_init($this->api_host."/sender/list");
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
     * Get details about the current active package of the user
     */
    public function get_account_package_details() {
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        $process = curl_init($this->api_host."/account/package");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        
        $resp_check = $this->process_response($process);
        if($resp_check !== true) return $resp_check;
        
        curl_close($process);
        return json_decode($return);
    }
    
    /**
     * The SMTP user and password are also used for API Auth. 
     */
    public function get_smtp_details() {
         //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        $process = curl_init($this->api_host."/account/smtp");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        
        $resp_check = $this->process_response($process);
        if($resp_check !== true) return $resp_check;
        
        curl_close($process);
        return json_decode($return);
    }
    
    /**
     * A new SMTP password will be generated.
     * @return boolean
     * @throws Exception
     */
    public function reset_smtp_password() {
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        
        $process = curl_init($this->api_host."/account/smtp");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, "POST");
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
     * get user details
     * @return boolean
     * @throws Exception
     */
    public function get_user_details() {
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        $process = curl_init($this->api_host."/account/user");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        
        $resp_check = $this->process_response($process);
        if($resp_check !== true) return $resp_check;
        
        curl_close($process);
        return json_decode($return);
    }
    
    /**
     * update user details
     * @param array $data (sex, first_name, last_name, country, phone_number, mobile_number)
     * @return boolean
     * @throws Exception
     */
    public function update_user_details($data) {
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        $sex_allowed('f', 'm');
        if(!$data['sex'] or !in_array($data['sex'], $sex_allowed)) {
            throw new Exception('invalid sex');
            return false;
        }
        
        if(!$data['first_name']) {
            throw new Exception('invalid first_name');
            return false;
        }
        
        if(!$data['last_name']) {
            throw new Exception('invalid last_name');
            return false;
        }
        
        if(!$data['country']) {
            throw new Exception('invalid country');
            return false;
        }
        
        if(!$data['phone_number']) {
            throw new Exception('invalid phone_number');
            return false;
        }
        
        if(!$data['mobile_number']) {
            throw new Exception('invalid mobile_number');
            return false;
        }
        
        $data = array(
            'sex' => $data['sex'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'country' => $data['country'],
            'phone_number' => $data['phone_number'],
            'mobile_number' => $data['mobile_number']
            );
        
        $data_string = json_encode($data);
        $process = curl_init($this->api_host."/account/user");
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
     * Get countries with their corresponding IDs.
     * @return boolean
     * @throws Exception
     */
    public function get_countries() {
        //check if username and password is set
        if(!$this->username and !$this->password) {
            throw new Exception('username or password is not set');
            return false;
        }
        
        $process = curl_init($this->api_host."/account/countries");
        curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
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
