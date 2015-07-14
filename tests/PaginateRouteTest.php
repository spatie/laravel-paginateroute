<?php

namespace Spatie\PaginateRoute\Test;

class PaginateRouteTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_models_without_querying_a_page_number()
    {
        $response = $this->callRoute('/');

        $this->assertNotEmpty($response['models']['data']);
        $this->assertEquals(1, $response['models']['current_page']);
    }

    /**
     * @test
     */
    public function it_returns_the_first_page()
    {
        $response = $this->callRoute('/page/1');

        $models = $response['models']['data'];

        $this->assertNotEmpty($models);
        $this->assertContains(['id' => '1', 'name' => 'Dummy 1'], $models);
        $this->assertContains(['id' => '5', 'name' => 'Dummy 5'], $models);
        $this->assertNotContains(['id' => '6', 'name' => 'Dummy 6'], $models);

        $this->assertEquals(1, $response['models']['current_page']);
    }

    /**
     * @test
     */
    public function it_returns_the_second_page()
    {
        $response = $this->callRoute('/page/2');

        $models = $response['models']['data'];

        $this->assertNotEmpty($models);
        $this->assertNotContains(['id' => '5', 'name' => 'Dummy 5'], $models);
        $this->assertContains(['id' => '6', 'name' => 'Dummy 6'], $models);
        $this->assertContains(['id' => '10', 'name' => 'Dummy 10'], $models);
        $this->assertNotContains(['id' => '11', 'name' => 'Dummy 11'], $models);

        $this->assertEquals(2, $response['models']['current_page']);
    }

    /**
     * @test
     */
    public function it_returns_a_previous_page_url()
    {
        $response = $this->callRoute('/page/3');

        $this->assertTrue($response['hasPrevious']);
        $this->assertEquals($this->hostName.'/dummies/page/2', $response['previousPageUrl']);
    }

    /**
     * @test
     */
    public function it_doesnt_return_a_previous_page_url_on_the_first_page()
    {
        $response = $this->callRoute('/');

        $this->assertFalse($response['hasPrevious']);
    }

    /**
     * @test
     */
    public function it_returns_a_pretty_previous_page_url_for_the_first_page()
    {
        $response = $this->callRoute('/page/2');

        $this->assertTrue($response['hasPrevious']);
        $this->assertEquals($this->hostName.'/dummies', $response['previousPageUrl']);
    }

    /**
     * @test
     */
    public function it_returns_a_next_page_url()
    {
        $response = $this->callRoute('/');

        $this->assertTrue($response['hasNext']);
        $this->assertEquals($this->hostName.'/dummies/page/2', $response['nextPageUrl']);
    }

    /**
     * @test
     */
    public function it_doesnt_return_a_next_page_url_on_the_last_page()
    {
        $response = $this->callRoute('/page/4');

        $this->assertFalse($response['hasNext']);
        $this->assertNull($response['nextPageUrl']);
    }
}
