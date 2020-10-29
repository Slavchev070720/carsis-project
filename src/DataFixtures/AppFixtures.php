<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\CarAd;
use App\Entity\Model;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const BASIC_CAR_LOGO = 'car.jpg';

    /**
     * @var array
     */
    private $usersData = [
        [
            'username' => 'Antonio',
            'email' => 'banderas@carsis.com',
            'password' => '12345',
            'sex' => 'Male',
            'city' => 'Kystendil',
            'userImage' => 'antonio.jpg'
        ],
        [
            'username' => 'Kati',
            'email' => 'lunnanosht@carsis.com',
            'password' => '12345',
            'sex' => 'Female',
            'city' => 'Batak',
            'userImage' => 'kati.jpg'
        ],
        [
            'username' => 'Rosie',
            'email' => 'rosi@carsis.com',
            'password' => '12345',
            'sex' => 'Female',
            'city' => 'Sofia',
            'userImage' => 'rosie.jpg'
        ],
        [
            'username' => 'Dakata',
            'email' => 'kruchmarq@carsis.com',
            'password' => '12345',
            'sex' => 'Male',
            'city' => 's.Harmanli',
            'userImage' => 'dakata.jpg'
        ],
        [
            'username' => 'Ivo',
            'email' => 'andonov@carsis.com',
            'password' => '12345',
            'sex' => 'Male',
            'city' => 's.Bogomilovo',
            'userImage' => 'ivo.jpg'
        ]
    ];

    /**
     * @var array
     */
    private $carAdsData = [
        [
            'brand' => 'BMW',
            'model' => 'M6',
            'horsePower' => 450,
            'miliage' => 150000,
            'colour' => 'Red',
            'description' => 'Like a new.',
            'price' => 200000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'BMW',
            'model' => 'X6',
            'horsePower' => 333,
            'miliage' => 999999,
            'colour' => 'Yellow',
            'description' => 'Almost like new.',
            'price' => 175000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'BMW',
            'model' => '550',
            'horsePower' => 260,
            'miliage' => 0,
            'colour' => 'Black',
            'description' => 'Driver seat is not comfortable!',
            'price' => 2000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Audi',
            'model' => 'A8',
            'horsePower' => 111,
            'miliage' => 20000,
            'colour' => 'White',
            'description' => 'Its ok.',
            'price' => 33500,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Audi',
            'model' => 'RS8',
            'horsePower' => 222,
            'miliage' => 80000,
            'colour' => 'Red',
            'description' => 'Like a new.',
            'price' => 60000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Audi',
            'model' => 'TT',
            'horsePower' => 450,
            'miliage' => 222000,
            'colour' => 'Green',
            'description' => 'Almost like a new.',
            'price' => 180000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Ferrari',
            'model' => 'LaFerrari',
            'horsePower' => 999,
            'miliage' => 2000,
            'colour' => 'Black',
            'description' => 'Super car!',
            'price' => 400000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Ferrari',
            'model' => 'Enzo',
            'horsePower' => 280,
            'miliage' => 550000,
            'colour' => 'Black',
            'description' => 'Have 4 tyres.',
            'price' => 200000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Ferrari',
            'model' => 'F50',
            'horsePower' => 450,
            'miliage' => 250000,
            'colour' => 'Pink',
            'description' => 'Awesome colour!',
            'price' => 220000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Ferrari',
            'model' => 'Enzo',
            'horsePower' => 333,
            'miliage' => 2,
            'colour' => 'Yellow',
            'description' => 'Ugly colour',
            'price' => 1000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Lamborghini',
            'model' => 'Diablo',
            'horsePower' => 450,
            'miliage' => 150000,
            'colour' => 'Red',
            'description' => 'Like a new',
            'price' => 200000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Lamborghini',
            'model' => 'Aventador',
            'horsePower' => 400,
            'miliage' => 150000,
            'colour' => 'Black',
            'description' => 'Almost like a new',
            'price' => 290000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Lamborghini',
            'model' => 'Huracane',
            'horsePower' => 450,
            'miliage' => 0,
            'colour' => 'red',
            'description' => 'New!',
            'price' => 190000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Lamborghini',
            'model' => 'Aventador',
            'horsePower' => 220,
            'miliage' => 450000,
            'colour' => 'red',
            'description' => 'Perfect car!',
            'price' => 22500,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Lamborghini',
            'model' => 'Diablo',
            'horsePower' => 350,
            'miliage' => 150000,
            'colour' => 'red',
            'description' => 'Like a new!',
            'price' => 200000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Lamborghini',
            'model' => 'Diablo',
            'horsePower' => 311,
            'miliage' => 35000,
            'colour' => 'Blue',
            'description' => 'Blue lightning!',
            'price' => 111000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Audi',
            'model' => 'RS8',
            'horsePower' => 245,
            'miliage' => 350500,
            'colour' => 'Green',
            'description' => 'Like a new!',
            'price' => 88500,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Audi',
            'model' => 'TT',
            'horsePower' => 231,
            'miliage' => 150000,
            'colour' => 'Red',
            'description' => 'Perfect car.',
            'price' => 55000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Audi',
            'model' => 'TT',
            'horsePower' => 215,
            'miliage' => 220000,
            'colour' => 'White',
            'description' => 'Normal car.',
            'price' => 35000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'Audi',
            'model' => 'A8',
            'horsePower' => 315,
            'miliage' => 240000,
            'colour' => 'Black',
            'description' => 'Normal car.',
            'price' => 34500,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'BMW',
            'model' => 'X6',
            'horsePower' => 215,
            'miliage' => 120000,
            'colour' => 'Black',
            'description' => 'Black ninja.',
            'price' => 99000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'BMW',
            'model' => 'M6',
            'horsePower' => 315,
            'miliage' => 340000,
            'colour' => 'Black',
            'description' => 'Nice car.',
            'price' => 88000,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'BMW',
            'model' => 'M6',
            'horsePower' => 300,
            'miliage' => 560000,
            'colour' => 'Black',
            'description' => 'Like a new!.',
            'price' => 66500,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'BMW',
            'model' => 'X6',
            'horsePower' => 256,
            'miliage' => 340000,
            'colour' => 'Black',
            'description' => 'Perfect car!',
            'price' => 55500,
            'image' => self::BASIC_CAR_LOGO
        ],
        [
            'brand' => 'BMW',
            'model' => 'X6',
            'horsePower' => 399,
            'miliage' => 1200000,
            'colour' => 'Black',
            'description' => 'I drove it a bit.',
            'price' => 30250,
            'image' => self::BASIC_CAR_LOGO
        ]
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $userRepository = $manager->getRepository(User::class);
        $brandRepository = $manager->getRepository(Brand::class);
        $modelRepository = $manager->getRepository(Model::class);

        $start = 0;
        foreach ($this->usersData as $userDatum) {
            if ($userRepository->findOneBy(['username' => $userDatum['username']]) === null) {
                $user = new User($userDatum);
                for ($i = $start; $i < $start + 5; $i++) {
                    $carAd = (new CarAd())
                        ->setBrand($brandRepository->findOneBy(['brandName' => $this->carAdsData[$i]['brand']]))
                        ->setModel($modelRepository->findOneBy(['modelName' => $this->carAdsData[$i]['model']]))
                        ->setHorsePower($this->carAdsData[$i]['horsePower'])
                        ->setMiliage($this->carAdsData[$i]['miliage'])
                        ->setColour($this->carAdsData[$i]['colour'])
                        ->setDescription($this->carAdsData[$i]['description'])
                        ->setPrice($this->carAdsData[$i]['price'])
                        ->setImage($this->carAdsData[$i]['image']);
                    $user->addCarAd($carAd);
                }
                $manager->persist($user);
                $start += 5;
            }
        }
        $manager->flush();
    }
}
