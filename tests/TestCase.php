<?php

namespace Spatie\PaginateRoute\Test;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @var string
     */
    protected $hostName;

    /**
     * @var string
     */
    protected $locale = 'en';

    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->app['paginateroute']->registerMacros();

        $this->hostName = $this->app['config']->get('app.url');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Spatie\PaginateRoute\PaginateRouteServiceProvider',
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.locale', $this->locale);
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => __DIR__.'/database.sqlite',
            'prefix' => '',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        file_put_contents(__DIR__.'/database.sqlite', null);

        $app['db']->connection()->getSchemaBuilder()->create('dummies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        for ($i = 1; $i <= 20; $i++) {
            Dummy::create(['name' => "Dummy {$i}"]);
        }
    }

    protected function registerDefaultRoute($withSides = false)
    {
        $this->app['router']->paginate('dummies', function () use ($withSides) {
            $dummies = $withSides ?  Dummy::paginate(1)->onEachSide(5) : Dummy::paginate(5);
            $paginateRoute = $this->app['paginateroute'];

            return [
                'nextPageUrl' => $paginateRoute->nextPageUrl($dummies),
                'hasPrevious' => $paginateRoute->hasPreviousPage(),
                'previousPageUrl' => $paginateRoute->previousPageUrl(),
                'models' => $dummies->toArray(),
                'hasNext' => $paginateRoute->hasNextPage($dummies),
                'rightPoint' => $paginateRoute->getRightPoint($dummies),
                'leftPoint' => $paginateRoute->getLeftPoint($dummies),
            ];
        });
    }

    /**
     * @param string $route
     * @param array $parameters
     *
     * @return array
     */
    protected function callRoute($route, array $parameters = [])
    {
        return json_decode($this->call('GET', 'dummies'.$route, $parameters)->getContent(), true);
    }
}
