<?php

namespace App\Controllers;

class TechnicianController extends BaseController {
    public function renderTechnicianDashboard() {
        $this->render('technician/index');
    }
}