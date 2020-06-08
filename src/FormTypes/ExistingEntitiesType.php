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

        $optionsResolver->setDefaults([
            'choices' => [
                'Other' => [
                    'Homepage' => $this->router->generate('index'),
                    'Homepage (russian)' => $this->router->generate('index', [ '_locale' => 'ru' ]),
                    'Homepage (german)' => $this->router->generate('index', [ '_locale' => 'de' ])
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
