<?php


namespace App\Module\Pages\Controller;


use App\Module\Admin\BaseController;
use App\Module\Pages\Admin\Add;
use App\Module\Pages\Admin\Edit;
use App\Module\Pages\Admin\Index;
use App\Module\Pages\Entities\Items;
use Doctrine\ORM\EntityManager;
use Enjoys\Forms\Renderer\RendererInterface;
use Enjoys\Http\ServerRequestInterface;
use EnjoysCMS\Core\Components\Helpers\Redirect;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class Admin extends BaseController
{

    public function __construct(
        Environment $twig,
        ServerRequestInterface $serverRequest,
        EntityManager $entityManager,
        UrlGeneratorInterface $urlGenerator,
        RendererInterface $renderer
    ) {
        parent::__construct($twig, $serverRequest, $entityManager, $urlGenerator, $renderer);
        $this->twigLoader->addPath(__DIR__ . '/../template', 'pages');
    }

    public function edit()
    {
        return $this->twig->render(
            '@pages/admin/edit.twig',
            (new Edit(
                $this->renderer, $this->entityManager, $this->serverRequest, $this->urlGenerator, $this->twig
            ))->getContext()
        );
    }

    public function delete()
    {
        $item = $this->entityManager->getRepository(Items::class)->find($this->serverRequest->get('id'));
        if($item === null) {
            throw new \InvalidArgumentException('Invalid Arguments');
        }

        $this->entityManager->remove($item);
        $this->entityManager->flush();
        Redirect::http($this->urlGenerator->generate('pages/admin/list'));
    }



    public function list()
    {
        return $this->twig->render(
            '@pages/admin/list.twig',
            (new Index($this->entityManager))->getContext()
        );
    }


    public function add()
    {
        return $this->twig->render(
            '@pages/admin/add.twig',
            (new Add(
                $this->renderer,
                $this->entityManager,
                $this->serverRequest,
                $this->urlGenerator,
                $this->twig
            ))->getContext()
        );
    }
}
