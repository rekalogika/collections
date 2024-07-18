<?php

declare(strict_types=1);

/*
 * This file is part of rekalogika/collections package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\Collections\Tests\App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Rekalogika\Collections\Tests\App\Factory\CitizenFactory;
use Rekalogika\Collections\Tests\App\Factory\CountryFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // large set: 6060
        // medium set: 1650
        // small set: 30
        // all: 7740


        // large set

        $countrySy = CountryFactory::createOne([
            'name' => 'Syldavia',
            'code' => 'SY',
        ]);

        CitizenFactory::createMany(2020, [
            'country' => $countrySy,
            'age' => random_int(1, 14),
        ]);

        CitizenFactory::createMany(2020, [
            'country' => $countrySy,
            'age' => random_int(16, 63),
        ]);

        CitizenFactory::createMany(2020, [
            'country' => $countrySy,
            'age' => random_int(65, 100),
        ]);

        // medium set

        $countrySt = CountryFactory::createOne([
            'name' => 'San Theodoros',
            'code' => 'ST',
        ]);

        CitizenFactory::createMany(550, [
            'country' => $countrySt,
            'age' => random_int(1, 14),
        ]);

        CitizenFactory::createMany(550, [
            'country' => $countrySt,
            'age' => random_int(16, 63),
        ]);

        CitizenFactory::createMany(550, [
            'country' => $countrySt,
            'age' => random_int(65, 100),
        ]);

        // small set

        $countrySd = CountryFactory::createOne([
            'name' => 'Sondonesia',
            'code' => 'SD',
        ]);

        CitizenFactory::createMany(10, [
            'country' => $countrySd,
            'age' => random_int(1, 14),
        ]);

        CitizenFactory::createMany(10, [
            'country' => $countrySd,
            'age' => random_int(16, 63),
        ]);

        CitizenFactory::createMany(10, [
            'country' => $countrySd,
            'age' => random_int(65, 100),
        ]);

        $manager->flush();
    }
}
