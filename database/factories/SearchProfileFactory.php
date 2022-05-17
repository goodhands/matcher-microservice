<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\Traits\Generator;

class SearchProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $field = Generator::fields('search_property');
        return [
            'searchFields' => array(
                "price" => $field['price'],
                "area" => $field['area'],
                "yearOfConstruction" => $field['yearOfConstruction'],
                "rooms" => $field['rooms'],
                "returnActual" => $field['returnActual']
            ),
            'propertyType' => Generator::uuid(),
            'name' => $this->faker->name(),
        ];
    }
}
