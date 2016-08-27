<?php

use \Mockery as m;
use Aedart\DTO\DataTransferObject;
use Aedart\DTO\Providers\Bootstrap;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\App;

/**
 * Class BootstrapTest
 *
 * @group bootstrap
 * @coversDefaultClass Aedart\DTO\Providers\Bootstrap
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
     * @return m\MockInterface|DataTransferObject
     */
    protected function getDtoMock(){
        return m::mock(DataTransferObject::class)->makePartial();
    }

    /***************************************************************
     * Actual tests
     **************************************************************/

    /**
     * @test
     * @covers ::boot
     * @covers ::setContainer
     * @covers ::getContainer
     * @covers ::getDefaultContainer
     */
    public function canBoot() {
        try {
            Bootstrap::boot();

            $this->assertInstanceOf(Container::class, Bootstrap::getContainer());
        } catch(Exception $e){
            $this->fail('Could not boot; ' . PHP_EOL . $e);
        }
    }

    /**
     * @test
     *
     * @depends canBoot
     */
    public function hasSetFacadeApplication(){
        $this->assertInstanceOf(Container::class, App::getFacadeApplication(), 'App Facade should have a container set');
    }

    /**
     * @test
     *
     * @covers Aedart\DTO\DataTransferObject::container
     */
    public function dtoHasContainerSet(){
        $dto = $this->getDtoMock();

        $this->assertInstanceOf(Container::class, $dto->container(), 'Invalid container on DTO');
    }

    /**
     * @test
     *
     * @covers ::destroy
     *
     * @depends hasSetFacadeApplication
     */
    public function canDestroy(){
        try {
            Bootstrap::destroy();
        } catch(Exception $e){
            $this->fail('Could not destroy; ' . PHP_EOL . $e);
        }
    }

    /**
     * @test
     *
     * @depends canDestroy
     */
    public function hasUnsetFacadeApplication(){
        $this->assertNull(App::getFacadeApplication(), 'Container / Application should be null');
    }

    /**
     * @test
     *
     * @depends hasUnsetFacadeApplication
     *
     * @covers Aedart\DTO\DataTransferObject::container
     */
    public function dtoHasNoContainerSet(){
        $dto = $this->getDtoMock();

        $this->assertNull($dto->container(), 'No container should be available');
    }
}