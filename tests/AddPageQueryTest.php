<?php

namespace Spatie\PaginateRoute\Test;

class AddPageQueryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_append_regular_page_queries_to_the_url()
    {
        $url = 'http://app.dev/blog';

        for ($page = 2; $page <= 10; ++$page) {
            $this->assertEquals("$url/page/{$page}", $this->app['paginateroute']->addPageQuery($url, $page));
        }
    }

    /**
     * @test
     */
    public function it_doesnt_append_a_query_for_the_first_page()
    {
        $url = 'http://app.dev/blog';
        $page = 1;

        $this->assertEquals($url, $this->app['paginateroute']->addPageQuery($url, $page));
    }

    /**
     * @test
     */
    public function it_appends_a_query_for_the_first_page_if_a_full_url_is_requested()
    {
        $url = 'http://app.dev/blog';
        $page = 1;

        $this->assertEquals("$url/page/{$page}", $this->app['paginateroute']->addPageQuery($url, $page, true));
    }
}
