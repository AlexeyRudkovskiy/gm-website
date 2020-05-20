<?php


namespace App\FormTypes;


use App\Entity\StaticPage;
use App\Repository\StaticPageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ExistingEntitiesType extends AbstractType
{

    /** @var StaticPageRepository */
    protected $staticPageRepository;

    /** @var RouterInterface */
    protected $router;

    public function __construct(
        StaticPageRepository $staticPageRepository,
        RouterInterface $router
    )
    {
        $this->staticPageRepository = $staticPageRepository;
        $this->router = $router;
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $staticPages = $this->staticPageRepository->findAll();

        $staticPagesMap = function ($index, StaticPage $staticPage) {
            $url = $this->router->generate('prod_static_page', [ 'slug' => $staticPage->getSlug() ]);
            return [ $staticPage->getTitle() . ' (' . $staticPage->getSlug() . ')', $url ];
        };

        $staticPages = $this->arrayMapAssoc($staticPagesMap, $staticPages);

        $optionsResolver->setDefaults([
            'choices' => [
                'Pages' => $staticPages,
                'Other' => [
                    'Homepage' => $this->router->generate('app_prod_index'),
                    'Configurator' => $this->router->generate('prod_configurator'),
                    'Fleet' => $this->router->generate('prod_fleet')
                ]
            ]
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    private function arrayMapAssoc(callable $f, array $a) {
        return array_column(array_map($f, array_keys($a), $a), 1, 0);
    }



}
