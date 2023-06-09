<?php
declare(strict_types=1);

use Blog\Database;
use Blog\LatestPosts;
use Blog\Slim\TwigMiddleware;
use DevCoder\DotEnv;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use \Twig\Loader\FilesystemLoader;
use Blog\PostMapper;

require __DIR__ . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions('config/di.php');
(new DotEnv(__DIR__ . '/.env'))->load();
$container = $builder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();

$view = $container->get(Environment::class);
$app->add(new TwigMiddleware($view));

$connection = $container->get(Database::class)->getConnection();

$app->get('/', function (Request $request, Response $response, $args) use ($view, $connection){
    $latestPosts = new LatestPosts($connection);
    $posts = $latestPosts->get(2);
    $body = $view->render('index.twig', [
        'posts' => $posts,
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/about', function (Request $request, Response $response, $args) use($view){
    $body = $view->render('about.twig');
    $response->getBody()->write($body);
    return $response;
});


$app->get('/blog[/{page}]', function (Request $request, Response $response, $args) use ($view, $connection){
    $postMapper = new PostMapper($connection);

    $page = isset($args['page']) ? (int) $args['page'] : 1;
    $limit = 2;

    $posts = $postMapper->getList($page, $limit, 'DESC');

    $totalCount = $postMapper->getTotalCount();
    $body = $view->render('blog.twig', [
        'posts' => $posts,
        'pagination' => [
            'current' => $page,
            'paging' => ceil($totalCount / $limit),
        ],
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/{url_key}', function (Request $request, Response $response, $args) use($view, $connection){
    $postMapper = new PostMapper($connection);
    $post = $postMapper->getByUrlKey((string) $args['url_key']);
    if(empty($post))
    {
        $body = $view->render('not-found.twig');
    }else {
        $body = $view->render('post.twig', [
            'post' => $post,
        ]);
    }
    $response->getBody()->write($body);
    return $response;
});

$app->run();