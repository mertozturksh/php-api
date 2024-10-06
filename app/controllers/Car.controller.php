<?php

namespace App\Controllers;

use App\Models\CarModel;

class CarController extends BaseController
{
    // Tüm araçları getirme
    public function get_all_cars()
    {
        $query = new CarModel();
        $cars = $this->db->getAll($query);
        return $cars;
    }

    // Belirli bir aracı ID ile getirme
    public function get_car($id)
    {
        $query = new CarModel();
        return $this->db->get($query, $id);
    }

    // Yeni bir araç kaydı ekleme
    public function create_car($data)
    {
        $query = new CarModel();
        $query->brand = $data['brand'];
        $query->model = $data['model'];
        $query->year = $data['year'];
        $query->color = $data['color'];
        $query->price = $data['price'];

        return $this->db->insert($query);
    }

    // Bir aracı güncelleme
    public function update_car($id, $data)
    {
        $query = new CarModel();
        $query->brand = $data['brand'];
        $query->model = $data['model'];
        $query->year = $data['year'];
        $query->color = $data['color'];
        $query->price = $data['price'];

        return $this->db->update($query, $id);
    }

    // Bir aracı silme
    public function delete_car($id)
    {
        $query = new CarModel();
        return $this->db->delete($query, $id);
    }
}
