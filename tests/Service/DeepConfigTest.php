<?php

namespace App\Tests\Service;

use App\Service\DeepConfig;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DeepConfigRepository;

class DeepConfigTest extends TestCase
{
    public function testReturnsNothing()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(DeepConfigRepository::class);
        $projectDir = '';
        $useDatabase = false;
        $deepConfig = new DeepConfig($projectDir, $useDatabase, $repository, $em);
        $result = $deepConfig->get('foo');

        $this->assertNull($result);
    }

    public function testReturnsValue()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(DeepConfigRepository::class);
        $projectDir = '';
        $useDatabase = false;
        $deepConfig = new DeepConfig($projectDir, $useDatabase, $repository, $em);

        $testArray = [
            'foo' => [
                'foo1' => 'bar1',
                'foo2' => [
                    'foo3' => 'bar3',
                    'foo4' => 'bar4'
                ]
            ]
        ];

        $key = 'foo.foo2.foo4';

        $keys = explode('.', $key);
        $result = $deepConfig->findValue($keys, $testArray);

        $this->assertEquals('bar4', $result);
    }

    public function testReturnsArray()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(DeepConfigRepository::class);
        $projectDir = '';
        $useDatabase = false;
        $deepConfig = new DeepConfig($projectDir, $useDatabase, $repository, $em);

        $testArray = [
            'foo' => [
                'foo1' => 'bar1',
                'foo2' => [
                    'foo3' => 'bar3',
                    'foo4' => 'bar4'
                ]
            ]
        ];

        $key = 'foo.foo2';

        $keys = explode('.', $key);
        $result = $deepConfig->findValue($keys, $testArray);

        $this->assertInternalType('array', $result);
    }
}