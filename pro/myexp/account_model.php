<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------|
  | Author: Provab Technosoft Pvt Ltd.									   |
  |--------------------------------------------------------------------------|
  | Developer: Naresh Kamireddy											   |
  | Started Date: 2014-08-19T18:00:00										   |
  | Ended Date:  										   					   |
  |--------------------------------------------------------------------------|
 */

class Account_Model extends CI_Model {

    public function isRegistered($email) {
        $this->db->where('email', $email);
        return $this->db->get('b2c');
    }

    public function get_unixtimestamp($DateTime) {
        //Exploding T from arrival time  
        list($date, $time) = explode('T', $DateTime);
        $DateTime = $date . " " . $time; //Exploding T and adding space
        $timestamp = strtotime($DateTime);
        return $timestamp;
    }

    public function createUsers($postData) {
        return $this->db->insert('b2c', $postData);
    }

    public function isValidUser($email, $password) {
        $this->db->where('email', $email);
        $this->db->where('password', $password);
        return $this->db->get('b2c');
    }

    public function getUserInfo($b2c_id) {
        $this->db->where('user_id', $b2c_id);
        return $this->db->get('b2c');
    }

    public function getUserInfoEmail($email) {
        $this->db->where('email', $email);
        return $this->db->get('b2c');
    }

    public function getUserInfoEmailB2b($email) {
        $this->db->where('email_id', $email);
        return $this->db->get('b2b');
    }

    public function updatePwdResetLink($b2c_id, $key, $secret) {
        $update = array(
            'key' => $key,
            'secret' => $secret
        );
        $this->db->where('user_id', $b2c_id);
        return $this->db->update('b2c', $update);
    }

    public function updatePwdResetLinkB2b($user_id, $key, $secret) {
        $update = array(
            'key' => $key,
            'secret' => $secret
        );
        $this->db->where('agent_id', $user_id);
        return $this->db->update('b2b', $update);
    }

    public function isvalidSecrect($key, $secret) {  //this method is valid for b2c only. the b2b sign up process will ensure the genuinity of the email.
        $this->db->where('key', $key);
        $this->db->where('secret', $secret);
        return $this->db->get('b2c');
    }

    public function update_b2c($update, $email) {
        $this->db->where('email', $email);
        $this->db->update('b2c', $update);
    }

    public function update_b2b($update, $email) {
        $this->db->where('email_id', $email);
        $this->db->update('b2b', $update);
    }

    public function update_b2c_user($update, $b2c_id) {
        $this->db->where('user_id', $b2c_id);
        if ($this->db->update('b2c', $update)) {
            return true;
        }
    }

    public function update_b2b_user($update, $b2b_id) {
        $this->db->where('agent_id', $b2b_id);
        if ($this->db->update('b2b', $update)) {
            return true;
        }
    }

    public function getUserEmergency($user_type, $b2c_id) {
        $this->db->where('user_type', $user_type);
        $this->db->where('user_id', $b2c_id);
        return $this->db->get('emergency_contact');
    }

    public function GetUserData($user_type, $user_id) {

        if ($user_type == '3') {
            $this->db->where('user_id', $user_id);
            return $this->db->get('b2c');
        } else if ($user_type == '2') {
            $this->db->where('agent_id', $user_id);
            return $this->db->get('b2b');
        }
    }

    public function get_account_statment($b2b_id) {
        $this->db->where('user_id', $b2b_id);
        $this->db->order_by('statdate', 'DESC');
        return $this->db->get('account_statment');
    }

    public function update_emergency_contact($update, $user_type, $b2c_id) {
        $this->db->where('user_type', $user_type);
        $this->db->where('user_id', $b2c_id);
        $this->db->update('emergency_contact', $update);
    }

    public function createEmergency($emergency) {
        return $this->db->insert('emergency_contact', $emergency);
    }

    public function validatePassword($user_id, $opassword) {
        $this->db->where('user_id', $user_id);
        $this->db->where('password', $opassword);
        return $this->db->get('b2c');
    }

    public function validatePasswordB2b($user_id, $opassword) {
        $this->db->where('agent_id', $user_id);
        $this->db->where('password', $opassword);
        return $this->db->get('b2b');
    }

    public function update_b2c_user_listing_property_access($data) {
        if ($data['access_listings'] == 'on') {
            $this->db->where('user_id', $this->session->userdata('b2c_id'));
            $this->db->update('b2c', array('property_owner_request' => 1));
        }
    }

    public function update_b2b_user_listing_property_access($data) {
        if ($data['access_listings'] == 'on') {
            $this->db->where('agent_id', $this->session->userdata('b2b_id'));
            $this->db->update('b2b', array('property_owner_request' => 1));
        }
    }

    public function getTimeZones() {
        return $this->db->get('timezones');
    }

    public function getLanguages() {
        return $this->db->get('language');
    }

    public function getLanguageById($code) {
        $this->db->where('code', $code);
        return $this->db->get('language');
    }

