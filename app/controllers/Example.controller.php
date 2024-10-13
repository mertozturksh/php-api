<?php

namespace App\Controllers;
use App\Models\CarModel;

class ExampleController extends BaseController
{

    public function get_all_cars()
    {
        $query = new CarModel();
        $cars = $this->sdk()->db()->getAll($query);
        return $cars;
    }

    public function get_car($id)
    {
        $query = new CarModel();
        $car = $this->sdk()->db()->get($query, $id);
        if (!$car) {
            return ['status' => 400, 'message' => 'Not found'];
        }
        return ['status' => 200, 'data' => $car];
    }

    public function create_car($data)
    {
        $query = new CarModel();
        $query->brand = $data['brand'];
        $query->model = $data['model'];
        $query->year = $data['year'];
        $query->color = $data['color'];
        $query->price = $data['price'];

        return $this->sdk()->db()->insert($query);
    }

    public function update_car($id, $data)
    {
        $query = new CarModel();
        $query->brand = $data['brand'];
        $query->model = $data['model'];
        $query->year = $data['year'];
        $query->color = $data['color'];
        $query->price = $data['price'];

        return $this->sdk()->db()->update($query, $id);
    }

    public function delete_car($id)
    {
        $query = new CarModel();
        return $this->sdk()->db()->delete($query, $id);
    }
}
