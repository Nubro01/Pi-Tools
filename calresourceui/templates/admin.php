<?php
/** @var array $_ */

function cr_options(array $rows, string $idKey, string $labelKey): void {
    foreach ($rows as $row) {
        $id = htmlspecialchars((string)($row[$idKey] ?? ''), ENT_QUOTES);
        $label = htmlspecialchars((string)($row[$labelKey] ?? ''), ENT_QUOTES);
        echo '<option value="' . $id . '">' . $id . ' - ' . $label . '</option>';
    }
}
?>
<div id="calresourceui" class="calresourceui section">
    <h2><?php p($l->t('Calendar Resources')); ?></h2>
    <p class="settings-hint"><?php p($l->t('Beheer buildings, stories, rooms, vehicles en resources voor de Calendar Resource Management-app.')); ?></p>

    <?php if ($_['feedback']): ?>
        <div class="cr-feedback <?php p($_['feedback']['ok'] ? 'ok' : 'err'); ?>">
            <h3><?php p(($_['feedback']['ok'] ? '✓ ' : '✗ ') . $_['feedback']['title']); ?></h3><?php p($_['feedback']['message'] !== '' ? $_['feedback']['message'] : ($_['feedback']['ok'] ? 'OK' : 'Onbekende fout')); ?>
        </div>
    <?php endif; ?>

    <details class="cr-panel" open>
        <summary><?php p($l->t('Overzicht')); ?></summary>
        <div class="cr-body">
            <?php if (!empty($_['listError'])): ?>
                <div class="cr-feedback err"><?php p($_['listError']); ?></div>
            <?php endif; ?>
            <?php $any = false; foreach ($_['resources'] as $section => $rows): ?>
                <?php if (empty($rows)) continue; ?>
                <?php $any = true; ?>
                <table class="cr-table">
                    <caption><?php p($section); ?></caption>
                    <thead><tr><?php foreach (array_keys($rows[0]) as $h): ?><th><?php p($h); ?></th><?php endforeach; ?></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr><?php foreach ($row as $v): ?><td><?php p($v); ?></td><?php endforeach; ?></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
            <?php if (!$any): ?>
                <p class="cr-hint"><?php p($l->t('Nog geen resources.')); ?></p>
            <?php endif; ?>
        </div>
    </details>

    <details class="cr-panel">
        <summary><?php p($l->t('Building aanmaken')); ?></summary>
        <div class="cr-body">
            <form method="post" action="<?php p($_['actionUrls']['building_create']); ?>">
                <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']); ?>">
                <div class="cr-grid">
                    <div><label><?php p($l->t('Naam *')); ?></label><input type="text" name="display_name" required></div>
                    <div><label><?php p($l->t('Adres')); ?></label><input type="text" name="address" placeholder="Straat 1, 1234AB Plaats, Nederland"></div>
                    <div><label><?php p($l->t('Omschrijving')); ?></label><input type="text" name="description"></div>
                </div>
                <div class="cr-checkrow"><input type="checkbox" name="wheelchair_accessible" value="1" id="b_wa"><label for="b_wa"><?php p($l->t('Rolstoeltoegankelijk')); ?></label></div>
                <button type="submit"><?php p($l->t('Aanmaken')); ?></button>
            </form>
        </div>
    </details>

    <details class="cr-panel">
        <summary><?php p($l->t('Story (verdieping) aanmaken')); ?></summary>
        <div class="cr-body">
            <form method="post" action="<?php p($_['actionUrls']['story_create']); ?>">
                <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']); ?>">
                <div class="cr-grid">
                    <div><label><?php p($l->t('Building *')); ?></label>
                        <select name="building_id" required><option value="">- <?php p($l->t('kies')); ?> -</option><?php cr_options($_['buildings'], 'ID', 'Name'); ?></select>
                    </div>
                    <div><label><?php p($l->t('Naam *')); ?></label><input type="text" name="display_name" placeholder="begane grond" required></div>
                </div>
                <button type="submit"><?php p($l->t('Aanmaken')); ?></button>
            </form>
        </div>
    </details>

    <details class="cr-panel">
        <summary><?php p($l->t('Room aanmaken')); ?></summary>
        <div class="cr-body">
            <form method="post" action="<?php p($_['actionUrls']['room_create']); ?>">
                <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']); ?>">
                <div class="cr-grid">
                    <div><label><?php p($l->t('Story *')); ?></label>
                        <select name="story_id" required><option value="">- <?php p($l->t('kies')); ?> -</option><?php cr_options($_['stories'], 'ID', 'Display Name'); ?></select>
                    </div>
                    <div><label>UID *</label><input type="text" name="uid" placeholder="berlin-meeting-1" required></div>
                    <div><label><?php p($l->t('Naam *')); ?></label><input type="text" name="display_name" required></div>
                    <div><label>E-mail *</label><input type="email" name="email" required></div>
                    <div><label>Room type *</label><input type="text" name="room_type" placeholder="Meeting room" required></div>
                    <div><label><?php p($l->t('Contactpersoon')); ?></label><input type="text" name="contact_person"></div>
                    <div><label><?php p($l->t('Capaciteit')); ?></label><input type="number" name="capacity" min="0"></div>
                    <div><label><?php p($l->t('Kamernummer')); ?></label><input type="text" name="room_number"></div>
                </div>
                <div class="cr-checks">
                    <div class="cr-checkrow"><input type="checkbox" name="has_phone" value="1" id="r_phone"><label for="r_phone"><?php p($l->t('Telefoon')); ?></label></div>
                    <div class="cr-checkrow"><input type="checkbox" name="has_video_conferencing" value="1" id="r_vc"><label for="r_vc"><?php p($l->t('Videoconferencing')); ?></label></div>
                    <div class="cr-checkrow"><input type="checkbox" name="has_tv" value="1" id="r_tv"><label for="r_tv">TV</label></div>
                    <div class="cr-checkrow"><input type="checkbox" name="has_projector" value="1" id="r_proj"><label for="r_proj"><?php p($l->t('Projector')); ?></label></div>
                    <div class="cr-checkrow"><input type="checkbox" name="has_whiteboard" value="1" id="r_wb"><label for="r_wb"><?php p($l->t('Whiteboard')); ?></label></div>
                    <div class="cr-checkrow"><input type="checkbox" name="wheelchair_accessible" value="1" id="r_wa"><label for="r_wa"><?php p($l->t('Rolstoeltoegankelijk')); ?></label></div>
                </div>
                <button type="submit"><?php p($l->t('Aanmaken')); ?></button>
            </form>
        </div>
    </details>

    <details class="cr-panel">
        <summary><?php p($l->t('Vehicle aanmaken')); ?></summary>
        <div class="cr-body">
            <form method="post" action="<?php p($_['actionUrls']['vehicle_create']); ?>">
                <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']); ?>">
                <div class="cr-grid">
                    <div><label><?php p($l->t('Building *')); ?></label>
                        <select name="building_id" required><option value="">- <?php p($l->t('kies')); ?> -</option><?php cr_options($_['buildings'], 'ID', 'Name'); ?></select>
                    </div>
                    <div><label>UID *</label><input type="text" name="uid" required></div>
                    <div><label><?php p($l->t('Naam *')); ?></label><input type="text" name="display_name" required></div>
                    <div><label>E-mail *</label><input type="email" name="email" required></div>
                    <div><label>Vehicle type *</label><input type="text" name="vehicle_type" required></div>
                    <div><label><?php p($l->t('Merk *')); ?></label><input type="text" name="vehicle_make" required></div>
                    <div><label><?php p($l->t('Model *')); ?></label><input type="text" name="vehicle_model" required></div>
                    <div><label><?php p($l->t('Contactpersoon')); ?></label><input type="text" name="contact_person"></div>
                    <div><label><?php p($l->t('Actieradius (km)')); ?></label><input type="number" name="range" min="0"></div>
                    <div><label><?php p($l->t('Zitplaatsen')); ?></label><input type="number" name="seating_capacity" min="0"></div>
                </div>
                <div class="cr-checkrow"><input type="checkbox" name="is_electric" value="1" id="v_el"><label for="v_el"><?php p($l->t('Elektrisch')); ?></label></div>
                <button type="submit"><?php p($l->t('Aanmaken')); ?></button>
            </form>
        </div>
    </details>

    <details class="cr-panel">
        <summary><?php p($l->t('Resource (generiek) aanmaken')); ?></summary>
        <div class="cr-body">
            <form method="post" action="<?php p($_['actionUrls']['resource_create']); ?>">
                <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']); ?>">
                <div class="cr-grid">
                    <div><label><?php p($l->t('Building *')); ?></label>
                        <select name="building_id" required><option value="">- <?php p($l->t('kies')); ?> -</option><?php cr_options($_['buildings'], 'ID', 'Name'); ?></select>
                    </div>
                    <div><label>UID *</label><input type="text" name="uid" required></div>
                    <div><label><?php p($l->t('Naam *')); ?></label><input type="text" name="display_name" required></div>
                    <div><label>E-mail *</label><input type="email" name="email" required></div>
                    <div><label>Resource type *</label><input type="text" name="resource_type" required></div>
                    <div><label><?php p($l->t('Contactpersoon')); ?></label><input type="text" name="contact_person"></div>
                </div>
                <button type="submit"><?php p($l->t('Aanmaken')); ?></button>
            </form>
        </div>
    </details>

    <details class="cr-panel">
        <summary><?php p($l->t('Restricted-vlag zetten')); ?></summary>
        <div class="cr-body">
            <form method="post" action="<?php p($_['actionUrls']['restrict']); ?>">
                <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']); ?>">
                <div class="cr-grid">
                    <div><label>Type *</label>
                        <select name="entity_type" required>
                            <option value="room">room</option>
                            <option value="vehicle">vehicle</option>
                            <option value="resource">resource</option>
                        </select>
                    </div>
                    <div><label>ID *</label><input type="number" name="entity_id" min="1" required></div>
                </div>
                <div class="cr-checkrow"><input type="checkbox" name="restricted" value="1" id="restr"><label for="restr">Restricted</label></div>
                <button type="submit"><?php p($l->t('Toepassen')); ?></button>
            </form>
        </div>
    </details>

    <details class="cr-panel">
        <summary><?php p($l->t('Restriction aanmaken (groep koppelen)')); ?></summary>
        <div class="cr-body">
            <form method="post" action="<?php p($_['actionUrls']['restriction_create']); ?>">
                <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']); ?>">
                <div class="cr-grid">
                    <div><label>Type *</label>
                        <select name="entity_type" required>
                            <option value="room">room</option>
                            <option value="vehicle">vehicle</option>
                            <option value="resource">resource</option>
                        </select>
                    </div>
                    <div><label>ID *</label><input type="number" name="entity_id" min="1" required></div>
                    <div><label><?php p($l->t('Groep-ID *')); ?></label><input type="text" name="group_id" required></div>
                </div>
                <button type="submit"><?php p($l->t('Aanmaken')); ?></button>
            </form>
        </div>
    </details>

    <details class="cr-panel">
        <summary><?php p($l->t('Verwijderen')); ?></summary>
        <div class="cr-body">
            <form method="post" action="<?php p($_['actionUrls']['delete']); ?>" class="cr-delete-form">
                <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']); ?>">
                <div class="cr-grid">
                    <div><label>Type *</label>
                        <select name="type" required>
                            <option value="building">building</option>
                            <option value="story">story</option>
                            <option value="room">room</option>
                            <option value="vehicle">vehicle</option>
                            <option value="resource">resource</option>
                            <option value="restriction">restriction</option>
                        </select>
                    </div>
                    <div><label>ID *</label><input type="number" name="resource_id" min="1" required></div>
                </div>
                <button type="submit" class="cr-danger"><?php p($l->t('Verwijderen')); ?></button>
            </form>
        </div>
    </details>
</div>
