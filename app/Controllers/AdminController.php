<?php

namespace App\Controllers;

class AdminController extends BaseController {
    public function renderAdminDashboard() {
        $this->render('admin/dashboard');
    }
}