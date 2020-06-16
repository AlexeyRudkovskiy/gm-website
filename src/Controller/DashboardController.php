<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Form\SettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Inflector\Inflector;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/dashboard")
 */
class DashboardController extends AbstractController
{

    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/settings", name="website_settings")
     */
    public function settings(KernelInterface $kernel, Request $request, TranslatorInterface $translator)
    {
        $path = $kernel->getProjectDir() . '/settings.json';
        $settingsData = json_decode(file_get_contents($path), true);

        $settings = new Settings();
        $this->populateSettings($settings, $settingsData);

        $form = $this->createForm(SettingsType::class, $settings);

        $form = $form->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'button'
            ],
            'label' => $translator->trans('Save')
        ]);

        /** @var FormInterface $form */
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settingsData = $this->settingsToArray($settings);
            file_put_contents($path, json_encode($settingsData));
        }

        return $this->render('dashboard/settings/index.html.twig', [
            'settings' => $settings,
            'form' => $form->createView()
        ]);
    }

    private function populateSettings(Settings $settings, $settingsData)
    {
        foreach ($settingsData as $key => $value) {
            if ($key === 'translations') {
                foreach ($value as $language => $translation) {
                    $translationData = $settings->translate($language);

                    foreach ($translation as $_key => $_value) {
                        $translationData->{$_key} = $_value;
                    }
                }

                $settings->mergeNewTranslations();
            } else {
                $settings->{$key} = $value;
            }
        }

        return $settings;
    }

    private function settingsToArray(Settings $settings): array
    {
        $output = [];
        $translations = $settings->getTranslations();
        $output['translations'] = [];

        foreach ($settings as $key => $value) {
            $output[$key] = $value;
        }

        foreach ($translations as $lang => $translation) {
            $output['translations'][$lang] = [];

            foreach ($translation as $_key => $_value) {
                $output['translations'][$lang][$_key] = $_value;
            }
        }

        return $output;
    }

}
