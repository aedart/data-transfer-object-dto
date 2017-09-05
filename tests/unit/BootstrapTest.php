<?php

use Aedart\DTO\DataTransferObject;
use Aedart\DTO\Providers\Bootstrap;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\App;
use Aedart\Testing\TestCases\Unit\UnitTestCase;

/**
 * Class BootstrapTest
 *
 * @group bootstrap
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class BootstrapTest extends UnitTestCase
{

    /***************************************************************
     * Utilities and helpers
     **************************************************************/

    /**
     * Get a Dto mock
     *
     * @return DataTransferObject
     */
    protected function getDto()
    {
        return new City();
    }

    /***************************************************************
     * Actual tests
     **************************************************************/

    /**
     * @test
     */
    public function canBoot()
    {
        try {
            Bootstrap::boot();

            $this->assertInstanceOf(Container::class, Bootstrap::getContainer());
        } catch (Exception $e) {
            $this->fail('Could not boot; ' . PHP_EOL . $e);
        }
    }

    /**
     * @test
     */
    public function hasSetFacadeApplication()
    {
        $this->assertInstanceOf(Container::class, App::getFacadeApplication(),
            'App Facade should have a container set');
    }

    /**
     * @test
     */
    public function dtoHasContainerSet()
    {
        $dto = $this->getDto();

        $this->assertInstanceOf(Container::class, $dto->container(), 'Invalid container on DTO');
    }

    /**
     * @test
     *
     * @depends hasSetFacadeApplication
     */
    public function canDestroy()
    {
        try {
            Bootstrap::destroy();
        } catch (Exception $e) {
            $this->fail('Could not destroy; ' . PHP_EOL . $e);
        }
    }

    /**
     * @test
     *
     * @depends canDestroy
     */
    public function hasUnsetFacadeApplication()
    {
        $this->assertNull(App::getFacadeApplication(), 'Container / Application should be null');
    }

    /**
     * @test
     *
     * @depends hasUnsetFacadeApplication
     */
    public function dtoHasNoContainerSet()
    {
        $dto = $this->getDto();

        $this->assertNull($dto->container(), 'No container should be available');
    }
}