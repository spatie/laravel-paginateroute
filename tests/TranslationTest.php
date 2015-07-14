<?php

namespace Spatie\PaginateRoute\Test;

class TranslationTest extends TestCase
{
    /**
     * @var string
     */
    protected $locale = 'nl';

    /**
     * @test
     */
    public function it_translates_the_page_word()
    {
        $response = $this->callRoute('/pagina/2');

        $this->assertNotEmpty($response['models']['data']);
    }

    /**
     * @test
     */
    public function it_doesnt_accept_the_english_word_anymore_when_translated()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $this->call('GET', 'dummies/page/1')->status();
    }
}
