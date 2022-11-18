<?php

namespace App\Controllers;

use App\Models\KomikModel;

class Komik extends BaseController
{
    protected $komikModel;
    public function __construct()
    {
        $this->komikModel = new KomikModel();
    }
    
    public function index(){

        $data = [
            'title' => 'Daftar Komik',
            'komik' => $this->komikModel->getKomik()
        ];
        return view('komik/index', $data);
    }

    public function detail($slug){
        $data = [
            'title' => 'Detail komik',
            'komik' => $this->komikModel->getKomik($slug)
        ];

        // jika komik tidak ada di table
        if(empty($data['komik'])){
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Judul komik " . $slug . " tidak ditemukan");
        }
        
        return view('komik/detail', $data);
    }

    public function create(){
        
        $data = [
            'title' => 'Form Tambah Data Komik', 
            'validation' => \Config\Services::validation()   
        ];

        return view('komik/create' , $data);
    }

    public function save(){
        // Validasi input
        if(!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required' => '{field} komik harus diisi',
                    'is_unique' => '{field} sudah ada'
                ]    
            ]
        ])) {
            $validation = \Config\Services::validation();
            
            return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
        }


        $slug = url_title($this->request->getVar('judul'), '-', true);
        
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $this->request->getVar('sampul')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan');
        
        return redirect()->to('/komik');
        
    }

    public function delete($id){
        $this->komikModel->delete($id);

        session()->setFlashdata('pesan', 'Data berhasil dihapus');
        
        return redirect()->to('/komik');
    }
}
