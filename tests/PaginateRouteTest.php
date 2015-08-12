<?php

namespace Spatie\PaginateRoute\Test;

class PaginateRouteTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_models_without_querying_a_page_number()
    {
        $this->registerDefaultRoute();

        $response = $this->callRoute('/');

        $this->assertNotEmpty($response['models']['data']);
        $this->assertEquals(1, $response['models']['current_page']);
    }

    /**
     * @test
     */
    public function it_returns_the_first_page()
    {
        $this->registerDefaultRoute();

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
     * @group foo
     */
    public function it_returns_the_second_page()
    {
        $this->registerDefaultRoute();

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
        $this->registerDefaultRoute();

        $response = $this->callRoute('/page/3');

        $this->assertTrue($response['hasPrevious']);
        $this->assertEquals($this->hostName.'/dummies/page/2', $response['previousPageUrl']);
    }

    /**
     * @test
     */
    public function it_doesnt_return_a_previous_page_url_on_the_first_page()
    {
        $this->registerDefaultRoute();

        $response = $this->callRoute('/');

        $this->assertFalse($response['hasPrevious']);
    }

    /**
     * @test
     */
    public function it_returns_a_pretty_previous_page_url_for_the_first_page()
    {
        $this->registerDefaultRoute();

        $response = $this->callRoute('/page/2');

        $this->assertTrue($response['hasPrevious']);
        $this->assertEquals($this->hostName.'/dummies', $response['previousPageUrl']);
    }

    /**
     * @test
     */
    public function it_returns_a_next_page_url()
    {
        $this->registerDefaultRoute();

        $response = $this->callRoute('/');

        $this->assertTrue($response['hasNext']);
        $this->assertEquals($this->hostName.'/dummies/page/2', $response['nextPageUrl']);
    }

    /**
     * @test
     */
    public function it_doesnt_return_a_next_page_url_on_the_last_page()
    {
        $this->registerDefaultRoute();

        $response = $this->callRoute('/page/4');

        $this->assertFalse($response['hasNext']);
        $this->assertNull($response['nextPageUrl']);
    }

    /**
     * @test
     */
    public function it_returns_all_urls()
    {
        $this->app['router']->paginate('dummies', function() {
            $dummies = Dummy::paginate(5);
            $paginateRoute = $this->app['paginateroute'];

            return [
                'allUrls' => $this->app['paginateroute']->allUrls($dummies),
                'allUrlsFull' => $this->app['paginateroute']->allUrls($dummies, true),
            ];
        });

        $response = $this->callRoute('/');

        $allUrls = [
            $this->hostName.'/dummies',
            $this->hostName.'/dummies/page/2',
            $this->hostName.'/dummies/page/3',
            $this->hostName.'/dummies/page/4',
        ];

        $this->assertEquals($allUrls, $response['allUrls']);

        $allUrlsFull = $allUrls;
        $allUrlsFull[0] = $this->hostName.'/dummies/page/1';

        $this->assertEquals($allUrlsFull, $response['allUrlsFull']);
    }

    /**
     * @test
     */
    public function it_renders_an_html_list()
    {
        $this->app['router']->paginate('dummies', function() {
            $dummies = Dummy::paginate(5);
            $paginateRoute = $this->app['paginateroute'];

            return [
                'list' => $this->app['paginateroute']->renderPageList($dummies),
            ];
        });

        $firstPage = $this->callRoute('/');

        $expectedForFirstPage = '<ul><li class="active"><a href="http://localhost/dummies">1</a></li><li><a href="http://localhost/dummies/page/2">2</a></li><li><a href="http://localhost/dummies/page/3">3</a></li><li><a href="http://localhost/dummies/page/4">4</a></li></ul>';

        $this->assertEquals($expectedForFirstPage, $firstPage['list']);

        $secondPage = $this->callRoute('/page/2');

        $expectedForSecondPage = '<ul><li><a href="http://localhost/dummies">1</a></li><li class="active"><a href="http://localhost/dummies/page/2">2</a></li><li><a href="http://localhost/dummies/page/3">3</a></li><li><a href="http://localhost/dummies/page/4">4</a></li></ul>';

        $this->assertEquals($expectedForSecondPage, $secondPage['list']);
    }
}
