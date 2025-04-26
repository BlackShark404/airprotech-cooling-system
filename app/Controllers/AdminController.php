<?php

namespace App\Controllers;

class AdminController extends BaseController {
    public function renderAdminDashboard() {
        $this->render('admin/dashboard');
    }

    public function renderServiceRequest() {
        $this->render('admin/service-request');
    }

    public function renderTechnician() {
        $this->render('admin/technician');
    }

    public function renderInventory() {
        $this->render('admin/inventory');
    }
}