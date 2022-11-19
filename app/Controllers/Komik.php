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
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'ukuran terlalu besar',
                    'is_image' => 'yang anda pilih bukan gambar',
                    'mime_in' => 'yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            // $validation = \Config\Services::validation();
            
            // return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
            
            return redirect()->to('/komik/create')->withInput();
        }

        // mengambil gambar
        $fileSampul = $this->request->getFile('sampul');

        // mengecek apakah tidak ada gambar yang di upload
        if($fileSampul->getError() == 4){
            $namaSampul = 'ori.png';
        }else{
            // generate nama sampul random
            $namaSampul = $fileSampul->getRandomName();
            
            // memindahkan file ke folder img
            $fileSampul->move('img', $namaSampul);
        }


        $slug = url_title($this->request->getVar('judul'), '-', true);
        
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan');
        
        return redirect()->to('/komik');
        
    }

    public function delete($id){
        // mencari gambar berdasarkan id
        $komik = $this->komikModel->find($id);

        // cek jika file gambarnya adalah ori
        if($komik['sampul'] != 'ori.png'){
            // hapus gambar
            unlink('img/' . $komik['sampul']);
        }
        
        
        $this->komikModel->delete($id);

        session()->setFlashdata('pesan', 'Data berhasil dihapus');
        
        return redirect()->to('/komik');
    }

    public function edit($slug){
        $data = [
            'title' => 'Form Ubah Data Komik', 
            'validation' => \Config\Services::validation(),
            'komik' => $this->komikModel->getKomik($slug)
        ];

        return view('komik/edit' , $data);
    } 

    public function update($id){
        // cek judul
        $komikLama = $this->komikModel->getKomik($this->request->getVar('slug'));
        if($komikLama['judul'] == $this->request->getVar('judul')){
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }
        
        // validasi
        if(!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '{field} komik harus diisi',
                    'is_unique' => '{field} sudah ada'
                ]    
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'ukuran terlalu besar',
                    'is_image' => 'yang anda pilih bukan gambar',
                    'mime_in' => 'yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            
            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput();
        }

        $fileSampul = $this->request->getFile('sampul');

        // mengecek gambar apakah tetap gambar lama
        if($fileSampul->getError() == 4){
            $namaSampul = $this->request->getVar('sampulLama');
        } else {
            // generate nama file random
            $namaSampul = $fileSampul->getRandomName();

            // memindahkan gambar
            $fileSampul->move('img', $namaSampul);

            // hapus file lama
            unlink('img/' . $this->request->getVar('sampulLama'));
        }
        
        
        $slug = url_title($this->request->getVar('judul'), '-', true);
        
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data berhasil diubah');
        
        return redirect()->to('/komik');
    }
}
