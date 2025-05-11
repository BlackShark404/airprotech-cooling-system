<?php

namespace App\Controllers;

use App\Models\ServiceRequestModel;
use Core\Session;

class ServiceRequestController extends BaseController
{
    private $serviceRequestModel;

    public function __construct()
    {
        parent::__construct();
        $this->serviceRequestModel = $this->loadModel('ServiceRequestModel');
    }
}