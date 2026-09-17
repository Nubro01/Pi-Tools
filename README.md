# Pi tools

Kleine, zelfstandige beheertools voor de gedeelde Raspberry Pi (`NLAMDB00`) die
`wethepeopleofthe.eu`, drie Nextcloud-instanties en diverse andere sites host.
Elke tool staat in zijn eigen submap.

## calresourceui

Installeerbare Nextcloud-app die een "Calendar Resources"-sectie toevoegt onder
Instellingen -> Beheer, met formulieren voor de `calendar_resource_management`
app (buildings, stories, rooms, vehicles, resources, restrictions) - die app
heeft zelf alleen `occ`-commando's, geen UI.

Live gedeployed op alle drie de Nextcloud-instanties:
- `nextcloud.nl1106.eu/apps/calresourceui`
- `nextcloud.nubergict.eu/apps/calresourceui`
- `nextcloud.platform1106.nl/apps/calresourceui`

Draait op elke instantie ongewijzigd (geen per-instantie configuratie nodig -
lost zijn eigen `occ`-pad op via `\OC::$SERVERROOT`). Om te deployen naar een
nieuwe instantie: kopieer de map naar `<nextcloud-root>/apps/calresourceui/`
en draai `occ app:enable calresourceui`.
