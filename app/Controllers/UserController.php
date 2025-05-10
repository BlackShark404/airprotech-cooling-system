<?php

namespace App\Controllers;

class UserController extends BaseController{
    public function renderUserDashboard() {
        $this->render("user/dashboard");
    }

    public function renderUserServices() {
        $this->render("user/services");
    }

    public function renderUserProducts() {
        $this->render("user/products");
    }

    public function renderMyOrders() {
        $this->render("user/my-orders");
    }
}