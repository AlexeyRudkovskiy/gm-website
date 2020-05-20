<?php


namespace App\Services;



use App\Entity\PageMetaTags;
use App\Repository\ContactRequestRepository;
use App\Repository\PageMetaTagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;


class DashboardService
{

    /** @var string */
    private $settingsPath;

    /** @var int  */
    private $productsCount = -1;

    /** @var int  */
    private $ordersCount = -1;

    /** @var int  */
    private $unprocessedOrdersCount = -1;

    /** @var int  */
    private $categoriesCount = -1;

    /** @var array */
    private $settings = null;

    /** @var TranslatorInterface */
    protected $translator = null;

    /** @var ContactRequestRepository */
    protected $contactRequestRepository = null;

    /** @var null PageMetaTagsRepository */
    protected $pageMetaTagsRepository = null;

    /** @var RequestStack */
    protected $requestStack;

    /** @var PageMetaTags|null */
    private $pageMetaTags = -1;

    /** @var Security */
    private $security;

    public function __construct(
        ContactRequestRepository $contactRequestRepository,
        PageMetaTagsRepository $pageMetaTagsRepository,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        Security $security,
        string $settingsPath
    )
    {
        $this->translator = $translator;
        $this->settingsPath = $settingsPath;
        $this->contactRequestRepository = $contactRequestRepository;
        $this->pageMetaTagsRepository = $pageMetaTagsRepository;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function getSidebar()
    {
        return [
            (new SidebarSection())
                ->setSectionName($this->translator->trans('Entities'))
                ->addItem('product_index', $this->translator->trans('Products'), 0, [ 'product_new', 'product_edit', 'product_show', 'ROLE_USER' ])
                ->addItem('project_index', $this->translator->trans('Projects'), 0, [ 'project_new', 'project_edit', 'project_show', 'ROLE_USER' ])
                ->addItem('partner_index', $this->translator->trans('Partners'), 0, [ 'partner_new', 'partner_edit', 'partner_show', 'ROLE_USER' ])

                ->filter($this->security)
            ,
            (new SidebarSection())
                ->setSectionName($this->translator->trans('Settings'))
                ->addItem('page_meta_tags_index', $this->translator->trans('Meta Tags'), 0, [ 'page_meta_tags_new', 'page_meta_tags_show', 'page_meta_tags_edit' ], 'ROLE_SEO')
                ->addItem('static_page_index', $this->translator->trans('Static Pages'), 0, [ 'static_page_new', 'static_page_show', 'static_page_edit' ], 'ROLE_SEO')
                ->addItem('user_index', $this->translator->trans('Users'), 0, ['user_show', 'user_edit', 'user_new'], 'ROLE_ADMIN')
                ->addItem('website_settings', $this->translator->trans('Settings'), 0, ['website_settings'], 'ROLE_ADMIN')

                ->filter($this->security)
            ,
        ];
    }

    public function getSettings()
    {
        if ($this->settings === null) {
            $this->settings = json_decode(file_get_contents($this->settingsPath), true);
        }

        return $this->settings;
    }

    public function getSetting(string $path)
    {
        $keys = explode('.', $path);
        $value = $this->getSettings();

        foreach ($keys as $key) {
            if (!array_key_exists($key, $value)) {
                return null;
            }

            $value = $value[$key];
        }

        return $value;
    }

    public function getMetaTags(): Collection
    {
        $pageMetaTags = $this->getPageMetaTags();
        if ($pageMetaTags !== null) {
            return $pageMetaTags->getMetaTags();
        }

        return new ArrayCollection();
    }

    public function getPageMetaTags(): ?PageMetaTags
    {
        if ($this->pageMetaTags !== -1) {
            return $this->pageMetaTags;
        }

        $request = $this->requestStack->getCurrentRequest();
        $requestUri = $request->getRequestUri();

        $pageMetaTags = $this->pageMetaTagsRepository->findByUrl($requestUri);

        if (!empty($pageMetaTags)) {
            $page = array_shift($pageMetaTags);
            $this->pageMetaTags = $page;
        } else {
            $this->pageMetaTags = null;
        }

        return $this->pageMetaTags;
    }

    private function getContactRequestsCount() {
        return $this->contactRequestRepository->getNewRequestsCount();
    }

}
