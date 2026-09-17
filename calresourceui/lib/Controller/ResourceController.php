<?php

declare(strict_types=1);

namespace OCA\CalResourceUI\Controller;

use OCA\CalResourceUI\Service\OccService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\AdminRequired;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\IRequest;
use OCP\IURLGenerator;

class ResourceController extends Controller {
    public function __construct(
        string $appName,
        IRequest $request,
        private OccService $occ,
        private IURLGenerator $urlGenerator,
    ) {
        parent::__construct($appName, $request);
    }

    private function boolFlag(string $name): string {
        return $this->request->getParam($name) === '1' ? '1' : '0';
    }

    private function req(string $name): string {
        return trim((string)$this->request->getParam($name, ''));
    }

    private function opt(string $name): ?string {
        $v = trim((string)$this->request->getParam($name, ''));
        return $v === '' ? null : $v;
    }

    #[AdminRequired]
    public function execute(string $action): RedirectResponse {
        $args = null;
        $title = $action;
        $preError = null;

        switch ($action) {
            case 'building_create':
                $args = ['calendar-resource:building:create'];
                if (($a = $this->opt('address')) !== null) { $args[] = '--address=' . $a; }
                if (($d = $this->opt('description')) !== null) { $args[] = '--description=' . $d; }
                $args[] = '--wheelchair-accessible=' . $this->boolFlag('wheelchair_accessible');
                $args[] = $this->req('display_name');
                $title = 'Building aanmaken';
                break;

            case 'story_create':
                $args = ['calendar-resource:story:create', $this->req('building_id'), $this->req('display_name')];
                $title = 'Story (verdieping) aanmaken';
                break;

            case 'room_create':
                $args = ['calendar-resource:room:create'];
                if (($v = $this->opt('contact_person')) !== null) { $args[] = '--contact-person-user-id=' . $v; }
                if (($v = $this->opt('capacity')) !== null) { $args[] = '--capacity=' . $v; }
                if (($v = $this->opt('room_number')) !== null) { $args[] = '--room-number=' . $v; }
                $args[] = '--has-phone=' . $this->boolFlag('has_phone');
                $args[] = '--has-video-conferencing=' . $this->boolFlag('has_video_conferencing');
                $args[] = '--has-tv=' . $this->boolFlag('has_tv');
                $args[] = '--has-projector=' . $this->boolFlag('has_projector');
                $args[] = '--has-whiteboard=' . $this->boolFlag('has_whiteboard');
                $args[] = '--wheelchair-accessible=' . $this->boolFlag('wheelchair_accessible');
                $args[] = $this->req('story_id');
                $args[] = $this->req('uid');
                $args[] = $this->req('display_name');
                $args[] = $this->req('email');
                $args[] = $this->req('room_type');
                $title = 'Room aanmaken';
                break;

            case 'vehicle_create':
                $args = ['calendar-resource:vehicle:create'];
                if (($v = $this->opt('contact_person')) !== null) { $args[] = '--contact-person-user-id=' . $v; }
                $args[] = '--is-electric=' . $this->boolFlag('is_electric');
                if (($v = $this->opt('range')) !== null) { $args[] = '--range=' . $v; }
                if (($v = $this->opt('seating_capacity')) !== null) { $args[] = '--seating-capacity=' . $v; }
                $args[] = $this->req('uid');
                $args[] = $this->req('building_id');
                $args[] = $this->req('display_name');
                $args[] = $this->req('email');
                $args[] = $this->req('vehicle_type');
                $args[] = $this->req('vehicle_make');
                $args[] = $this->req('vehicle_model');
                $title = 'Vehicle aanmaken';
                break;

            case 'resource_create':
                $args = ['calendar-resource:resource:create'];
                if (($v = $this->opt('contact_person')) !== null) { $args[] = '--contact-person-user-id=' . $v; }
                $args[] = $this->req('uid');
                $args[] = $this->req('building_id');
                $args[] = $this->req('display_name');
                $args[] = $this->req('email');
                $args[] = $this->req('resource_type');
                $title = 'Resource (generiek) aanmaken';
                break;

            case 'restrict':
                $entityType = $this->req('entity_type');
                if (!in_array($entityType, ['room', 'vehicle', 'resource'], true)) {
                    $preError = 'Ongeldig entity_type';
                    break;
                }
                $args = ['calendar-resource:restrict', $entityType, $this->req('entity_id'), $this->boolFlag('restricted')];
                $title = 'Restricted-vlag zetten';
                break;

            case 'restriction_create':
                $entityType = $this->req('entity_type');
                if (!in_array($entityType, ['room', 'vehicle', 'resource'], true)) {
                    $preError = 'Ongeldig entity_type';
                    break;
                }
                $args = ['calendar-resource:restriction:create', $entityType, $this->req('entity_id'), $this->req('group_id')];
                $title = 'Restriction aanmaken';
                break;

            case 'delete':
                $type = $this->req('type');
                if (!in_array($type, ['building', 'story', 'room', 'vehicle', 'resource', 'restriction'], true)) {
                    $preError = 'Ongeldig type';
                    break;
                }
                $args = ['calendar-resource:resource:delete', $type, $this->req('resource_id')];
                $title = 'Verwijderen';
                break;

            default:
                $preError = 'Onbekende actie: ' . $action;
        }

        if ($preError !== null) {
            $ok = false;
            $message = $preError;
        } else {
            [$stdout, $stderr, $exit] = $this->occ->run($args);
            $ok = $exit === 0;
            $message = trim($stdout . "\n" . $stderr);
        }

        $url = $this->urlGenerator->linkToRoute('settings.AdminSettings.index', ['section' => 'calresourceui'])
            . '?crOk=' . ($ok ? '1' : '0')
            . '&crTitle=' . rawurlencode($title)
            . '&crMsg=' . rawurlencode($message);

        return new RedirectResponse($url);
    }
}
