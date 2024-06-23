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
        $countrySd = CountryFactory::createOne([
            'name' => 'Sondonesia',
            'code' => 'SD',
        ]);
        CitizenFactory::createMany(10, [
            'country' => $countrySd,
        ]);

        $countrySt = CountryFactory::createOne([
            'name' => 'San Theodoros',
            'code' => 'ST',
        ]);
        CitizenFactory::createMany(550, [
            'country' => $countrySt,
        ]);

        $countrySy = CountryFactory::createOne([
            'name' => 'Syldavia',
            'code' => 'SY',
        ]);
        CitizenFactory::createMany(2050, [
            'country' => $countrySy,
        ]);

        $manager->flush();
    }
}
