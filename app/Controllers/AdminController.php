<?php

namespace App\Controllers;

class AdminController extends BaseController {
    public function renderServiceRequest() {
        $this->render('admin/service-request');
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
}