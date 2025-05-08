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

    public function renderProfile() {
        $this->render('admin/profile');
    }

    public function renderReports() {
        $this->render('admin/reports');
    }

    public function renderAddProduct() {
        $this->render('admin/add-product');
    }

    public function renderUserManagement() {
        $this->render('admin/user-management');
    }

    public function renderAdminProfile() {
        $this->render('admin/profile');
    }
}