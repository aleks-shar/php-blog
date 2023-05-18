<?php
declare(strict_types=1);
namespace Blog\Twig;

use Psr\Http\Message\ServerRequestInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    private ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function getFunctions(): array
    {
        return [
          new   TwigFunction('asset_url', [$this, 'getAssetUrl']),
          new   TwigFunction('url', [$this, 'geUrl']),
          new   TwigFunction('base_url', [$this, 'getBaseUrl'])
        ];
    }

    public function getAssetUrl(
        string $path
    ): string {
        return $this->getBaseUrl() . $path;
    }

    public function getBaseUrl(): string
    {
        $params = $this->request->getServerParams();
        return $params['REQUEST_SCHEME'] . '://' . $params['HTTP_HOST'] . '/';
    }

    public function geUrl(
        string $path
    ) : string {
        return $this->getBaseUrl() . $path;
    }
}