<?php

namespace App\Controllers;

use App\Enums\AggregateFunctionEnum;
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

    public function sample1()
    {
        // = and >= operatör filter

        $query = new CarModel();
        $query->setWhere(new QueryOperator('brand', '=', 'Toyota'));
        $query->setWhere(new QueryOperator('year', '>=', 2020));
        $query->setColumns(['brand', 'AVG(price) as average_price']);
        $query->setOrderBy('price', 'ASC');

        $cars = $this->sdk()->db()->select($query);
        return $cars;
    }
    public function sample2()
    {
        // price range and color

        $minPrice = 20000;
        $maxPrice = 40000;
        $color = 'Black';
        $query = new CarModel();
        $query->setWhere(new QueryOperator('price', 'BETWEEN', [$minPrice, $maxPrice]));
        $query->setWhere(new QueryOperator('color', '=', $color));
        $query->setOrderBy('created_at', 'DESC');
        $query->setLimit(0, 5);

        $cars = $this->sdk()->db()->select($query);
        return $cars;
    }
    public function sample3()
    {
        // avg price

        $minPrice = 25000;
        $maxPrice = 40000;
        $query = new CarModel();
        $query->setWhere(new QueryOperator('price', 'BETWEEN', [$minPrice, $maxPrice]));

        $avg = $this->sdk()->db()->aggregate($query, AggregateFunctionEnum::AVG, 'price');
        return $avg;
    }
    public function sample4()
    {
        // query group

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
