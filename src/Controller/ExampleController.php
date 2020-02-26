<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DeepConfig;

class ExampleController extends AbstractController
{
    /**
     * @Route("/", name="example")
     */
    public function index(DeepConfig $config)
    {
        $level1 = $config->get('level1');
        $level1_foo = $config->get('level1.foo');
        $level1_foo_foo1 = $config->get('level1.foo.foo1');
        $level1_foo_foo2_foo3 = $config->get('level1.foo.foo2.foo3');

        $level2 = $config->get('second.level2');
        $level2_foo = $config->get('second.level2.foo');
        $level2_foo_foo1 = $config->get('second.level2.foo.foo1');
        $level2_foo_foo2_foo3 = $config->get('second.level2.foo.foo2.foo3');

        $level3 = $config->get('second.third.level3');
        $level3_foo = $config->get('second.third.level3.foo');
        $level3_foo_foo1 = $config->get('second.third.level3.foo.foo1');
        $level3_foo_foo2_foo3 = $config->get('second.third.level3.foo.foo2.foo3');

        return $this->render('example.html.twig', [
            'level1' => $level1,
            'level1_foo' => $level1_foo,
            'level1_foo_foo1' => $level1_foo_foo1,
            'level1_foo_foo2_foo3' => $level1_foo_foo2_foo3,

            'level2' => $level2,
            'level2_foo' => $level2_foo,
            'level2_foo_foo1' => $level2_foo_foo1,
            'level2_foo_foo2_foo3' => $level2_foo_foo2_foo3,
            
            'level3' => $level3,
            'level3_foo' => $level3_foo,
            'level3_foo_foo1' => $level3_foo_foo1,
            'level3_foo_foo2_foo3' => $level3_foo_foo2_foo3
        ]);
    }
}