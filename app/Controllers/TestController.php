<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function renderTest() {
        $this->render('test/test');
    }
}