<?php

namespace App\Controllers;
use App\Models\CarModel;

class CarController extends BaseController
{
    public function get_all_cars()
    {
        $query = new CarModel();
        $cars = $this->db->getAll($query);
        return $cars;
    }

    public function get_car($id)
    {
        $query = new CarModel();
        $car = $this->db->get($query, $id);
        if (!$car) {
            return ['status' => 400, 'message' => 'Not found'];
        }
        return $car;
    }

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

    public function delete_car($id)
    {
        $query = new CarModel();
        return $this->db->delete($query, $id);
    }
}
