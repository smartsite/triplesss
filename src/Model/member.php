<?php

/**
 *  Users who have paid are members! *   
 * 
 */

namespace  Triplesss\user;

use \Triplesss\notification\Notification;
use \Triplesss\repository\Repository;
use \Triplesss\error\Error;


class Member extends User {
    
    public $memberID;
    public $status;         //  on hold, pending delete, suspended, warned
    public $active;         //  current or deleted
    public $payment_method = "credit card";
    public $customerID;     // unique id used by merchant to track payment
    public $renewal_date;   // payments due date
    public $created_date;   // member created date
    public $renewal_interval = 'monthly';

    
    function __construct() {
        $this->repository = new Repository();
        $this->error = new Error();
        $this->created_date = date("Y-m-d");
        $this->renewal_date = date("Y-m-d", strtotime("+1 month"));
        return $this;
    }
    
    Public function create() {
        return  $this->repository->createMember($this);
    }

    Public function getId() :Int{
        return $this->memberID;
    }

    Public function setId(Int $id) {
        $this->memberID = $id;
    }

    Public function getStatus() :Int {
        return $this->status;
    }

    Public function getActive() :Bool {
        return $this->active && 1;
    }

    Public function setActive(Bool $active) { 
        $details = [
            'member_id' => $this->memberID,
            'active' => $active
        ];
        
        $this->repository->updateMember($details);  
        $this->active = 1; 
    }


    Public function getRenewalDate() :String {
        return $this->renewal_date;
    }

    Public function getRenewalInterval() :String {
        return $this->renewal_interval;
    }

    Public function getCustomerId() :String {
        return $this->customerID;
    }

    Public function setCustomerId(String $customer_id) {
        $details = [
            'member_id' => $this->memberID,
            'customer_id' => $customer_id
        ];
        $this->customerID = $this->repository->updateMember($details);        
    }

    public function getMemberByUserId(Int $user_id) {       
        return $this->repository->getMember(-1, $user_id, true);
    }

    public function getMemberByCustomerId(String $customer_id) {       
        return $this->repository->getMemberByCustomerId($customer_id);
    }    

    Public function getPaymentMethod() :String {
        return $this->payment_method;
    }

    Public function getCreatedDate() :String {
        return $this->created_date;
    }

    Public function getDetails(Bool $safe = true) :Array {
        return $this->repository->getMember($this->memberID, -1, $safe);
    }
}