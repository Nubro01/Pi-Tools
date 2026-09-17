<?php

declare(strict_types=1);

namespace OCA\CalResourceUI\Settings;

use OCA\CalResourceUI\AppInfo\Application;
use OCA\CalResourceUI\Service\OccService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\Settings\ISettings;
use OCP\Util;

class AdminSettings implements ISettings {
    private const ACTIONS = [
        'building_create', 'story_create', 'room_create', 'vehicle_create',
        'resource_create', 'restrict', 'restriction_create', 'delete',
    ];

    public function __construct(
        private OccService $occ,
        private IRequest $request,
        private IURLGenerator $urlGenerator,
    ) {
    }

    public function getForm(): TemplateResponse {
        Util::addStyle(Application::APP_ID, 'admin');
        Util::addScript(Application::APP_ID, 'admin');

        [$resources, $listStderr] = $this->occ->listResources();

        $feedback = null;
        $crOk = $this->request->getParam('crOk');
        if ($crOk !== null) {
            $feedback = [
                'ok' => $crOk === '1',
                'title' => (string)$this->request->getParam('crTitle', ''),
                'message' => (string)$this->request->getParam('crMsg', ''),
            ];
        }

        $actionUrls = [];
        foreach (self::ACTIONS as $action) {
            $actionUrls[$action] = $this->urlGenerator->linkToRoute(
                'calresourceui.resource.execute',
                ['action' => $action]
            );
        }

        return new TemplateResponse(Application::APP_ID, 'admin', [
            'resources' => $resources,
            'buildings' => $resources['Buildings'] ?? [],
            'stories' => $resources['Stories'] ?? [],
            'listError' => $listStderr,
            'feedback' => $feedback,
            'actionUrls' => $actionUrls,
        ]);
    }

    public function getSection(): string {
        return 'calresourceui';
    }

    public function getPriority(): int {
        return 50;
    }
}
