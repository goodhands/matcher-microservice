<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\Traits\Generator;

class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $field = Generator::fields('property');

        return [
            'fields' => array(
                'area' => $field['area'],
                'yearOfConstruction' => $field['yearOfConstruction'],
                'rooms' => $field['rooms'],
                'heatingType' => $field['heatingType'],
                'parking' => $field['parking'],
                'returnActual' => $field['returnActual'],
                'price' => $field['price']
            ),
            'propertyType' => Generator::uuid(),
            'address' => $this->faker->address(),
            'name' => $this->faker->name(),
        ];
    }
}