    //Public Profile
    public function getPublicProfile($user_id, $user_type) {
        if ($user_type == '3') {
            $this->db->where('user_id', $user_id);
            return $this->db->get('b2c');
        } else if ($user_type == '2') {
            $this->db->where('agent_id', $user_id);
            return $this->db->get('b2b');
        }
    }

    public function checkTwoStepVerification($user_id, $user_type) {
        $where = "user_type = '" . $user_type . "' AND user_id = " . $user_id . " AND two_step_verification = '1'";
        $this->db->where($where);
        $query_output = $this->db->get('user_verifications');

        if ($query_output->num_rows() > 0) {
            return $query_output->row();
        } else {
            return false;
        }
    }

    public function checkPswrdAvail($user_id) {
        $this->db->select('password');
        $this->db->from('b2c');
        $this->db->where('user_id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function B2b_checkPswrdAvail($user_id) {
        $this->db->select('password');
        $this->db->from('b2b');
        $this->db->where('agent_id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    //Delete user account and other data for b2c {
    public function deleteUserAccount($user_id) {

        $this->db->where('user_id', $user_id);
        $this->db->delete('b2c');

        return true;
    }

    public function deleteb2cverification($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('user_verifications');

        return true;
    }

    public function deleteWishlist($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('wishlist');

        return true;
    }

    public function deleteWishlist_type($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('wishlist_type');

        return true;
    }

    public function deleteSMSalertType($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('sms_alert_enabled');

        return true;
    }

    public function getSupportTicketNumber($user_id, $user_type) {
        $this->db->select('support_ticket_id');
        $this->db->from('support_ticket');
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);

        return $this->db->get();
    }

    public function deleteSupportTicketHistory($support_id) {
        $this->db->where('support_ticket_id', $support_id);
        $this->db->delete('support_ticket_history');
    }

    public function deleteSupportTicket($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('support_ticket');
    }

    public function deleteMessagesInit($user_id, $user_type) {
        $this->db->where('init_user_id', $user_id);
        $this->db->where('init_user_type', $user_type);
        $this->db->delete('user_messages');
    }

    public function deleteMessagesRece($user_id, $user_type) {
        $this->db->where('init_receiver_id', $user_id);
        $this->db->where('init_receiver_type', $user_type);
        $this->db->delete('user_messages');
    }

    public function deleteReference($user_id, $user_type) {
        $this->db->where('b2c_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('references');
    }

    public function deleteReviewGuestBy($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('reviews_guest');
    }

    public function deleteReviewGuestTo($user_id, $user_type) {
        $this->db->where('review_to', $user_id);
        $this->db->where('review_user_type', $user_type);
        $this->db->delete('reviews_guest');
    }

    public function deleteReviewGuestDataTo($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('reviews_guest_data');
    }

    public function deleteReviewGuestDataHost($user_id, $user_type) {
        $this->db->where('host_id', $user_id);
        $this->db->where('host_type', $user_type);
        $this->db->delete('reviews_guest_data');
    }

    public function deleteReviewHostTo($user_id, $user_type) {
        $this->db->where('host_id', $user_id);
        $this->db->where('host_type', $user_type);
        $this->db->delete('reviews_host');
    }

    public function deleteReviewHostBy($user_id, $user_type) {
        $this->db->where('review_to', $user_id);
        $this->db->where('review_user_type', $user_type);
        $this->db->delete('reviews_host');
    }

    public function deleteReviewHostDataTo($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('reviews_host_data');
    }

    public function deleteReviewHostDataBy($user_id, $user_type) {
        $this->db->where('host_id', $user_id);
        $this->db->where('host_type', $user_type);
        $this->db->delete('reviews_host_data');
    }

    public function deleteReviewUserTo($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('reviews_user');
    }

    public function deleteReviewUserBy($user_id, $user_type) {
        $this->db->where('host_id', $user_id);
        $this->db->where('host_type', $user_type);
        $this->db->delete('reviews_user');
    }

    public function deleteReviewUserDataTo($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('reviews_user_data');
    }

    public function deleteReviewUserDataBy($user_id, $user_type) {
        $this->db->where('host_id', $user_id);
        $this->db->where('host_type', $user_type);
        $this->db->delete('reviews_user_data');
    }

    //End of user account deletion block }
    //Delete user account and other data for B2B {

    public function b2b_deleteUserAccount($user_id) {
        $this->db->where('agent_id', $user_id);
        $this->db->delete('b2b');

        return true;
    }

    public function b2b_deleteb2cverification($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('user_verifications');

        return true;
    }

    public function b2b_deleteWishlist($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('wishlist');

        return true;
    }

    public function b2b_deleteWishlist_type($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('wishlist_type');

        return true;
    }

    public function b2b_deleteSMSalertType($user_id, $user_type) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete('sms_alert_enabled');

        return true;
    }

    public function deleteB2bAccountInfo($user_id) {
        $this->db->where('agent_id', $user_id);
        $this->db->delete('b2b_acc_info');

        return true;
    }

    public function deleteB2bDeposit($user_id) {
        $this->db->where('agent_id', $user_id);
        $this->db->delete('b2b_deposit');

        return true;
    }

    public function deleteB2bTopCities($user_id) {
        $this->db->where('agent_id', $user_id);
        $this->db->delete('b2b_top_cities');

        return true;
    }

    //End of delete user account and other data for B2B }



    public function addSubscriberEmail($data) {
        $insertData = $this->db->insert('newsletter_subscriber', $data);

        if ($insertData) {
            return true;
        } else {
            return false;
        }
    }

    public function addB2cNewsletterSub($user_id) {
        $data = array('newsletter_subscribe' => 1);
        $this->db->where('user_id', $user_id);

        $this->db->update('b2c', $data);
        return true;
    }

    public function addB2bNewsletterSub($user_id) {
        $data = array('newsletter_subscribe' => 1);
        $this->db->where('agent_id', $user_id);

        $this->db->update('b2b', $data);
        return true;
    }

    public function checkSubEmailB2c($current_b2c_session, $subscriberEmail) {
        $this->db->select('email');
        $this->db->from('b2c');
        $this->db->where('email', $subscriberEmail);
        $this->db->where('user_id', $current_b2c_session);

        $query = $this->db->get();
        return $query;
    }

    public function checkSubEmailB2b($current_b2c_session, $subscriberEmail) {
        $this->db->select('email_id');
        $this->db->from('b2b');
        $this->db->where('email_id', $subscriberEmail);
        $this->db->where('agent_id', $current_b2c_session);

        $query = $this->db->get();
        return $query;
    }

    public function addB2cNewsletterSubCheckBx($user_id, $data) {
        $this->db->where('user_id', $user_id);
        $this->db->update('b2c', $data);
        return true;
    }

    public function addB2bNewsletterSubCheckBx($user_id, $data) {
        $this->db->where('agent_id', $user_id);
        $this->db->update('b2b', $data);
        return true;
    }

    public function getNewsletterStatus($user_id, $user_type) {
        $this->db->select('newsletter_subscribe');
        if ($user_type == '3') {
            $this->db->where('user_id', $user_id);
            return $this->db->get('b2c');
        } else if ($user_type == '2') {
            $this->db->where('agent_id', $user_id);
            return $this->db->get('b2b');
        }
    }

    public function isSubscribed($subscriberEmail) {
        $this->db->where('email_id', $subscriberEmail);
        $query = $this->db->get('newsletter_subscriber');
        return $query;
    }

    //Agent starts here

    public function isAgentRegistered($email) {
        $this->db->where('email_id', $email);
        return $this->db->get('b2b');
    }

    public function isValidAgent($email, $password) {
        $this->db->where('email_id', $email);
        $this->db->where('password', $password);
        return $this->db->get('b2b');
    }

    public function createAgent($postData) {
        return $this->db->insert('b2b', $postData);
    }

    public function verifyAgentContactDetails($agent_id, $email_code, $mobile_code) {
        $this->db->select('*');
        $this->db->from('b2b');
        $this->db->where('agent_id', $agent_id);
        $this->db->where('temp_email_opt', $email_code);
        $this->db->where('temp_mobile_opt', $mobile_code);

        return $this->db->get();
    }

    public function verifyAgentContactDetails_1($agent_id, $email_code) {
        $this->db->select('*');
        $this->db->from('b2b');
        $this->db->where('agent_id', $agent_id);
        $this->db->where('temp_email_opt', $email_code);


        return $this->db->get();
    }

    public function verifyAgentContactDetails_v1($vid, $email_code, $mobile_code) {
        $this->db->select('*');
        $this->db->from('b2b');
        $this->db->where('verification_code', $vid);
        $this->db->where('temp_email_opt', $email_code);
        $this->db->where('temp_mobile_opt', $mobile_code);

        return $this->db->get();
    }

    public function verifyAgentContactDetails_v12($vid, $email_code) {
        $this->db->select('*');
        $this->db->from('b2b');
        $this->db->where('verification_code', $vid);
        $this->db->where('temp_email_opt', $email_code);

        return $this->db->get();
    }

    public function verifyAgentContactDetails_v2($vid) {
        $this->db->select('*');
        $this->db->from('b2b');
        $this->db->where('verification_code', $vid);


        return $this->db->get();
    }

    public function changeAgentStatus($b2b_id) {
        $data = array('status' => 0);
        $this->db->where('agent_id', $b2b_id);
        $this->db->update('b2b', $data);
        return true;
    }

    public function agent_deposit_details($b2b_id) {
        $this->db->where('agent_id', $b2b_id);
        return $this->db->get('b2b_deposit');
    }

    public function get_deposit_amount($b2b_id) {
        $this->db->where('agent_id', $b2b_id);
        return $this->db->get('b2b_acc_info');
    }

    public function saveDeposit_model($datadep, $agent_id, $photo) {

        $this->db->select('max(deposit_id)+1 as max_id');
        $this->db->from('b2b_deposit');
        $query_run = $this->db->get();

        $query_row = $query_run->row();

        if (!empty($query_row)) {
            $m_id = $query_row->max_id;
        }
        $transaction_id = 'AT' . date('d') . date('m') . ($m_id + 10000);
        if ($datadep['modedep'] == 'Cash Deposite') {
            $data = array(
                'agent_id' => $agent_id,
                'amount_credit' => $datadep['amount'],
                'deposited_date' => $datadep['deposited_date'],
                'deposit_type' => $datadep['modedep'],
                'transaction_id' => $transaction_id,
                'bank' => $datadep['bank'],
                'branch' => $datadep['branch'],
                'city' => $datadep['city'],
                'city' => $datadep['city'],
                'status' => 'Pending',
                'image' => $photo,
                'remarks' => $datadep['remarks']
            );
        } elseif ($datadep['modedep'] == 'Check Or DD') {

            $data = array(
                'agent_id' => $agent_id,
                'amount_credit' => $datadep['amount'],
                'deposited_date' => $datadep['deposited_date'],
                'deposit_type' => $datadep['modedep'],
                'transaction_id' => $transaction_id,
                'bank' => $datadep['issued_bank'],
                'branch' => $datadep['dd_branch'],
                'city' => $datadep['dd_city'],
                'cheque_number' => $datadep['dd_check'],
                'status' => 'Pending'
            );
        } elseif ($datadep['modedep'] == 'E-Transfer') {
            $data = array(
                'agent_id' => $agent_id,
                'amount_credit' => $datadep['amount'],
                'deposited_date' => $datadep['deposited_date'],
                'deposit_type' => $datadep['modedep'],
                'transaction_id' => $datadep['transfer_id'],
                'bank' => $datadep['e_bank'],
                'branch' => $datadep['e_branch'],
                'city' => $datadep['e_city'],
                'status' => 'Pending'
            );
        }
        $this->db->insert('b2b_deposit', $data);
        return true;
    }

    public function savemarkup_flight($datamark, $agent_id) {

        $this->db->select('*');
        $this->db->from('b2b_markup');
        $this->db->where('agent_id', $agent_id);

        $query_run = $this->db->get();

        $query_row = $query_run->num_rows();

        if ($query_row > 0) {
            $data = array(
                'AirAsiaindiaper' => $datamark['AirAsiaIndia'],
                'Aircostaper' => $datamark['Aircosta'],
                'AirIndiaper' => $datamark['AirIndia'],
                'Indianper' => $datamark['Indian'],
                'JetAirwaysper' => $datamark['JetAirways'],
                'JetLiteper' => $datamark['JetLite'],
                'AirIndiaExpressper' => $datamark['AirIndiaExpress'],
                'GoAirper' => $datamark['GoAir'],
                'Indigoper' => $datamark['Indigo'],
                'SpiceJetper' => $datamark['SpiceJet'],
                'InternationalFlightsper' => $datamark['InternationalFlights'],
                'AirAsiaindiars' => $datamark['AirAsiaIndiars'],
                'Aircostars' => $datamark['Aircostars'],
                'AirIndiars' => $datamark['AirIndiars'],
                'Indianrs' => $datamark['Indianrs'],
                'JetAirwaysrs' => $datamark['JetAirwaysrs'],
                'JetLiters' => $datamark['JetLiters'],
                'AirIndiaExpressrs' => $datamark['AirIndiaExpressrs'],
                'GoAirrs' => $datamark['GoAirrs'],
                'Indigors' => $datamark['Indigors'],
                'SpiceJetrs' => $datamark['SpiceJetrs'],
                'InternationalFlightsrs' => $datamark['InternationalFlightsrs']
            );


            $this->db->where('agent_id', $agent_id);
            $this->db->update('b2b_markup', $data);
            return true;
        } else {

            $data = array(
                'agent_id' => $agent_id,
                'AirAsiaindiaper' => $datamark['AirAsiaIndia'],
                'Aircostaper' => $datamark['Aircosta'],
                'AirIndiaper' => $datamark['AirIndia'],
                'Indianper' => $datamark['Indian'],
                'JetAirwaysper' => $datamark['JetAirways'],
                'JetLiteper' => $datamark['JetLite'],
                'AirIndiaExpressper' => $datamark['AirIndiaExpress'],
                'GoAirper' => $datamark['GoAir'],
                'Indigoper' => $datamark['Indigo'],
                'SpiceJetper' => $datamark['SpiceJet'],
                'InternationalFlightsper' => $datamark['InternationalFlights'],
                'AirAsiaindiars' => $datamark['AirAsiaIndiars'],
                'Aircostars' => $datamark['Aircostars'],
                'AirIndiars' => $datamark['AirIndiars'],
                'Indianrs' => $datamark['Indianrs'],
                'JetAirwaysrs' => $datamark['JetAirwaysrs'],
                'JetLiters' => $datamark['JetLiters'],
                'AirIndiaExpressrs' => $datamark['AirIndiaExpressrs'],
                'GoAirrs' => $datamark['GoAirrs'],
                'Indigors' => $datamark['Indigors'],
                'SpiceJetrs' => $datamark['SpiceJetrs'],
                'InternationalFlightsrs' => $datamark['InternationalFlightsrs']
            );


            $this->db->insert('b2b_markup', $data);
            return true;
        }
    }

    public function savemarkup_hotel($datamark, $agent_id) {

        $this->db->select('*');
        $this->db->from('b2b_markup');
        $this->db->where('agent_id', $agent_id);

        $query_run = $this->db->get();

        $query_row = $query_run->num_rows();

        if ($query_row > 0) {
            $data = array(
                'Hotelper' => $datamark['hotelmark'],
                'Hotelrs' => $datamark['hotelmarkrs']
            );
            $this->db->where('agent_id', $agent_id);
            $this->db->update('b2b_markup', $data);
            return true;
        } else {

            $data = array(
                'agent_id' => $agent_id,
                'Hotelper' => $datamark['hotelmark'],
                'Hotelrs' => $datamark['hotelmarkrs']
            );


            $this->db->insert('b2b_markup', $data);
            return true;
        }
    }

    public function savemarkup_bus($datamark, $agent_id) {

        $this->db->select('*');
        $this->db->from('b2b_markup');
        $this->db->where('agent_id', $agent_id);

        $query_run = $this->db->get();

        $query_row = $query_run->num_rows();

        if ($query_row > 0) {
            $data = array(
                'Busper' => $datamark['busmark'],
                'Busrs' => $datamark['busmarkrs']
            );
            $this->db->where('agent_id', $agent_id);
            $this->db->update('b2b_markup', $data);
            return true;
        } else {

            $data = array(
                'agent_id' => $agent_id,
                'Busper' => $datamark['busmark'],
                'Busrs' => $datamark['busmarkrs']
            );


            $this->db->insert('b2b_markup', $data);
            return true;
        }
    }

    public function savemarkup_cat($datamark, $agent_id) {

        $this->db->select('*');
        $this->db->from('b2b_markup');
        $this->db->where('agent_id', $agent_id);

        $query_run = $this->db->get();

        $query_row = $query_run->num_rows();

        if ($query_row > 0) {
            $data = array(
                'Carper' => $datamark['carmark'],
                'Carrs' => $datamark['carmarkrs']
            );
            $this->db->where('agent_id', $agent_id);
            $this->db->update('b2b_markup', $data);
            return true;
        } else {

            $data = array(
                'agent_id' => $agent_id,
                'Carper' => $datamark['carmark'],
                'Carrs' => $datamark['carmarkrs']
            );


            $this->db->insert('b2b_markup', $data);
            return true;
        }
    }

    public function contactAdmin_model($data) {
        $this->db->insert('user_failed_verifications', $data);
        return true;
    }

    public function initializeAccountInfo_model($agent_id) {
        $data = array('agent_id' => $agent_id, 'balance_credit' => 0, 'last_credit' => 0);
        $this->db->insert('b2b_acc_info', $data);
        return true;
    }

    public function update_credit_amount($update_credit_amount, $b2b_id) {
        $this->db->where('agent_id', $b2b_id);
        $this->db->update('b2b_acc_info', $update_credit_amount);
    }

    public function update_payment_transaction($payment_transaction) {
        $this->db->insert('booking_transaction', $payment_transaction);
    }

    public function update_account_transaction($account_transaction) {
        $this->db->insert('account_statment', $account_transaction);
        $bid = $this->db->insert_id();
        $timing = date('Ymd');
        $timing1 = date('His');
        $txno = 'TX' . $timing . $bid . $timing1;
        $update_account = array(
            'statment_number' => $txno
        );

        $this->db->where('account_statment_id', $bid);

        $this->db->update('account_statment', $update_account);
    }

    public function get_markup($module, $country = '') {
        //Get Markup Starts
        if ($this->session->userdata('b2b_id')) {
            $user_type = '2';
            $b2b_id = $this->session->userdata('b2b_id');
            $markup = $this->get_agent_markup($b2b_id, $module);
            //echo '<pre>';print_r($markup->hotel_markup);
            if (!empty($markup->markup)) {
                $aMarkup = array(
                    'markup' => $markup->markup,
                    'module' => $module,
                    'type' => 'b2b'
                );
            } else {
                $aMarkup = array(
                    'markup' => 0,
                    'module' => $module,
                    'type' => 'b2b'
                );
            }
            return $aMarkup;
        } else {
            $markup = $this->get_user_markup($module, $country);
            if (!empty($markup->markup)) {
                $aMarkup = array(
                    'markup' => $markup->markup,
                    'module' => $module,
                    'type' => 'b2c'
                );
            } else {
                $aMarkup = array(
                    'markup' => 0,
                    'module' => $module,
                    'type' => 'b2c'
                );
            }
            return $aMarkup;
        }
        //Get Markup Ends
        //echo '<pre>';print_r($aMarkup);
    }

    public function get_agent_markup($b2b_id, $module) {
        //$this->db->where('hotel_country_id',$country);
        $this->db->where('agent_id', $b2b_id);
        $this->db->where('product', $module);
        $this->db->where('markup_type', 'Specific');
        $query = $this->db->get('markup_b2b');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            //$this->db->where('agent_id',$b2b_id);
            $this->db->where('product', $module);
            $this->db->where('markup_type', 'Generic');
            $query = $this->db->get('markup_b2b');
            if ($query->num_rows() > 0) {
                return $query->row();
            } else {
                return 0;
            }
        }
    }

    public function get_user_markup($module, $country) {
        if ($country != '') {
            $this->db->where('name', $country);
            $query = $this->db->get('country');
            if ($query->num_rows() > 0) {
                $c = $query->row();
                $this->db->where('country', $c->country_id);
                $this->db->where('product', $module);
                $this->db->where('markup_type', 'Specific');
                $query = $this->db->get('markup_b2c');
                if ($query->num_rows() > 0) {
                    return $query->row();
                } else {
                    $this->db->where('product', $module);
                    $this->db->where('markup_type', 'Generic');
                    $query = $this->db->get('markup_b2c');
                    if ($query->num_rows() > 0) {
                        return $query->row();
                    } else {
                        return 0;
                    }
                }
            } else {

                $this->db->where('product', $module);
                $this->db->where('markup_type', 'Generic');
                $query = $this->db->get('markup_b2c');
                if ($query->num_rows() > 0) {
                    return $query->row();
                } else {
                    return 0;
                }
            }
        } else {

            $this->db->where('product', $module);
            $this->db->where('markup_type', 'Generic');
            $query = $this->db->get('markup_b2c');
            if ($query->num_rows() > 0) {
                return $query->row();
            } else {
                return 0;
            }
        }
    }

    public function get_airport_country($airport_code) {

        $this->db->where('airport_code', $airport_code);
        $query = $this->db->get('flight_airport_list');
        if ($query->num_rows() > 0) {
            $v = $query->row();
            // print_r($v);exit;
            return $v->country;
        } else {
            return '';
        }
    }

 /*   public function get_my_markup() {
        //Get Markup Starts
        if ($this->session->userdata('b2b_id')) {
            $user_type = '2';
            $b2b_id = $this->session->userdata('b2b_id');
            $userdata = $this->GetUserData($user_type, $b2b_id)->row();
            $markup = $userdata->markup;
            //echo '<pre>';print_r($markup->hotel_markup);
            if (!empty($userdata->markup) && $markup != 0.00) {
                $aMarkup = array(
                    'markup' => $userdata->markup,
                    'type' => 'b2b'
                );
            } else {
                $aMarkup = array(
                    'markup' => 0,
                    'type' => 'b2b'
                );
            }
            return $aMarkup;
        } else {
            $aMarkup = array(
                'markup' => 0,
                'type' => 'mymarkup'
            );
            return $aMarkup;
        }
        //Get Markup Ends
        //echo '<pre>';print_r($aMarkup);
    }   */

    public function get_my_markup_flight()
    {
          if ($this->session->userdata('b2b_id')) {
            $user_type = '2';
            $b2b_id = $this->session->userdata('b2b_id');
         
            $this->db->where('agent_id',$b2b_id);
           
            $res=$this->db->get('b2b_agent_markup_flight');
            
            if($res->num_rows() > 0 )
             {
              // $data= $res->row();
               $data= $res->result();
            //  echo'<pre>';
             // print_r(($data));
              $markup_data=array();
              foreach($data as $f_data)
              {
               /* echo $f_data->flight_name;
                  echo $f_data->markup;
                  echo $f_data->type; */
                 $markup_data[$f_data->flight_name]['markup']=$f_data->markup;
                 $markup_data[$f_data->flight_name]['type']=$f_data->type;
                 
              }   
              
              return $markup_data;
            
        }
      }
    }
    
      public function get_my_markup() {
        //Get Markup Starts
           if ($this->session->userdata('b2b_id')) {
            $user_type = '2';
            $b2b_id = $this->session->userdata('b2b_id');
         
            $this->db->where('agent_id',$b2b_id);
            $res=$this->db->get('b2b_agent_markup_hotel');
            
            if($res->num_rows() > 0 )
            {
               $data= $res->row();
               
               $markup=$data->markup;
               $type=$data->type;
               
               
               if($type=='0')
               { /*0== % */
                   
                   $markup_data=array(
                    'markup' => $data->markup,
                    'type' => '0'
                  );  
                  // print_r($markup_data);exit;
                 return $markup_data;
                   
               }
               
            if($type=='1')
               { /*1== Rs */
                   
                   $markup_data=array(
                    'markup' => $data->markup,
                    'type' => '1'
                  );  
                      // print_r($markup_data);exit;
                 return $markup_data;
                   
               }
   
                
            }
        
        } else {
            $aMarkup = array(
                'markup' => 0,
                'type' => '1'
            );
            return $aMarkup;
        }
        //Get Markup Ends
        //echo '<pre>';print_r($aMarkup);
    }

       
    public function PercentageToAmount($total, $percentage) {
        $amount = ($percentage / 100) * $total;
        $total = number_format(($total + $amount), 2, '.', '');
        return $total;
    }

    public function PercentageMinusAmount($total, $amount) {
        //$amount = ($percentage/100) * $total;
        $total = number_format(($total - $amount), 2, '.', '');
        return $total;
    }

    public function PercentageAmount($total, $percentage) {
        $amount = ($percentage / 100) * $total;
        $amount = number_format(($amount), 2, '.', '');
        return $amount;
    }

    public function get_curr_val($curr) {
        $curr = strtoupper($curr);
        //$this->db->select('value');
        $this->db->where('country', $curr);
        $price = $this->db->get('currency_converter')->row();
        return $value = $price->value;
    }

    public function currency_convertor($amount) {
        if ($this->display_currency === CURR) {
            $amount = $amount * 1;
            return number_format(($amount), 2, '.', '');
        } else {
            $amount = ($amount) * ($this->curr_val);
            return number_format(($amount), 2, '.', '');
        }
    }

    public function insertInUserVerification($insert_data) {
        $this->db->insert('user_verifications', $insert_data);
        return true;
    }

    public function addMarkUp_model($agent_id, $data) {
        $this->db->where('agent_id', $agent_id);
        $this->db->update('b2b', $data);
        return true;
    }

    public function addBalanceAlert($agent_id, $data1, $data) {
        $this->db->where('agent_id', $agent_id);
        $res = $this->db->get('balance_alert');
        if ($res->num_rows() > 0) {
            $this->db->where('agent_id', $agent_id);
            $this->db->update('balance_alert', $data);
            return true;
        } else {

            $this->db->insert('balance_alert', $data1);
            return true;
        }
    }

    public function getting_balance_alert($agent_id) {
        $this->db->where('agent_id', $agent_id);
        $res = $this->db->get('balance_alert');
        return $res->result();
    }

    public function Get_acdetails($datade) {

        $agent_id = $datade['agnt'];
        $sdate = $datade['sdate'];
        $edate = $datade['edate'];
        $sql = "SELECT * FROM `account_statment` WHERE `agent_id`=$agent_id AND `date_added` BETWEEN '$sdate' AND  '$edate' ";

        $query = $this->db->query($sql);

        return $datas = $query->result();
    }

    public function getting_commission($agent_id) {
        $this->db->select('CommissionPlan');
        $this->db->where('AgentID', $agent_id);
        $this->db->where('API !=','BUS' );
        $res = $this->db->get('CommissionPlan');
        if ($res->num_rows() > 0) {
            $row = $res->row();

            $Commission_plan = $row->CommissionPlan;

            $this->db->select("Id,FlightDetails,Carrier,Type,$Commission_plan");

            $results = $this->db->get('AdminCommissionTable');

            return $results->result();
        }
    }

    public function Agent_commission_SpecFlight($agent_id, $FlightDetails) {
        $this->db->select('CommissionPlan');
        $this->db->where('AgentID', $agent_id);
        $res = $this->db->get('CommissionPlan');
        if ($res->num_rows() > 0) {
            $row = $res->row();

            $Commission_plan = $row->CommissionPlan;
            $data['CommissionPlan']=$Commission_plan;
            $this->db->select("Id,FlightDetails,Carrier,Type,$Commission_plan");
            $this->db->where('FlightDetails', $FlightDetails);
            $results = $this->db->get('AdminCommissionTable');
            $data['result']=$results->result();
          
           if ($results->num_rows() > 0) {
               return $data;
           }
          
          
           
        }
    }

    public function getting_flight_markup($agent_id) {
        $this->db->select('FlightDetails');
        $res = $this->db->get('AdminCommissionTable');

        $arr = array();
        $i = 0;
        foreach ($res->result() as $value) {
            //echo $value->FlightDetails;

            $arr[$i] = $value->FlightDetails;
            ++$i;
        }

        //-----------------------------------

        $this->db->select('flight_name');
        $this->db->where('agent_id', $agent_id);
        $reu = $this->db->get('b2b_agent_markup_flight');

        $ar = array();
        $j = 0;
        foreach ($reu->result() as $val) {
            //echo $val->flight_name;

            $ar[$j] = $val->flight_name;
            ++$j;
        }
        //--------------------------------- 
        //print_r( array_diff($arr,$ar));
        //print_r($res->result());

        $arr_dif = array_diff($arr, $ar);

        if (!empty($arr_dif)) {
            //for($k=0;$k<=count($arr_dif);$k++)
            foreach ($arr_dif as $flight) {

                $this->db->set('flight_name', $flight);
                $this->db->set('agent_id', $agent_id);
                $this->db->insert('b2b_agent_markup_flight');
            }

            $this->db->where('agent_id', $agent_id);
            $result1 = $this->db->get('b2b_agent_markup_flight');
            return $result1->result();
        } else {
            $this->db->where('agent_id', $agent_id);
            $result1 = $this->db->get('b2b_agent_markup_flight');
            return $result1->result();
        }
    }

    public function update_flight_markup($b2b_agent_id, $id, $markup, $type) {

        $this->db->set('markup', $markup);
        $this->db->set('type', $type);
        $this->db->where('id', $id);
        $this->db->where('agent_id', $b2b_agent_id);
        $res = $this->db->update('b2b_agent_markup_flight');
        return $res;
    }

    public function getting_hotel_markup($agent_id) {
        $this->db->where('agent_id', $agent_id);
        $res = $this->db->get('b2b_agent_markup_hotel');
        return $res->result();
    }

    public function update_hotel_markup($b2b_agent_id, $markup, $type) {
        $this->db->where('agent_id', $b2b_agent_id);
        $results = $this->db->get('b2b_agent_markup_hotel');

        if ($results->num_rows() > 0) {

            $this->db->set('markup', $markup);
            $this->db->set('type', $type);
            $this->db->where('agent_id', $b2b_agent_id);
            $res = $this->db->update('b2b_agent_markup_hotel');
            return $res;
        } else {
            $this->db->set('markup', $markup);
            $this->db->set('type', $type);
            $this->db->set('agent_id', $b2b_agent_id);
            $res = $this->db->insert('b2b_agent_markup_hotel');
            return $res;
        }
    }

    public function getting_car_markup($agent_id) {
        $this->db->where('agent_id', $agent_id);
        $res = $this->db->get('b2b_agent_markup_car');
        return $res->result();
    }

    public function update_car_markup($b2b_agent_id, $markup, $type) {
        $this->db->where('agent_id', $b2b_agent_id);
        $results = $this->db->get('b2b_agent_markup_car');

        if ($results->num_rows() > 0) {

            $this->db->set('markup', $markup);
            $this->db->set('type', $type);
            $this->db->where('agent_id', $b2b_agent_id);
            $res = $this->db->update('b2b_agent_markup_car');
            return $res;
        } else {
            $this->db->set('markup', $markup);
            $this->db->set('type', $type);
            $this->db->set('agent_id', $b2b_agent_id);
            $res = $this->db->insert('b2b_agent_markup_car');
            return $res;
        }
    }

    public function getting_bus_markup($agent_id) {
        $this->db->where('agent_id', $agent_id);
        $res = $this->db->get('b2b_agent_markup_bus');
        return $res->result();
    }

    public function update_bus_markup($b2b_agent_id, $markup, $type) {
        $this->db->where('agent_id', $b2b_agent_id);
        $results = $this->db->get('b2b_agent_markup_bus');

        if ($results->num_rows() > 0) {

            $this->db->set('markup', $markup);
            $this->db->set('type', $type);
            $this->db->where('agent_id', $b2b_agent_id);
            $res = $this->db->update('b2b_agent_markup_bus');
            return $res;
        } else {
            $this->db->set('markup', $markup);
            $this->db->set('type', $type);
            $this->db->set('agent_id', $b2b_agent_id);
            $res = $this->db->insert('b2b_agent_markup_bus');
            return $res;
        }
    }

       /*Hotel Commission*/
    
    public function getting_hotel_commission($agent_id)
     {   $this->db->select('CommissionPlan');
         $this->db->where('AgentId', $agent_id);
         $res = $this->db->get('HotelCommissionPlan');
        if ($res->num_rows() > 0) {
            $row = $res->row();

            $Commission_plan = $row->CommissionPlan;

            $this->db->select("id,hotel_name,type,$Commission_plan");

            $results = $this->db->get('admin_hotel_commission');

            return $results->result();
        }
     }
     
         public function get_agent_commission($agent_id)
		  {   
		   if($this->session->userdata('b2b_id')){
			  $this->db->select('CommissionPlan');
			$this->db->where('AgentId', $agent_id);
			$res = $this->db->get('HotelCommissionPlan');
			if ($res->num_rows() > 0) {
				$row = $res->row();

				$Commission_plan = $row->CommissionPlan;

				$this->db->select("type,$Commission_plan");
			   # $this->db->select('type,'.$Commission_plan.');

				$results = $this->db->get('admin_hotel_commission')->row();

				#echo "<pre>";
			   return $results;
			   # exit;
				#return  $results->result();
			}
		   }
			
			
		  }

       //----------Bus Commission----------------//  
          
           public function getting_bus_commission($agent_id)
             {   $this->db->select('CommissionPlan');
                 $this->db->where('AgentId', $agent_id);
                 $this->db->where('API', 'BUS');
                 $res = $this->db->get('CommissionPlan');
                if ($res->num_rows() > 0) {
                    $row = $res->row();

                    $Commission_plan = $row->CommissionPlan;

                    $this->db->select("id,bus_name,type,$Commission_plan");

                    $results = $this->db->get('admin_bus_commission');

                    return $results->result();
                }
             }
     

         /*   Bank Details      */

         public function get_bank_details()
            {

               $res=$this->db->get('bank_details');
               return $res->result();
            }
         /*Banner image for dashboard*/

            public function get_banner_image()
            {
                $res=$this->db->get('banner_images',2);
                return $res->result();
            }
            
            
    /* public function current_agent_markup_model() {
      $this->db->select('markup')
      } */
}
