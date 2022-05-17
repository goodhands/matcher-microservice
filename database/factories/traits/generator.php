<?php

namespace Database\Factories\Traits;

trait Generator
{
    public static function uuid()
    {
        // Pre-Generated so we can have similar
        // uuid between search profiles and properties
        $uuids = array(
            'd388b6fa-4bdc-47d1-aa65-bb7a0526f94c',
            'da7a06f5-7ff3-4ae0-9daf-1967676e9d45',
            '1c29a915-cbdc-4be1-8b4d-1250ad1c1840',
            'eaf9fc09-2238-4a83-a20e-094c29fb1dd1',
            'ed1cf1b4-e01b-4303-8b9f-d1da9e94aa15',
            '554028e0-2c57-400e-8a85-e2b719b7706f',
            '92037946-7d62-4adf-b704-4563d985c314',
            'c0c1f62d-7c76-4aa6-a1be-98c1fc67e707',
            '8bdb2e74-bf8d-42c2-bfc6-589021fe6712',
            '1795f7fe-fb63-486f-9b18-e17434893630',
            '4bf9afa7-875b-4082-9c7c-cb90baca903c',
            '4c043835-cb9f-4dcc-947d-a88e0506015a',
            '264ac990-0603-4592-8090-1c9d8518869e',
            '380afec4-41aa-4576-a448-88168bdbb24a',
            '07c542f5-70cf-4b94-aff2-2726276bbb0a',
            '54021377-b696-4759-b40b-52e0d5af5b51',
            '8e70afa2-5056-4289-b6e3-ef7eb94e1999',
            '03ba0dae-8462-408c-864f-9043713fb503',
            '409341f3-2020-4870-9236-68b5f2750ea6',
            '94f51e18-979e-46fc-8ff4-7e97894c0795',
            '9a2bbb3c-ff38-409d-a604-f444b105be85',
            '27fb40a3-d214-4aa9-9b99-cc7d06a5c3e3',
            'a3211ffa-65e3-4ed8-8717-92516f9ec7fe',
            '70f9e3b5-9793-4414-9a1e-66864165b15f',
            'b7623f71-a4b4-429f-9a53-240b20deadc3',
            '86e147b5-a343-45f6-a688-7e280db50ef2',
            '19085d8f-5e49-478d-a596-c1725f09af17',
            '4ffa36fb-05f2-4a4e-844c-c24a8153d140',
            'bfec9502-4785-4789-b4e2-28783552abc4',
            '4b150f74-9bbc-448e-9a73-7a3577af095f',
            '0f5908bb-326b-41c0-9845-1999dfcaf0c1',
            'e080e7cd-c0ea-4449-93d9-dbd99e50b8a0',
            '0b903005-e1c5-441e-9f17-325c93ba8edd',
            '6799c72f-9273-456c-a1c5-32899be38689',
            '3ed59941-aeae-4360-bf3f-7d9c92f66389',
            'ab627d0c-53a4-440e-b681-2d710dfa37d7',
            'cf533ad2-ad3b-4f2f-8fd9-6c9e0b609a6e',
            '465ac9e4-620a-4d90-9455-398d08b32b9d',
            '99f2d066-0f30-42cc-adde-566af6dbbef9',
            '0c9e4b3d-437a-448e-ad21-7892e6beef9c',
            '63e0be4b-0873-4bda-aa04-96320fee510a',
            '9e48e35d-2035-4cc9-87e8-11d40ccd280b',
            '7e701353-833e-43bb-bacc-4700e5f3fe1d',
            '2ebf4243-a248-4365-8f10-b319cf422492',
            'deb673c0-d9fe-4063-b690-fde1638154d8',
            'ca63893f-9e24-401f-af90-2469cf1aad5a',
            '52f10c45-f314-4955-a524-45ffc4ad5145',
            'b43e356c-6fcc-4bb4-9472-f27164fb87ca'
        );

        return $uuids[rand(0, count($uuids) - 1)];
    }

    public static function fields($type)
    {
        $price = array(rand(0, 100 * 100), rand(10 * 1000, 500 * 1000));

        // Whenever the first value is less than 100, we will replace it with null
        if ($price[0] < 1000) {
            $price[0] = null;
        }

        $areas = array('150', '200', null, '50', '100', null, '500', '300', null, 111, '120', '001');
        $area = collect($areas)->random(2)->sort(function ($a, $b) {
            return $a > $b ? 1 : -1;
        })->toArray();

        $years = array('2000', '2010', '2050', null, '2030', null, '2040', '2022', null);
        $construction_years = collect($years)->random(2)->sort(function ($a, $b) {
            return $a > $b ? 1 : -1;
        })->toArray();
        $construction_years = array(min($construction_years), max($construction_years));

        $rooms = array(rand(1, 20), rand(20, 50), null);
        $rooms = collect($rooms)->random(2)->sort(function ($a, $b) {
            return $a > $b ? 1 : -1;
        })->toArray();

        $returnActual = array(rand(1, 100), null);

        $heating = array('gas', 'electricity');
        $parking = array(true, false);

        if ($type === 'property') {
            return array(
                'area' => $area[rand(0, 1)],
                'yearOfConstruction' => $years[rand(0, count($years) - 1)],
                'rooms' => $rooms[rand(0, count($rooms) - 1)],
                'heatingType' => $heating[rand(0, 1)],
                'parking' => $parking[rand(0, 1)],
                'returnActual' => $returnActual[rand(0, 1)],
                'price' => $price[rand(0, 1)]
            );
        } elseif ($type === 'search_property') {
            return array(
                'price' => $price,
                'area' => $area,
                'yearOfConstruction' => $construction_years,
                'rooms' => $rooms,
                'returnActual' => $returnActual
            );
        }
    }
}
