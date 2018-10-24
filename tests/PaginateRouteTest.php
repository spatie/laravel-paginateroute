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
        $this->app['router']->paginate('dummies', function () {
            $dummies = Dummy::paginate(5);
            $paginateRoute = $this->app['paginateroute'];

            return [
                'allUrls' => $this->app['paginateroute']->allUrls($dummies),
                'allUrlsFull' => $this->app['paginateroute']->allUrls($dummies, true),
            ];
        });

        $response = $this->callRoute('/');

        $allUrls = [
            1 => $this->hostName.'/dummies',
            2 => $this->hostName.'/dummies/page/2',
            3 => $this->hostName.'/dummies/page/3',
            4 => $this->hostName.'/dummies/page/4',
        ];

        $this->assertEquals($allUrls, $response['allUrls']);

        $allUrlsFull = $allUrls;
        $allUrlsFull[1] = $this->hostName.'/dummies/page/1';

        $this->assertEquals($allUrlsFull, $response['allUrlsFull']);
    }

    /**
     * @test
     */
    public function it_returns_limited_urls_with_on_each_side()
    {
        $this->app['router']->paginate('dummies', function () {
            // We only have 20 dummy but needs 11 page
            // limit 1 dummy per page
            $dummies = Dummy::paginate(1)->onEachSide(5);
            $paginateRoute = $this->app['paginateroute'];

            return [
                'allUrls' => $paginateRoute->allUrls($dummies),
                'allUrlsFull' => $paginateRoute->allUrls($dummies, true),
            ];
        });

        $response = $this->callRoute('/');

        // 5 items plus 5 on the side and 1 current item = 11 items visible
        $allUrls = [
            1  => $this->hostName.'/dummies',
            2  => $this->hostName.'/dummies/page/2',
            3  => $this->hostName.'/dummies/page/3',
            4  => $this->hostName.'/dummies/page/4',
            5  => $this->hostName.'/dummies/page/5',
            6  => $this->hostName.'/dummies/page/6',
            7  => $this->hostName.'/dummies/page/7',
            8  => $this->hostName.'/dummies/page/8',
            9  => $this->hostName.'/dummies/page/9',
            10 => $this->hostName.'/dummies/page/10',
            11 => $this->hostName.'/dummies/page/11',
        ];

        $this->assertEquals($allUrls, $response['allUrls']);

        $allUrlsFull = $allUrls;
        $allUrlsFull[1] = $this->hostName.'/dummies/page/1';

        $this->assertEquals($allUrlsFull, $response['allUrlsFull']);
    }

    /**
     * @test
     */
    public function it_return_zero_on_left_and_ten_on_right_with_on_each_side()
    {
        $this->registerDefaultRoute(true);

        $response = $this->callRoute('/page/1');

        $this->assertEquals($response['leftPoint'], 1);
        $this->assertEquals($response['rightPoint'], 11);
    }

    /**
     * @test
     */
    public function it_return_five_on_left_and_five_on_right_with_on_each_side()
    {
        $this->registerDefaultRoute(true);
        $response = $this->callRoute('/page/6');

        $this->assertEquals($response['leftPoint'], 1);
        $this->assertEquals($response['rightPoint'], 11);
    }

    /**
     * @test
     */
    public function it_return_ten_on_left_and_zero_on_right_with_on_each_side()
    {
        $this->registerDefaultRoute(true);

        // The test dummy is defined with 20 entries @ 1 entry per page = 20 page.
        $end = 20;

        // We set the onEachSide to have 5 pages on left and right thus 10 additional page plus the current page
        $perpage = 5 * 2;
        $response = $this->callRoute('/page/'.$end);

        $this->assertEquals($response['leftPoint'], $end - $perpage);
        $this->assertEquals($response['rightPoint'], $end);
    }

    /**
     * @test
     */
    public function it_renders_an_html_list()
    {
        $this->app['router']->paginate('dummies', function () {
            $dummies = Dummy::paginate(5);
            $paginateRoute = $this->app['paginateroute'];

            return [
                'list' => $paginateRoute->renderPageList($dummies),
                'listClass' => $paginateRoute->renderPageList($dummies, false, 'pagination'),
            ];
        });

        $firstPage = $this->callRoute('/');

        $expectedForFirstPage = '<ul><li class="active">1</li><li><a href="http://localhost/dummies/page/2">2</a></li><li><a href="http://localhost/dummies/page/3">3</a></li><li><a href="http://localhost/dummies/page/4">4</a></li></ul>';

        $this->assertEquals($expectedForFirstPage, $firstPage['list']);

        $secondPage = $this->callRoute('/page/2');

        $expectedForSecondPage = '<ul><li><a href="http://localhost/dummies">1</a></li><li class="active">2</li><li><a href="http://localhost/dummies/page/3">3</a></li><li><a href="http://localhost/dummies/page/4">4</a></li></ul>';

        $this->assertEquals($expectedForSecondPage, $secondPage['list']);

        $expectedForFirstPageWithClass = '<ul class="pagination"><li class="active">1</li><li><a href="http://localhost/dummies/page/2">2</a></li><li><a href="http://localhost/dummies/page/3">3</a></li><li><a href="http://localhost/dummies/page/4">4</a></li></ul>';
    }

    /** @test */
    public function it_adds_query_string_to_page_urls()
    {
        $this->registerDefaultRoute();

        $response = $this->callRoute('/page/2', ['test' => 123]);

        $this->assertTrue($response['hasNext']);
        $this->assertEquals($this->hostName.'/dummies/page/3?test=123', $response['nextPageUrl']);
        $this->assertTrue($response['hasPrevious']);
        $this->assertEquals($this->hostName.'/dummies?test=123', $response['previousPageUrl']);

        $fullPreviousUrl = $this->app['paginateroute']->previousPageUrl(true);
        $this->assertEquals($this->hostName.'/dummies/page/1?test=123', $fullPreviousUrl);
    }
}
