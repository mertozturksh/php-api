<?php

namespace App\Controllers;

use App\Models\CarModel;
use Core\QueryGroup;
use Core\QueryOperator;

class ExampleController extends BaseController
{

    public function getAll()
    {
        $query = new CarModel();
        $cars = $this->sdk()->db()->select($query);
        return $cars;
    }
    public function get($id)
    {
        $query = new CarModel();
        $query->setWhere(new QueryOperator('id', '=', $id));
        $car = $this->sdk()->db()->select($query);
        if (empty($car)) {
            return ['status' => 400, 'message' => 'Not found'];
        }
        return $car[0];
    }
    public function create($variables)
    {
        if (!$this->sdk()->checkVariables($variables, ['brand', 'model', 'year', 'color', 'price'])) {
            return ['status' => 400, 'message' => 'Missing parameters.'];
        }

        $car = new CarModel();
        $car->brand = $variables['brand'];
        $car->model = $variables['model'];
        $car->year = $variables['year'];
        $car->color = $variables['color'];
        $car->price = $variables['price'];

        $result = $this->sdk()->db()->insert($car);
        if ($result) {
            return 1;
        } else {
            return ['status' => 500, 'message' => 'Failed to create car'];
        }
    }
    public function update($id, $variables) // TODO FIXIT
    {
        return ['status' => 500, 'message' => 'UNHANDLED ERROR'];

        $car = new CarModel();
        $car->year = 2999;
        $car->setWhere(new QueryOperator('id', '=', $id));
        $result = $this->sdk()->db()->update($car);
        return $result;
    }
    public function delete($id)
    {
        $car = new CarModel();
        $car->setWhere(new QueryOperator('id', '=', $id));
        $result = $this->sdk()->db()->delete($car);

        if ($result) {
            return ['status' => 200, 'message' => 'Car deleted successfully'];
        } else {
            return ['status' => 500, 'message' => 'Failed to delete car'];
        }
    }


    // EXAMPLE FUNCTIONS

    public function get_filtered_cars()
    {
        $query = new CarModel();
        $query->setWhere(new QueryOperator('brand', '=', 'Toyota'));
        $query->setWhere(new QueryOperator('year', '>=', 2020));
        $query->setOrderBy('price', 'ASC');

        $cars = $this->sdk()->db()->select($query);
        return $cars;
    }
    public function get_cars_in_price_range($minPrice, $maxPrice, $color)
    {
        $query = new CarModel();
        $query->setWhere(new QueryOperator('price', '>=', $minPrice));
        $query->setWhere(new QueryOperator('price', '<=', $maxPrice));
        $query->setWhere(new QueryOperator('color', '=', $color));
        $query->setOrderBy('created_at', 'DESC');
        $query->setLimit(0, 5);

        $cars = $this->sdk()->db()->select($query);
        return $cars;
    }
    public function get_average_price_by_brand($minPrice, $maxPrice)
    {
        $query = new CarModel();
        $query->setWhere(new QueryOperator('price', 'BETWEEN', [$minPrice, $maxPrice]));
        
        $cars = $this->sdk()->db()->select($query);
        return $cars;
    }
    public function get_cars_with_or_condition()
    {
        $query = new CarModel();

        $group1 = new QueryGroup('AND');
        $group1->addCondition(new QueryOperator('brand', '=', 'Toyota'));
        $group1->addCondition(new QueryOperator('year', '>=', 2020));

        $group2 = new QueryGroup('AND');
        $group2->addCondition(new QueryOperator('brand', '=', 'Honda'));
        $group2->addCondition(new QueryOperator('year', '>=', 2018));

        $mainGroup = new QueryGroup('OR');
        $mainGroup->addCondition($group1);
        $mainGroup->addCondition($group2);

        $query->setWhere($mainGroup);

        $cars = $this->sdk()->db()->select($query);
        return $cars;
    }
}
