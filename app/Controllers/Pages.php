<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Home | Thomas' ,
            'tes' => ['satu', 'dua', 'tiga']   
        ];
        
        return view('pages/home', $data);
    }

    public function about(){
        $data = [
            'title' => 'About Me'    
        ];

        return view('pages/about', $data);
    }

    public function contact(){
        $data = [
            'title' => 'contact saya',
            'alamat' => [
                [
                    'tipe' => 'rumah',
                    'alamat' => 'Jl anu subroto',
                    'kota' => 'Karanganyar'
                ],
                [
                    'tipe' => 'sekolah',
                    'alamat' => 'Jl gatot subroto',
                    'kota' => 'Karanganyar'
                ]
            ]
        ];
        return view('pages/contact', $data);
    }
}
