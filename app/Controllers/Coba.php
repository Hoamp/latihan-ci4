<?php

namespace App\Controllers;

class Coba extends BaseController
{
    public function index()
    {
        echo "Controller coba dgn method index";
    }

    public function about($nama)
    {
        echo "Halo, $nama.";
    }
}
