<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Faker\Factory;

class CustomerSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('customers');

        $faker = Factory::create();

        Customer::truncate();
        Address::truncate();

        $customer = Customer::create([
            'name'     => 'John Smith',
            'email'    => 'john.smith@botble.com',
            'password' => bcrypt('12345678'),
            'phone'    => $faker->e164PhoneNumber,
            'avatar'   => 'customers/1.jpg',
        ]);

        Address::create([
            'name'        => $customer->name,
            'phone'       => $faker->e164PhoneNumber,
            'email'       => $customer->email,
            'country'     => $faker->countryCode,
            'state'       => $faker->state,
            'city'        => $faker->city,
            'address'     => $faker->streetAddress,
            'zip_code'    => $faker->postcode,
            'customer_id' => $customer->id,
            'is_default'  => true,
        ]);

        Address::create([
            'name'        => $customer->name,
            'phone'       => $faker->e164PhoneNumber,
            'email'       => $customer->email,
            'country'     => $faker->countryCode,
            'state'       => $faker->state,
            'city'        => $faker->city,
            'address'     => $faker->streetAddress,
            'zip_code'    => $faker->postcode,
            'customer_id' => $customer->id,
            'is_default'  => false,
        ]);

        for ($i = 0; $i < 10; $i++) {
            $customer = Customer::create([
                'name'     => $faker->name,
                'email'    => $faker->safeEmail,
                'password' => bcrypt('12345678'),
                'phone'    => $faker->e164PhoneNumber,
                'avatar'   => 'customers/' . ($i + 1) . '.jpg',
            ]);

            Address::create([
                'name'        => $customer->name,
                'phone'       => $faker->e164PhoneNumber,
                'email'       => $customer->email,
                'country'     => $faker->countryCode,
                'state'       => $faker->state,
                'city'        => $faker->city,
                'address'     => $faker->streetAddress,
                'zip_code'    => $faker->postcode,
                'customer_id' => $customer->id,
                'is_default'  => true,
            ]);
        }
    }
}
